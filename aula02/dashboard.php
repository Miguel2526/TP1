<?php
// 1. Configurações de Sessão
ini_set('session.gc_maxlifetime', 600);
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['id'];
$user_nome = $_SESSION['nome'];
$perfil_user = $_SESSION['perfil']; 

// Gestão de Inatividade
if (isset($_SESSION['ultimo_clique'])) {
    $inatividade = time() - $_SESSION['ultimo_clique'];
    if ($inatividade > 600) {
        session_unset();
        session_destroy();
        header("Location: login.php?erro=sessao_expirada");
        exit();
    }
}
$_SESSION['ultimo_clique'] = time();

// 2. Inicializar variáveis
$fichas_pendentes = null;
$matriculas_pendentes = null;
$dados_ficha = null;
$minhas_matriculas = null;
$minhas_notas = null;

// Lógica do GESTOR
if ($perfil_user == 'Gestor') {
    if (isset($_GET['del_curso'])) {
        $id = intval($_GET['del_curso']);
        $conn->query("DELETE FROM cursos WHERE Id_cursos = $id");
        header("Location: dashboard.php");
        exit;
    }
    if (isset($_POST['add_curso'])) {
        $nome = trim($_POST['nome']);
        if ($nome != '') {
            $stmt = $conn->prepare("INSERT INTO cursos (Nome) VALUES (?)");
            $stmt->bind_param("s", $nome);
            $stmt->execute();
        }
        header("Location: dashboard.php");
        exit;
    }
    $fichas_pendentes = $conn->query("SELECT f.*, u.nome FROM ficha_aluno f JOIN usuarios u ON f.utilizador_id = u.id WHERE f.estado = 'Submetida'");
}

// Lógica do FUNCIONÁRIO
if ($perfil_user == 'Funcionário') {
    $matriculas_pendentes = $conn->query("SELECT m.id, u.nome as aluno, c.Nome as curso, m.data_submissao 
                                        FROM matriculas m 
                                        JOIN usuarios u ON m.utilizador_id = u.id 
                                        JOIN cursos c ON m.curso_id = c.Id_cursos 
                                        WHERE m.estado = 'Pendente'");
}

// Lógica do ALUNO
// Lógica do ALUNO
if ($perfil_user == 'Aluno') {
    // Adicionamos as novas colunas na consulta para o PHP as reconhecer
    $res_ficha = $conn->query("SELECT estado, foto_path, observacoes, data_decisao, validado_por FROM ficha_aluno WHERE utilizador_id = $user_id ORDER BY id DESC LIMIT 1");
    if ($res_ficha) { $dados_ficha = $res_ficha->fetch_assoc(); }
    
    $minhas_matriculas = $conn->query("SELECT m.estado, c.Nome as curso FROM matriculas m JOIN cursos c ON m.curso_id = c.Id_cursos WHERE m.utilizador_id = $user_id");
    
    $minhas_notas = $conn->query("SELECT n.nota, c.Nome as curso, n.data_lancamento 
                                  FROM notas n 
                                  JOIN cursos c ON n.curso_id = c.Id_cursos 
                                  WHERE n.utilizador_id = $user_id 
                                  ORDER BY n.data_lancamento DESC");
}

// Consultas Comuns
$cursos = $conn->query("SELECT * FROM cursos ORDER BY Nome");
// Altera a tua linha do $plano para esta:
$plano = $conn->query("SELECT c.Nome as nome_curso, d.nome_disciplina 
                       FROM cursos c 
                       LEFT JOIN plano_estudos p ON c.Id_cursos = p.cursos 
                       LEFT JOIN disciplinas d ON p.disciplinas = d.Id_disciplina 
                       ORDER BY c.Nome");?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Portal Académico IPCA</title>
    <link rel="stylesheet" href="estilos.css?v=<?= time(); ?>">
    <style>
        /* Correção para evitar sobreposição visual e melhorar impressão */
        .lista-notas { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
        .nota-card { background: #fff; border: 1px solid #eee; padding: 12px; border-radius: 8px; color: #333; }
        
        @media print {
            body * { visibility: hidden; }
            .printable, .printable * { visibility: visible; }
            .printable { position: absolute; left: 0; top: 0; width: 100%; }
            .btn, button { display: none !important; }
        }
    </style>
</head>
<body>

<div class="header-box">
    <h1>Portal Académico IPCA</h1>
</div>

<div class="user-bar">
    <span>Olá, <strong><?= htmlspecialchars($user_nome) ?>!</strong> (Perfil: <?= $perfil_user ?>)</span>
    <a href="logout.php">Sair</a>
</div>

<div class="container">
    <div class="main-content">
        <div class="dashboard-grid">
            <div class="box">
                <h2>Cursos</h2>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Nome do Curso</th><?php if ($perfil_user == 'Gestor'): ?><th>Ações</th><?php endif; ?></tr>
                    </thead>
                    <tbody>
                    <?php while ($c = $cursos->fetch_assoc()): ?>
                        <tr>
                            <td><?= $c['Id_cursos'] ?></td>
                            <td><?= htmlspecialchars($c['Nome']) ?></td>
                            <?php if ($perfil_user == 'Gestor'): ?>
                            <td>
                                <a href="editar_curso.php?id=<?= $c['Id_cursos'] ?>">✏️</a>
                                <a href="?del_curso=<?= $c['Id_cursos'] ?>" onclick="return confirm('Tem certeza?')">🗑️</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <h2>Plano de Estudos</h2>
                <table class="tabela-plano">
                    <thead><tr><th>Curso</th><th>Disciplina</th></tr></thead>
                    <tbody>
                    <?php if($plano): while ($p = $plano->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nome_curso']) ?></td>
                            <td><?= htmlspecialchars($p['nome_disciplina']) ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 

<div class="sidebar">
        <?php if ($perfil_user == 'Gestor'): ?>
            <div class="box">
                <h3>Novo Curso</h3>
                <form action="dashboard.php" method="POST">
                    <input type="text" name="nome" placeholder="Nome do curso" required>
                    <button type="submit" name="add_curso" class="btn">Adicionar</button>
                </form>
            </div>

            <div class="box" style="margin-top:20px;">
                <h3>Validar Fichas</h3>
                <a href="validar_fichas.php" class="btn">Ver Pendentes</a>
            </div>

            <div class="box" style="margin-top:20px;">
                <h3>🔐 Segurança</h3>
                <p style="font-size: 11px; color: #666; margin-bottom: 10px;">Gestão de contas.</p>
                <a href="gerir_usuarios.php" class="btn" style="background: #ef4444; color: white;">Gerir Passwords</a>
            </div>

            <div class="box" style="margin-top:20px; border-top: 4px solid #00a4aa;">
                <h3>📖 Gestão do Plano</h3>
                <p style="font-size: 11px; color: #666; margin-bottom: 10px;">Vincule novas disciplinas aos cursos.</p>
                
                <form action="processar_plano.php" method="POST">
                    <select name="curso_id" required style="width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd;">
                        <option value="">-- Selecione o Curso --</option>
                        <?php 
                        $cursos->data_seek(0); 
                        while($c = $cursos->fetch_assoc()): 
                        ?>
                            <option value="<?= $c['Id_cursos'] ?>"><?= htmlspecialchars($c['Nome']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <input type="text" name="nome_disciplina" placeholder="Nome da Disciplina" required 
                           style="width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; box-sizing: border-box;">
                    
                    <button type="submit" class="btn" style="width: 100%; background: #00a4aa;">GRAVAR NO PLANO</button>
                </form>
            </div>
            
        <?php endif; ?>

        <?php if ($perfil_user == 'Funcionário'): ?>
            <div class="box">
                <h3>📊 Avaliações</h3>
                <a href="gerar_pauta.php" class="btn" style="margin-bottom: 5px;">Lançar Notas (Gerar Pauta)</a>
                <a href="historico_pautas.php" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-main);">Consultar Histórico</a>
            </div>
            
            <div class="box" style="margin-top:20px;">
                <h3>✅ Validar Matrículas</h3>
                <?php if ($matriculas_pendentes && $matriculas_pendentes->num_rows > 0): ?>
                    <?php while($m = $matriculas_pendentes->fetch_assoc()): ?>
                        <div style="border-bottom:1px solid #ddd; padding:10px 0;">
                            <strong><?= htmlspecialchars($m['aluno']) ?></strong><br>
                            <small><?= htmlspecialchars($m['curso']) ?></small><br>
                            <a href="validar_matricula.php?id=<?= $m['id'] ?>" class="btn" style="background: var(--primary); padding: 5px 10px; margin-top: 5px; display: inline-block;">Avaliar Pedido</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p><small>Sem pedidos pendentes.</small></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($perfil_user == 'Aluno'): ?>
            <div class="printable">
                <div class="box" style="background: linear-gradient(135deg, #00a4aa 0%, #005f63 100%); color: white; border: none; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <?php if (!empty($dados_ficha['foto_path']) && file_exists($dados_ficha['foto_path'])): ?>
                            <img src="<?= $dados_ficha['foto_path'] ?>?v=<?= time(); ?>" style="width: 70px; height: 70px; border-radius: 10px; object-fit: cover; border: 2px solid white;">
                        <?php else: ?>
                            <div style="font-size: 40px; background: rgba(255,255,255,0.2); padding: 10px; border-radius: 10px;">👤</div>
                        <?php endif; ?>
                        <div>
                            <h3 style="margin:0; color: white; font-size: 18px;"><?= strtoupper($user_nome) ?></h3>
                            <p style="margin:0; font-size: 11px; opacity: 0.9;">ALUNO ATIVO - IPCA</p>
                            <p style="margin:5px 0 0 0; font-size: 13px; font-weight: bold; background: rgba(0,0,0,0.2); display: inline-block; padding: 2px 8px; border-radius: 4px;">ID: #<?= $user_id ?></p>
                        </div>
                    </div>
                </div>

                <div class="box" style="margin-top:20px; border-top: 4px solid #00a4aa;">
                    <h3>🎓 As Minhas Notas</h3>
                    <div class="lista-notas">
                        <?php if ($minhas_notas && $minhas_notas->num_rows > 0): ?>
                            <?php while($n = $minhas_notas->fetch_assoc()): ?>
                                <div class="nota-card">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span><?= htmlspecialchars($n['curso']) ?></span>
                                        <strong style="color: #00a4aa; font-size: 1.2em;"><?= number_format($n['nota'], 1) ?></strong>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            <button onclick="window.print();" class="btn" style="background: #333; margin-top: 15px; width: 100%;">🖨️ IMPRIMIR PAUTA</button>
                        <?php else: ?>
                            <p><small>Ainda não tens notas lançadas.</small></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="box" style="margin-top: 20px;">
                <h3>📊 Estado da Ficha</h3>
                <?php if ($dados_ficha && $dados_ficha['estado'] == 'Rejeitada'): ?>
                    <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin-top: 10px; border-left: 5px solid #ef5350;">
                        <strong>⚠️ Ficha Rejeitada:</strong><br>
                        <small><?= htmlspecialchars($dados_ficha['observacoes']) ?></small>
                        <br><br>
                        <a href="editar_ficha.php" class="btn" style="background: #ef5350; font-size: 12px;">CORRIGIR DADOS</a>
                    </div>
                <?php elseif ($dados_ficha && $dados_ficha['estado'] == 'Aprovada'): ?>
                    <div style="background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 8px; margin-top: 10px;">
                        <strong>✅ Validado por:</strong> Funcionário #<?= $dados_ficha['validado_por'] ?> 
                        em <?= date('d/m/Y', strtotime($dados_ficha['data_decisao'])) ?>
                    </div>
                <?php endif; ?>
                <?php 
                $quer_editar = isset($_GET['editar_ficha']);
                $mostrar_form = (!$dados_ficha || $dados_ficha['estado'] == 'Rejeitada' || $quer_editar);
                if ($dados_ficha && $dados_ficha['estado'] == 'Submetida' && !$quer_editar): ?>
                    <p>Ficha: <span style="color: orange; font-weight: bold;">Submetida</span></p>
                    <a href="?editar_ficha=1" class="btn" style="font-size: 10px;">EDITAR DADOS</a>
                <?php elseif ($mostrar_form): ?>
                    <form action="processar_ficha.php" method="POST" enctype="multipart/form-data">
                        <select name="curso_id" required style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid var(--border-light); background: rgba(0, 0, 0, 0.2); color: white; margin-bottom: 15px; box-sizing: border-box; font-family: 'Inter', sans-serif;">
                            <option value="">-- Selecione o Curso Pretendido --</option>
                            <?php $cursos->data_seek(0); while($c = $cursos->fetch_assoc()): ?>
                                <option value="<?= $c['Id_cursos'] ?>"><?= htmlspecialchars($c['Nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="text" name="morada" placeholder="Morada" required>
                        <input type="text" name="contacto" placeholder="Contacto Telefónico" required>
                        <input type="file" name="foto" accept="image/*" required style="margin-top:5px;">
                        <button type="submit" class="btn">Submeter Ficha</button>
                    </form>
                <?php else: ?>
                    <p>Ficha: <b style="color: #00a4aa;"><?= htmlspecialchars($dados_ficha['estado']) ?></b></p>
                <?php endif; ?>
            </div>

            <?php if ($dados_ficha && $dados_ficha['estado'] == 'Aprovada'): ?>
                <div class="box" style="margin-top:20px;">
                    <h3>📝 Inscrição</h3>
                    <form action="processar_matricula.php" method="POST">
                        <select name="curso_id" required>
                            <?php $cursos->data_seek(0); while($c = $cursos->fetch_assoc()): ?>
                                <option value="<?= $c['Id_cursos'] ?>"><?= htmlspecialchars($c['Nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="btn" style="margin-top: 10px;">Pedir Matrícula</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div> 
</div>

</body>
</html>
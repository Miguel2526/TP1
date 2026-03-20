<?php
session_start();
require_once 'config.php';

if ($_SESSION['perfil'] != 'Gestor') { header("Location: dashboard.php"); exit; }

$sucesso = '';
$erro = '';

// Lógica para Adicionar UC
if (isset($_POST['add_uc'])) {
    $nome = $_POST['nome_uc'];
    if (!empty($nome)) {
        $stmt = $conn->prepare("INSERT INTO disciplinas (nome_disciplina) VALUES (?)");
        $stmt->bind_param("s", $nome);
        if ($stmt->execute()) {
            $sucesso = "Unidade Curricular crida com sucesso.";
        } else {
            $erro = "Erro ao criar UC.";
        }
    }
}

// Lógica para Remover UC
if (isset($_GET['del_uc'])) {
    $id = intval($_GET['del_uc']);
    // Ignorar erros caso existam dependências noutras tabelas como pautas/notas
    if ($conn->query("DELETE FROM disciplinas WHERE Id_disciplina = $id")) {
        $sucesso = "UC removida do sistema.";
    } else {
        $erro = "Não pode remover esta UC pois já está a ser utilizada noutros recursos.";
    }
}

// Lógica para Associar UC ao Curso (Plano de Estudos)
if (isset($_POST['associar_plano'])) {
    $curso = intval($_POST['curso_id']);
    $uc = intval($_POST['uc_id']);
    $semestre = intval($_POST['semestre']);
    
    $stmt = $conn->prepare("INSERT INTO plano_estudos (cursos, disciplinas, semestre) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $curso, $uc, $semestre);
    if($stmt->execute()) {
        $sucesso = "UC associada ao curso com sucesso!";
    } else {
        $erro = "Erro: Esta UC já existe neste curso neste semestre, ou erro de base de dados.";
    }
}

// Lógica para Remover do Plano
if (isset($_GET['del_plano'])) {
    $id = intval($_GET['del_plano']);
    $conn->query("DELETE FROM plano_estudos WHERE id = $id");
    $sucesso = "Associação removida do Plano de Estudos.";
}

$cursos = $conn->query("SELECT * FROM cursos");
$ucs = $conn->query("SELECT * FROM disciplinas ORDER BY nome_disciplina");
$plano_estudos = $conn->query("SELECT p.id, c.Nome as nome_curso, d.nome_disciplina, p.semestre FROM plano_estudos p JOIN cursos c ON p.cursos = c.Id_cursos JOIN disciplinas d ON p.disciplinas = d.Id_disciplina ORDER BY c.Nome, p.semestre");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Pedagógica - IPCA</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .split-box { display: flex; flex-direction: column; gap: 20px; }
        .danger-msg { background: rgba(239, 68, 68, 0.2); border-left: 4px solid #ef4444; color: #fca5a5; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; }
        .success-msg { background: rgba(16, 185, 129, 0.2); border-left: 4px solid #10b981; color: #6ee7b7; padding: 12px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="header-box">
        <h1>⚙️ Gestão Pedagógica</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
    </div>

    <div class="container">
        
        <div class="main-content">
            <div class="box">
                <h2>1. Gestão de Unidades Curriculares (UC)</h2>
                <?php if($sucesso): ?><div class="success-msg"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
                <?php if($erro): ?><div class="danger-msg"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

                <form method="POST" style="display:flex; gap:10px; margin-bottom: 20px;">
                    <input type="text" name="nome_uc" placeholder="Nova Disciplina (Ex: Programação Web)" required style="flex:1;">
                    <button type="submit" name="add_uc" class="btn">Criar UC</button>
                </form>

                <div style="max-height: 400px; overflow-y: auto;">
                    <table>
                        <thead><tr><th>ID</th><th>Unidade Curricular</th><th>Apagar</th></tr></thead>
                        <tbody>
                            <?php $ucs->data_seek(0); while($u = $ucs->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $u['Id_disciplina'] ?></td>
                                    <td><?= htmlspecialchars($u['nome_disciplina']) ?></td>
                                    <td>
                                        <a href="?del_uc=<?= $u['Id_disciplina'] ?>" onclick="return confirm('Apagar definitivamente esta UC?');" style="color:red; text-decoration:none;">🗑️</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box" style="margin-top: 20px;">
                <h2>3. Lista Global: Plano de Estudos</h2>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table>
                        <thead><tr><th>Curso</th><th>UC</th><th>Semestre</th><th>Remover</th></tr></thead>
                        <tbody>
                            <?php if($plano_estudos): while($p = $plano_estudos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['nome_curso']) ?></td>
                                    <td><?= htmlspecialchars($p['nome_disciplina']) ?></td>
                                    <td><?= htmlspecialchars($p['semestre']) ?>º Sem.</td>
                                    <td>
                                        <a href="?del_plano=<?= $p['id'] ?>" onclick="return confirm('Remover esta UC do plano do curso?');" style="color:red; text-decoration:none;">❌</a>
                                    </td>
                                </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <div class="box">
                <h2>2. Configurar Plano de Estudos</h2>
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:15px;">Associe uma UC a um curso e defina o semestre.</p>
                
                <form method="POST">
                    <select name="curso_id" required>
                        <option value="">Selecionar Curso...</option>
                        <?php $cursos->data_seek(0); while($c = $cursos->fetch_assoc()): ?>
                            <option value="<?= $c['Id_cursos'] ?>"><?= htmlspecialchars($c['Nome']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <select name="uc_id" required>
                        <option value="">Selecionar UC...</option>
                        <?php $ucs->data_seek(0); while($u = $ucs->fetch_assoc()): ?>
                            <option value="<?= $u['Id_disciplina'] ?>"><?= htmlspecialchars($u['nome_disciplina']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    
                    <input type="number" name="semestre" placeholder="Indique o Semestre (Ex: 1 ou 2)" min="1" max="10" required 
                           style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid var(--border-light); background: rgba(0, 0, 0, 0.2); color: white; margin-bottom: 15px; box-sizing: border-box; font-family: 'Inter', sans-serif;">
                    
                    <button type="submit" name="associar_plano" class="btn" style="width: 100%; margin-top: 5px;">ADICIONAR AO CURSO</button>
                </form>
            </div>
        </div>

    </div>
</body>
</html>
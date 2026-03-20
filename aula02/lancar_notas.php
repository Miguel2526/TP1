<?php
session_start();
require_once 'config.php';

// Só funcionários entram
if ($_SESSION['perfil'] != 'Funcionário') {
    header("Location: dashboard.php");
    exit();
}

$uc_id = isset($_GET['uc_id']) ? intval($_GET['uc_id']) : 0;
$epoca = $_GET['epoca'] ?? '';
$ano_letivo = $_GET['ano_letivo'] ?? '';

// Gravar a nota quando o botão for clicado (LÓGICA ANTES DO HTML)
if (isset($_POST['gravar_nota'])) {
    $aluno_id = intval($_POST['aluno_id']);
    $curso_id = intval($_POST['curso_id']);
    $post_uc_id = intval($_POST['uc_id']);
    $post_epoca = $_POST['epoca'];
    $post_ano_letivo = $_POST['ano_letivo'];
    $nota = floatval($_POST['nota']);
    $func_id = $_SESSION['id'];

    $stmt = $conn->prepare("INSERT INTO notas (utilizador_id, curso_id, nota, lancado_por, uc_id, epoca, ano_letivo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidiiss", $aluno_id, $curso_id, $nota, $func_id, $post_uc_id, $post_epoca, $post_ano_letivo);
    
    if ($stmt->execute()) {
        header("Location: lancar_notas.php?msg=sucesso&uc_id=$post_uc_id&epoca=" . urlencode($post_epoca) . "&ano_letivo=" . urlencode($post_ano_letivo));
        exit();
    } else {
        die("Erro ao gravar: " . $stmt->error);
    }
}

// Obter nome da UC apenas se um ID estiver definido
$uc_nome = "Desconhecida";
if ($uc_id > 0) {
    $r_uc = $conn->query("SELECT nome_disciplina FROM disciplinas WHERE Id_disciplina = $uc_id");
    if ($r_uc && $r_uc->num_rows > 0) {
        $uc_nome = $r_uc->fetch_assoc()['nome_disciplina'];
    }
}

// Alunos aprovados que estão matriculados em cursos que contêm esta UC no seu plano de estudos
$query = "SELECT u.id as aluno_id, u.nome, c.Nome as curso_nome, c.Id_cursos 
          FROM matriculas m
          JOIN usuarios u ON m.utilizador_id = u.id
          JOIN cursos c ON m.curso_id = c.Id_cursos
          WHERE m.estado = 'Aprovada' AND u.perfil = 'Aluno'";

if ($uc_id > 0) {
    $query .= " AND c.Id_cursos IN (SELECT cursos FROM plano_estudos WHERE disciplinas = $uc_id)";
}

$alunos = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lançar Notas - IPCA</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="header-box">
        <h1>📊 Lançar Notas Finais</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
        <?php if ($uc_id > 0): ?>
            <span style="float:right; opacity: 0.8; font-size: 0.9rem;">
                UC: <strong><?= htmlspecialchars($uc_nome) ?></strong> | Ano: <?= htmlspecialchars($ano_letivo) ?> | Época: <?= htmlspecialchars($epoca) ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="main-content">
            <div class="box">
                <h2>Alunos Elegíveis <?php if($uc_id == 0) echo "<span style='color:red;'>(Selecione uma Pauta primeiro no Dashboard!)</span>"; ?></h2>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso'): ?>
                    <div style="background: rgba(16, 185, 129, 0.2); border-left: 4px solid #10b981; color: #6ee7b7; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
                        ✅ Nota lançada com sucesso!
                    </div>
                <?php endif; ?>

                <?php if ($alunos && $alunos->num_rows > 0 && $uc_id > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Curso</th>
                                <th>Nota (0-20)</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($a = $alunos->fetch_assoc()): ?>
                            <tr>
                                <!-- Dita a Tabela com display row para englobar forms perfeitamente (requer html solto) -->
                                <form method="POST" style="display: table-row;">
                                    <td><?= htmlspecialchars($a['nome']) ?></td>
                                    <td><?= htmlspecialchars($a['curso_nome']) ?></td>
                                    <td>
                                        <input type="number" name="nota" min="0" max="20" step="0.1" required style="width: 80px; margin-bottom: 0;">
                                        <input type="hidden" name="aluno_id" value="<?= $a['aluno_id'] ?>">
                                        <input type="hidden" name="curso_id" value="<?= $a['Id_cursos'] ?>">
                                        
                                        <!-- Passar dados da pauta -->
                                        <input type="hidden" name="uc_id" value="<?= $uc_id ?>">
                                        <input type="hidden" name="epoca" value="<?= htmlspecialchars($epoca) ?>">
                                        <input type="hidden" name="ano_letivo" value="<?= htmlspecialchars($ano_letivo) ?>">
                                    </td>
                                    <td><button type="submit" name="gravar_nota" class="btn">GRAVAR</button></td>
                                </form>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-muted);">
                        <?php if ($uc_id == 0) { echo "Aceda ao Dashboard -> Avaliações -> Gerar Pauta para criar uma lista de pauta."; } else { echo "Não existem alunos matriculados nos cursos que contém esta Unidade Curricular no plano de estudos."; } ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
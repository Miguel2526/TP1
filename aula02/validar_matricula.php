<?php
session_start();
require_once 'config.php';

// Segurança: Só Funcionário ou Gestor entram aqui
if (!isset($_SESSION['perfil']) || ($_SESSION['perfil'] != 'Funcionário' && $_SESSION['perfil'] != 'Gestor')) {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_GET['id']) && !isset($_POST['id_matricula'])) {
    header("Location: dashboard.php");
    exit();
}

// Processar a decisão (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decisao'])) {
    $id_matricula = intval($_POST['id_matricula']);
    $nova_acao = $_POST['decisao'];
    $obs = trim($_POST['observacoes']);
    $id_validador = $_SESSION['id'];

    $sql = "UPDATE matriculas SET estado = ?, validado_por = ?, data_decisao = NOW(), observacoes = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $nova_acao, $id_validador, $obs, $id_matricula);

    if ($stmt->execute()) {
        header("Location: dashboard.php?status=ok");
        exit();
    } else {
        $erro = "Erro ao atualizar: " . $conn->error;
    }
}

// Mostrar a form de validação (GET)
$id_matricula = intval($_GET['id'] ?? $_POST['id_matricula']);
$sql = "SELECT m.*, u.nome as aluno, c.Nome as curso_nome 
        FROM matriculas m 
        JOIN usuarios u ON m.utilizador_id = u.id 
        JOIN cursos c ON m.curso_id = c.Id_cursos
        WHERE m.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_matricula);
$stmt->execute();
$mat = $stmt->get_result()->fetch_assoc();

if (!$mat) {
    echo "Matrícula não encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Matrícula - IPCA</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <div class="header-box">
        <h1>✅ Avaliar Pedido de Matrícula</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
    </div>

    <div class="container" style="max-width: 600px; margin: 0 auto; display: block;">
        <div class="box">
            <?php if (isset($erro)): ?>
                <div style="color:red; margin-bottom: 20px;"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <h3 style="color: var(--primary);">Resumo do Pedido #<?= $mat['id'] ?></h3>
            <p><strong>Aluno:</strong> <?= htmlspecialchars($mat['aluno']) ?></p>
            <p><strong>Curso Pretendido:</strong> <?= htmlspecialchars($mat['curso_nome']) ?></p>
            <p><strong>Data de Submissão:</strong> <?= date('d/m/Y H:i', strtotime($mat['data_submissao'] ?? $mat['data_pedido'])) ?></p>

            <hr style="border:0; border-top:1px solid var(--border-light); margin:20px 0;">

            <form method="POST">
                <input type="hidden" name="id_matricula" value="<?= $mat['id'] ?>">
                
                <label style="display:block; margin-bottom:8px; font-weight:600; color:var(--text-muted); font-size:0.9rem;">
                    Observações (Justifique a aprovação ou rejeição):
                </label>
                <textarea name="observacoes" placeholder="Escreva as observações aqui..." required 
                          style="width: 100%; height: 100px; padding: 12px; border-radius: 8px; border: 1px solid var(--border-light); background: rgba(0, 0, 0, 0.2); color: white; box-sizing: border-box; font-family: 'Inter', sans-serif; resize: vertical; margin-bottom: 20px;"></textarea>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" name="decisao" value="Aprovada" class="btn" style="flex: 1; background: linear-gradient(135deg, var(--success), #059669);">✅ Aprovar</button>
                    <button type="submit" name="decisao" value="Rejeitada" class="btn" style="flex: 1; background: linear-gradient(135deg, var(--danger), #b91c1c);">❌ Rejeitar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
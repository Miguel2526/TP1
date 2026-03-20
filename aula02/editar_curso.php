<?php
session_start();
require_once 'config.php';

// Proteção básica
if (!isset($_SESSION['id']) || $_SESSION['perfil'] !== 'Gestor') {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM cursos WHERE Id_cursos = $id");
$curso = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome = trim($_POST['nome_curso']);
    $conn->query("UPDATE cursos SET Nome = '$novo_nome' WHERE Id_cursos = $id");
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="header-box">
        <h1>✏️ Editar Curso</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
    </div>

    <div class="container">
        <div class="main-content">
            <div class="box">
                <h2>Editar Nome do Curso</h2>
                <form method="POST">
                    <input type="text" name="nome_curso" value="<?= htmlspecialchars($curso['Nome']) ?>" required>
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button type="submit" class="btn">Atualizar Curso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id']) || $_SESSION['perfil'] !== 'Aluno') {
    die("Acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $curso_id = intval($_POST['curso_id']);

    // Verifica se já existe um pedido para este curso
    $check = $conn->prepare("SELECT id FROM matriculas WHERE utilizador_id = ? AND curso_id = ?");
    $check->bind_param("ii", $user_id, $curso_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        echo "<script>alert('Já solicitou matrícula neste curso!'); window.location.href='dashboard.php';</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO matriculas (utilizador_id, curso_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $curso_id);
        $stmt->execute();
        echo "<script>alert('Pedido de matrícula enviado!'); window.location.href='dashboard.php';</script>";
    }
}
?>
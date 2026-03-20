<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // 1. Procurar o utilizador pelo email
    $stmt = $conn->prepare("SELECT id, nome, perfil, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($user = $resultado->fetch_assoc()) {
        // 2. Verificar a senha de forma segura com hash
        if (password_verify($senha, $user['senha'])) {
            
            // 3. GUARDAR TUDO NA SESSÃO (Isto é o que faz o Dashboard funcionar)
            $_SESSION['id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['perfil'] = $user['perfil']; // Crucial: Aluno, Funcionário ou Gestor
            $_SESSION['ultimo_clique'] = time();

            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?erro=senha_errada");
        }
    } else {
        header("Location: login.php?erro=usuario_nao_encontrado");
    }
}
?>
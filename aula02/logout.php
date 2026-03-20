<?php
// 1. Inicia a sessão para ter acesso a ela
session_start();

// 2. Remove todas as variáveis de sessão
$_SESSION = array();

// 3. Destrói o cookie da sessão no navegador (opcional, mas recomendado por segurança)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destrói a sessão no servidor
session_destroy();

// 5. Redireciona para a página de login (index.php)
header("Location: index.php");
exit();
?>
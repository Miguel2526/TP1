<?php
// Define o tempo de expiração em segundos (ex: 600 segundos = 10 minutos)
$tempo_expiracao = 600; 

if (isset($_SESSION['ultimo_clique'])) {
    // Calcula há quanto tempo o utilizador não interage
    $inatividade = time() - $_SESSION['ultimo_clique'];

    if ($inatividade > $tempo_expiracao) {
        // Se passou do tempo, destrói a sessão e expulsa o utilizador
        session_unset();
        session_destroy();
        header("Location: login.php?erro=sessao_expirada");
        exit();
    }
}

// Atualiza o carimbo de data/hora para o momento atual
$_SESSION['ultimo_clique'] = time();
// Configurações da base de dados
$host = "localhost";
$user = "root";
$pass = ""; // Por padrão no XAMPP a senha é vazia
$dbname = "ipca_gestao";

// 1. Criar a ligação
$conn = new mysqli($host, $user, $pass, $dbname);

// 2. Verificar se a ligação falhou
if ($conn->connect_error) {
    // Se falhar, mostra o erro e para a execução do site
    die("Erro de ligação: " . $conn->connect_error);
}

// 3. Definir o charset para UTF-8 (importante para acentos e cedilhas)
$conn->set_charset("utf8mb4");

// Opcional: Ativar o reporte de erros do MySQLi para ajudar no desenvolvimento
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
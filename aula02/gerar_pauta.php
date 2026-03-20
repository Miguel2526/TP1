<?php
session_start();
require_once 'config.php';

// Proteção: Só Funcionário entra
if ($_SESSION['perfil'] != 'Funcionário') {
    header("Location: dashboard.php");
    exit();
}

// 1. Listar todas as UCs disponíveis
$ucs = $conn->query("SELECT * FROM disciplinas");

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Pautas - IPCA</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-muted); font-size: 0.9rem; margin-top: 15px; }
    </style>
</head>
<body>

    <div class="header-box">
        <h1>📜 Gerar Pauta de Avaliação</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
    </div>

    <div class="container">
        <div class="main-content">
            <div class="box">
                <h2>Selecione a Unidade Curricular</h2>
                <form action="lancar_notas.php" method="GET">
                    <label>Unidade Curricular:</label>
                    <select name="uc_id" required>
                        <?php while($u = $ucs->fetch_assoc()): ?>
                            <option value="<?= $u['Id_disciplina'] ?>"><?= htmlspecialchars($u['nome_disciplina']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label>Ano Letivo:</label>
                    <input type="text" name="ano_letivo" placeholder="Ex: 2023/2024" required style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid var(--border-light); background: rgba(0, 0, 0, 0.2); color: white; margin-bottom: 0px; box-sizing: border-box; font-family: 'Inter', sans-serif;">

                    <label style="margin-top:20px;">Época de Avaliação:</label>
                    <select name="epoca">
                        <option value="Normal">Normal</option>
                        <option value="Recurso">Recurso</option>
                        <option value="Especial">Especial</option>
                    </select>

                    <div style="margin-top: 25px;">
                        <button type="submit" class="btn">ABRIR LISTA DE ALUNOS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
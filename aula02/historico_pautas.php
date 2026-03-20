<?php
session_start();
require_once 'config.php';

if ($_SESSION['perfil'] != 'Funcionário') {
    header("Location: dashboard.php");
    exit();
}

$query = "SELECT n.id, n.nota, n.ano_letivo, n.epoca, n.data_lancamento, 
                 u.nome as aluno, c.Nome as curso, d.nome_disciplina as uc_nome,
                 f.nome as funcionario
          FROM notas n
          JOIN usuarios u ON n.utilizador_id = u.id
          JOIN cursos c ON n.curso_id = c.Id_cursos
          LEFT JOIN disciplinas d ON n.uc_id = d.Id_disciplina
          LEFT JOIN usuarios f ON n.lancado_por = f.id
          ORDER BY n.data_lancamento DESC";

$notas = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Pautas - IPCA</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="header-box">
        <h1>📚 Histórico de Pautas e Notas</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
    </div>

    <div class="container" style="max-width: 1200px; display: block; margin: 0 auto;">
        <div class="box">
            <h2>Todas as Notas Registadas</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">Consulte o histórico global de avaliações lançadas no sistema.</p>

            <?php if ($notas && $notas->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Aluno</th>
                            <th>Unidade Curricular</th>
                            <th>Curso</th>
                            <th>Ano Letivo</th>
                            <th>Época</th>
                            <th>Nota Final</th>
                            <th>Validado Por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($n = $notas->fetch_assoc()): ?>
                        <tr>
                            <td style="font-size: 0.85rem; color: var(--text-muted);"><?= date('d/m/Y H:i', strtotime($n['data_lancamento'])) ?></td>
                            <td><strong><?= htmlspecialchars($n['aluno']) ?></strong></td>
                            <td><?= htmlspecialchars($n['uc_nome'] ?? 'N/D') ?></td>
                            <td style="font-size: 0.9rem;"><?= htmlspecialchars($n['curso']) ?></td>
                            <td><?= htmlspecialchars($n['ano_letivo'] ?? 'N/D') ?></td>
                            <td><?= htmlspecialchars($n['epoca'] ?? 'N/D') ?></td>
                            <td>
                                <span style="color: var(--primary); font-weight: bold; font-size: 1.1rem;"><?= number_format($n['nota'], 1) ?></span>
                            </td>
                            <td style="font-size: 0.85rem;">#<?= htmlspecialchars($n['funcionario'] ?? 'Sis') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="background: rgba(0,0,0,0.2); padding: 40px; border-radius: 8px; text-align: center;">
                    <p style="color: var(--text-muted); font-size: 1.1rem;">Ainda não existem registos de notas no sistema.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
session_start();
require_once 'config.php';

// Proteção: Apenas o Gestor pode aceder
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] != 'Gestor') {
    header("Location: dashboard.php");
    exit;
}

// Busca as fichas submetidas e o nome do aluno associado
// Nota: Confirma se a tua tabela de utilizadores se chama 'usuarios' ou 'utilizadores'
$fichas = $conn->query("SELECT f.*, u.nome FROM ficha_aluno f 
                        JOIN usuarios u ON f.utilizador_id = u.id 
                        WHERE f.estado = 'Submetida'");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Fichas - Portal Académico</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .textarea-premium {
            width: 100%; height: 70px; margin-top: 5px; padding: 12px; 
            border-radius: 8px; border: 1px solid var(--border-light); 
            background: rgba(0, 0, 0, 0.2); color: white;
            font-family: 'Inter', sans-serif; font-size: 0.95rem;
            box-sizing: border-box; transition: all 0.3s ease;
        }
        .textarea-premium:focus {
            outline: none; border-color: var(--primary);
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 0 3px rgba(0, 210, 198, 0.15);
        }
        .textarea-premium::placeholder { color: rgba(255, 255, 255, 0.3); }
        hr { border: 0; border-top: 1px solid var(--border-light); margin: 20px 0; }
    </style>
</head>
<body>

    <div class="header-box">
        <h1>📋 Fichas Aguardando Validação</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
    </div>

    <div class="container" style="max-width: 900px; margin: 0 auto; display: block;">
        
        <?php if ($fichas && $fichas->num_rows > 0): ?>
            <?php while($f = $fichas->fetch_assoc()): ?>
                <div class="box" style="margin-bottom: 20px; position: relative;">
                    
                    <img src="<?= htmlspecialchars($f['foto_path']) ?>" 
                         style="width: 130px; height: 130px; object-fit: cover; border-radius: 12px; float: right; border: 3px solid var(--primary); box-shadow: 0 10px 20px rgba(0,0,0,0.3);">

                    <div style="margin-right: 150px;">
                        <h3 style="margin-bottom: 5px; color: var(--primary);">#<?= $f['id'] ?> - <?= htmlspecialchars($f['nome']) ?></h3>
                        <p style="margin: 5px 0; font-size: 0.95rem; color: var(--text-muted);"><strong>Contacto:</strong> <?= htmlspecialchars($f['contacto']) ?></p>
                        <p style="margin: 5px 0; font-size: 0.95rem; color: var(--text-muted);"><strong>Morada:</strong> <?= htmlspecialchars($f['morada']) ?></p>
                        
                        <hr>

                        <form action="processar_ficha.php" method="POST">
                            <input type="hidden" name="id_ficha" value="<?= $f['id'] ?>">
                            
                            <label style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted);">
                                Motivo/Observações (Obrigatório):
                            </label>
                            <textarea name="observacoes" placeholder="Justifique a aprovação ou rejeição..." required class="textarea-premium"></textarea>
                            
                            <div style="display: flex; gap: 15px; margin-top: 20px;">
                                <button type="submit" name="decisao" value="Aprovada" class="btn" 
                                        style="background: linear-gradient(135deg, var(--success), #059669); flex: 1; font-size: 13px;">✅ APROVAR FICHA</button>
                                
                                <button type="submit" name="decisao" value="Rejeitada" class="btn" 
                                        style="background: linear-gradient(135deg, var(--danger), #b91c1c); flex: 1; font-size: 13px;">❌ REJEITAR FICHA</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="box" style="text-align: center; padding: 50px;">
                <h3 style="color: var(--text-muted); justify-content: center;">Não existem fichas pendentes de validação no momento.</h3>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
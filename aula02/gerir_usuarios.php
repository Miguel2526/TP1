<?php
session_start();
require_once 'config.php';

// Bloqueia se não for Gestor
if (!isset($_SESSION['id']) || $_SESSION['perfil'] !== 'Gestor') {
    header("Location: dashboard.php");
    exit();
}

$erro = '';
$sucesso = '';

// Atualizar senha se submetida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mudar_senha'])) {
    $id_user = intval($_POST['user_id']);
    $nova_senha = $_POST['nova_senha'];

    if (!empty($nova_senha)) {
        // Encriptar de forma segura antes de guardar na base de dados
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", $senha_hash, $id_user);

        if ($stmt->execute()) {
            $sucesso = "A senha foi alterada com sucesso!";
        } else {
            $erro = "Erro ao alterar a senha: " . $conn->error;
        }
    } else {
        $erro = "A senha não pode estar em branco.";
    }
}

// Criar novo utilizador se submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_usuario'])) {
    $nome = trim($_POST['novo_nome']);
    $email = trim($_POST['novo_email']);
    $senha = $_POST['nova_senha_criacao'];
    $perfil = $_POST['novo_perfil'];

    if (!empty($nome) && !empty($email) && !empty($senha) && !empty($perfil)) {
        // Verificar se email já existe
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $erro = "Já existe um utilizador com este email registado.";
        } else {
            // Guardar novo utilizador
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $email, $senha_hash, $perfil);
            
            if ($stmt->execute()) {
                $sucesso = "Utilizador '$nome' criado com sucesso!";
            } else {
                $erro = "Erro ao criar utilizador: " . $conn->error;
            }
        }
    } else {
        $erro = "Todos os campos de criação são obrigatórios.";
    }
}

// Buscar todos os utilizadores
$usuarios = $conn->query("SELECT id, nome, email, perfil FROM usuarios ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Utilizadores - IPCA Gestão</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .tabela-pass {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
        }
        .form-pass {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        .form-pass input {
            width: 180px;
            margin-bottom: 0 !important;
        }
        .form-pass button {
            white-space: nowrap;
        }
        .alerta-box {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        .alerta-success {
            background: rgba(16, 185, 129, 0.2);
            border-left: 4px solid #10b981;
            color: #6ee7b7;
        }
        .alerta-error {
            background: rgba(239, 68, 68, 0.2);
            border-left: 4px solid #ef4444;
            color: #fca5a5;
        }
        .form-novo-user {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }
        .form-novo-user input, .form-novo-user select {
            margin-bottom: 0 !important;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--border-light);
            background: rgba(0,0,0,0.2);
            color: white;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>

    <div class="header-box">
        <h1>🔐 Gerir Contas e Passwords</h1>
    </div>

    <div class="user-bar" style="margin-bottom: 20px;">
        <a href="dashboard.php" style="background: rgba(255,255,255,0.1); box-shadow: none;">← Voltar ao Dashboard</a>
    </div>

    <div class="container" style="max-width: 1100px; margin: 0 auto; display: block;">
        
        <?php if (!empty($sucesso)): ?>
            <div class="alerta-box alerta-success">✅ <?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="alerta-box alerta-error">⚠️ <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="box" style="margin-bottom: 30px;">
            <h2 style="margin-top:0;">Adicionar Novo Utilizador</h2>
            <form method="POST" class="form-novo-user">
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display:block; margin-bottom:5px;">Nome Completo</label>
                    <input type="text" name="novo_nome" placeholder="Nome" required>
                </div>
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display:block; margin-bottom:5px;">Email</label>
                    <input type="email" name="novo_email" placeholder="Email institucional" required>
                </div>
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display:block; margin-bottom:5px;">Password</label>
                    <input type="password" name="nova_senha_criacao" placeholder="Senha segura" required>
                </div>
                <div>
                    <label style="font-size: 0.85rem; color: var(--text-muted); display:block; margin-bottom:5px;">Perfil de Acesso</label>
                    <select name="novo_perfil" required>
                        <option value="Aluno">Aluno</option>
                        <option value="Funcionário">Funcionário</option>
                        <option value="Gestor">Gestor Pedagógico</option>
                    </select>
                </div>
                <div>
                    <button type="submit" name="criar_usuario" class="btn" style="height: 45px; margin-bottom: 0;">ADICIONAR</button>
                </div>
            </form>
        </div>

        <div class="box">
            <h2 style="margin-top:0;">Lista de Utilizadores</h2>

            <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                <table class="tabela-pass">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Nova Senha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['nome']) ?></strong></td>
                            <td style="color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span style="background: rgba(0,210,198,0.15); padding: 5px 10px; border-radius: 6px; font-size: 0.8rem; color: var(--primary);">
                                    <?= htmlspecialchars($u['perfil']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="form-pass">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <input type="password" name="nova_senha" placeholder="Alterar senha..." required>
                                    <button type="submit" name="mudar_senha" class="btn">MUDAR</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted);">Nenhum utilizador encontrado.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

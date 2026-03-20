<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IPCA Gestão</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00d2c6;
            --primary-hover: #00a4aa;
            --bg-gradient-1: #0f2027;
            --bg-gradient-2: #203a43;
            --bg-gradient-3: #2c5364;
            --text-main: #ffffff;
            --text-muted: #a0aec0;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex; 
            justify-content: center;
            align-items: center; 
            background: linear-gradient(-45deg, var(--bg-gradient-1), var(--bg-gradient-2), var(--bg-gradient-3), #1a365d);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: var(--text-main);
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism Card */
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            padding: 40px; 
            border-radius: 20px; 
            width: 100%;
            max-width: 400px; 
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            transform: translateY(0);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 210, 198, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.2rem;
            background: linear-gradient(to right, #00d2c6, #4facfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .login-header p {
            margin: 8px 0 0;
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 300;
        }

        .input-group {
            margin-bottom: 22px;
            position: relative;
        }

        .input-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-size: 0.85rem;
            font-weight: 600; 
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group input { 
            width: 100%; 
            padding: 14px 16px; 
            box-sizing: border-box; 
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 10px; 
            color: white;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 0 4px rgba(0, 210, 198, 0.1);
        }

        .btn-login { 
            width: 100%; 
            padding: 15px; 
            background: linear-gradient(135deg, var(--primary), #007bb5);
            color: white; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-size: 1rem; 
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 210, 198, 0.3);
            margin-top: 10px;
        }

        .btn-login:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 210, 198, 0.4);
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .credentials-hint {
            margin-top: 25px;
            padding: 15px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            border-left: 3px solid var(--primary);
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .credentials-hint strong {
            color: white;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .login-card {
                margin: 20px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h1>IPCA-Gestão</h1>
            <p>Portal Académico Exclusivo</p>
        </div>

        <form action="autenticar.php" method="POST">
            <div class="input-group">
                <label>Email de Acesso</label>
                <input type="email" name="email" required placeholder="ex: admin@ipca.pt">
            </div>

            <div class="input-group">
                <label>Senha</label>
                <input type="password" name="senha" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-login">ENTRAR</button>
        </form>

        <div class="credentials-hint">
            <strong>Acesso de Demonstração:</strong><br>
            O email pode ser admin@ipca.pt, aluno@ipca.pt ou func@ipca.pt. A senha para todos é <strong>123</strong>.
        </div>
    </div>

</body> 
</html>
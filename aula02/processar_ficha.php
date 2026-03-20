<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- LÓGICA DO GESTOR: VALIDAR FICHA ---
    if (isset($_POST['decisao'])) {
        $id_ficha = intval($_POST['id_ficha']);
        $nova_decisao = $_POST['decisao']; 
        $obs = trim($_POST['observacoes']);
        $gestor_id = $_SESSION['id']; 

        $sql = "UPDATE ficha_aluno SET 
                estado = ?, 
                validado_por = ?, 
                data_decisao = NOW(), 
                observacoes = ? 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $nova_decisao, $gestor_id, $obs, $id_ficha);
        
        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=validado");
            exit();
        } else {
            die("Erro ao validar: " . $conn->error);
        }
    }

    // --- LÓGICA DO ALUNO: SUBMETER FICHA ---
    if (isset($_SESSION['id']) && $_SESSION['perfil'] == 'Aluno' && isset($_FILES['foto'])) {
        $user_id = $_SESSION['id'];
        $morada = trim($_POST['morada'] ?? '');
        $contacto = trim($_POST['contacto'] ?? '');
        $curso_id = intval($_POST['curso_id'] ?? 0);
        
        // Criar pasta uploads se não existir
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Lógica de Upload da Foto
        $foto_temp = $_FILES['foto']['tmp_name'];
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $novo_nome_foto = 'aluno_' . $user_id . '_' . time() . '.' . $extensao;
        $caminho_final = 'uploads/' . $novo_nome_foto;

        // Validar extensão (apenas imagens)
        $extensoes_validas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extensao, $extensoes_validas)) {
            die("Erro: Formato de ficheiro não suportado. Usa apenas JPG, PNG, GIF ou WEBP.");
        }

        // Limite de tamanho: Máximo 2MB
        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            die("Erro: A imagem excede o tamanho máximo de 2MB. Por favor, redimensione-a (ex: crop/resize).");
        }

        if (move_uploaded_file($foto_temp, $caminho_final)) {
            // Inserir nova ficha
            $sql = "INSERT INTO ficha_aluno (utilizador_id, morada, contacto, foto_path, estado, curso_id) 
                    VALUES (?, ?, ?, ?, 'Submetida', ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssi", $user_id, $morada, $contacto, $caminho_final, $curso_id);
            
            if ($stmt->execute()) {
                header("Location: dashboard.php?msg=ficha_submetida");
                exit();
            } else {
                die("Erro na base de dados: " . $conn->error);
            }
        } else {
            die("Erro ao fazer o upload da foto.");
        }
    } else {
        // Redirecionar se acedido indevidamente
        header("Location: dashboard.php");
        exit();
    }
}
?>
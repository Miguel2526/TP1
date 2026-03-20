<?php
session_start();
require_once 'config.php';

// Proteção: Apenas Gestores podem aceder
if (!isset($_SESSION['id']) || $_SESSION['perfil'] !== 'Gestor') {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $curso_id = intval($_POST['curso_id']);
    $nome_disciplina = trim($_POST['nome_disciplina']);

    if ($curso_id > 0 && !empty($nome_disciplina)) {
        
        // 1. Inserir a disciplina na tabela 'disciplinas'
        $stmt1 = $conn->prepare("INSERT INTO disciplinas (nome_disciplina) VALUES (?)");
        $stmt1->bind_param("s", $nome_disciplina);
        
        if ($stmt1->execute()) {
            $novo_id_disciplina = $conn->insert_id;

            // 2. Criar o vínculo na tabela 'plano_estudos'
            $stmt2 = $conn->prepare("INSERT INTO plano_estudos (cursos, disciplinas) VALUES (?, ?)");
            $stmt2->bind_param("ii", $curso_id, $novo_id_disciplina);
            $stmt2->execute();
            
            header("Location: dashboard.php?sucesso=plano_atualizado");
        } else {
            header("Location: dashboard.php?erro=falha_ao_inserir");
        }
    }
}
exit();
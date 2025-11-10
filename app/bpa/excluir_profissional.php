<?php
session_start();



require_once "classes/db.class.php";
require_once "classes/painel.class.php";

// Validação do ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID do profissional inválido.";
    header("Location: profissionais.php");
    exit();
}

$id = intval($_GET['id']);

// Chama o método estático de exclusão
try {
    if (Painel::ExcluirProfissional($id)) {
        $_SESSION['mensagem'] = "Profissional excluído com sucesso!";
    } else {
        throw new Exception("Erro ao excluir profissional.");
    }
} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao excluir profissional: " . $e->getMessage();
}

header("Location: profissionais.php");
exit();
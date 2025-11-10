<?php
session_start();

// Validação de login
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

require_once "classes/db.class.php";
require_once "classes/painel.class.php";

// Validação do ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID do paciente inválido.";
    header("Location: pacientes.php");
    exit();
}

$id = intval($_GET['id']);

// Chama o método estático de exclusão
try {
    if (Painel::ExcluirPaciente($id)) {
        $_SESSION['mensagem'] = "Paciente excluído com sucesso!";
    } else {
        throw new Exception("Erro ao excluir paciente.");
    }
} catch (Exception $e) {
    $_SESSION['erro'] = "Erro ao excluir paciente: " . $e->getMessage();
}

header("Location: pacientes.php");
exit();
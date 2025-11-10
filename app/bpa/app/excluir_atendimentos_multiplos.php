<?php
session_start();
include_once "classes/db.class.php";
include_once "classes/painel.class.php";

// Limpa mensagens antigas
unset($_SESSION['mensagem']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['ids'])) {
    echo json_encode(['tipo' => 'erro', 'texto' => 'Requisição inválida.']);
    exit;
}

$ids = explode(',', $_POST['ids']);
$ids = array_map('intval', $ids);
$user_id = intval($_POST['usuario_id'] ?? 0);

if (empty($ids)) {
    echo json_encode(['tipo' => 'erro', 'texto' => 'Nenhum ID válido foi enviado.']);
    exit;
}

try {
    $db = DB::connect();

    $idsStr = implode(',', $ids);
    $stmt = $db->prepare("DELETE FROM atendimento WHERE id IN ($idsStr)");
    $stmt->execute();

    $_SESSION['mensagem'] = [
        'tipo' => 'sucesso',
        'texto' => count($ids) . ' atendimento(s) excluído(s) com sucesso.'
    ];

    echo json_encode([
        'tipo' => 'sucesso',
        'texto' => count($ids) . ' atendimento(s) excluído(s).'
    ]);

} catch (PDOException $e) {
    $_SESSION['mensagem'] = [
        'tipo' => 'erro',
        'texto' => 'Erro ao excluir atendimentos: ' . $e->getMessage()
    ];
    echo json_encode([
        'tipo' => 'erro',
        'texto' => 'Erro ao excluir: ' . $e->getMessage()
    ]);
}
exit;
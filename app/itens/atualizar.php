<?php
session_start();
include_once "classes/gest-item.class.php";
include_once "classes/db.class.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    $id = $dados['id'];
    $data = [
        'ds_item' => $dados['ds_item'],
        'natureza' => $dados['natureza']
    ];

    if (Item::updateItem($id, $data)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}
?>
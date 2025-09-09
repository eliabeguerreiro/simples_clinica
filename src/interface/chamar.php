<?php 
require_once __DIR__ . '/../service/tokenService.php';
$resultado = tokenService::chamarProximo();

    if($resultado['sucesso']) {
        header('Location: painel-paciente.php');
        exit();
    } else {
        header('Location: recepcao.php?erro=' . urlencode($resultado['erro']));
        exit();
    }
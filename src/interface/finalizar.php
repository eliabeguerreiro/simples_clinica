<?php
require_once __DIR__ . '/../service/tokenService.php';
$resultado = tokenService::finalizarAtendimento();

    if($resultado['sucesso']) {
        header('Location: recepcao.php?sucesso=' . urldecode("Atendimento ao paciente {$resultado['token']} finalizado"));
        exit();
    } else {
        header('Location: recepcao.php?erro=' . urldecode($resultado['erro']));
        exit();
    }
<?php
session_start();
ob_start();

// Validar login
if (!isset($_SESSION['data_user']) || !isset($_SESSION['login_time'])) {
    $_SESSION['msg'] = 'Realize o login para acessar o painel';
    header('Location: ../');
    exit;
}

include "classes/conteudo.painel-user.class.php";

$pagina = new ConteudoPainelUser();
echo $pagina->render();
?> 
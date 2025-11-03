<?php
session_start();
ob_start();

include "../../classes/index.class.php";
include "classes/conteudo.painel-user.class.php";


if (!Index::validaLogin($_SESSION['data_user'] ?? [], $_SESSION['login_time'] ?? 0)) {
    $_SESSION['msg'] = '<p>Realize o login para acessar o painel</p>';
    header('Location:../');
    exit;
}

if (isset($_GET['sair'])) {
    Index::logOut();
}

$pagina = new ConteudoPainelUser();
echo $pagina->render();
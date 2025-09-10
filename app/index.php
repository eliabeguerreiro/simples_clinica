<?php
session_start();
ob_start();
//var_dump($_SESSION);

include"../classes/index.class.php";
include"classes/conteudo.painel.class.php";

if (Index::validaLogin($_SESSION['data_user'], $_SESSION['login_time'])){}else{
    $_SESSION['msg'] = '<p>Realize o login para acessar o painel</p>';
    header('Location:../');
    exit;
}


if(isset($_GET['sair'])){Index::logOut();}


$pagina = new ContentPainelInicial;
echo $pagina->renderHeader();
echo $pagina->renderBody();

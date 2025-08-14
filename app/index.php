<?php
session_start();
ob_start();
var_dump($_SESSION);

include"../classes/index.class.php";
include"classes/conteudo.painel.class.php";

if (!Index::validaLogin($_SESSION['data_user'])) {
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    //header('Location:../');
    exit;
}


if(isset($_GET['sair'])){Paineel::logOut();}


$pagina = new ContentPainel;
echo $pagina->renderHeader();
echo $pagina->renderBody();



?>

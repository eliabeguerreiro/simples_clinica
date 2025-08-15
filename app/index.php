<?php
session_start();
ob_start();
var_dump($_SESSION);

include"../classes/index.class.php";
include"classes/conteudo.totem.class.php";

if (Index::validaLogin($_SESSION['data_user'], $_SESSION['login_time'])){}else{
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painelasdad</p>';
    header('Location:../');
    exit;
}


if(isset($_GET['sair'])){Index::logOut();}


$pagina = new ContentPainel;
echo $pagina->renderHeader();
echo $pagina->renderBody();



?>

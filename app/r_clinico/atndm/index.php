<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


ob_start();


//include"classes/r_clinico.class.php";
include"../../../classes/db.class.php";
include"../../../classes/index.class.php";
include"classes/conteudo.r_clinico.atdnm.class.php";



if (Index::validaLogin($_SESSION['data_user'], $_SESSION['login_time'])){}else{
    $_SESSION['msg'] = '<p>Realize o login para acessar o painel</p>';
    header('Location:../../');
    exit;
}


if(isset($_GET['sair'])){Index::logOut();}


$pagina = new ContentRClinicoAtdnm;
echo $pagina->render();

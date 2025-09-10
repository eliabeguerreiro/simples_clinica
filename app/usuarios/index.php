<?php
session_start();
ob_start();
//var_dump($_SESSION);

include_once"classes/conteudo.painel-user.class.php";
include_once"classes/gest-user.class.php";
include_once"classes/db.class.php";

if(Paineel::validarToken()){

}else{
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 
    exit;
}



if(!isset($_SESSION['data_user'])){
  
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 

}

if(isset($_GET['sair'])){Paineel::logOut();}

$usuarios = User::getUsuarios(null);
$pagina = new ContentPainelUser;

if($_POST){  if($cad_item = User::setUsuario($_POST)){ header('location:');
}} 

echo $pagina->renderHeader();


if(isset($_GET['pagina'])){
    echo $pagina->renderBody($_GET['pagina'],$usuarios['dados']);
}else{
    echo $pagina->renderBody(null,$usuarios['dados']);
}

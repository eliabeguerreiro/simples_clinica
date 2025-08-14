<?php
session_start();
ob_start();
include_once"classes/conteudo.painel-itens.class.php";
include_once"classes/gest-item.class.php";
include_once"classes/db.class.php";




$familia = Item::getFamilia();
$itens = Item::getItens();
$itens_disponiveis = Item::getItensDisponiveis();
$itens_locados = Item::getItensLocados();
$itens_quebrados = Item::getItensQuebrados();

if(Paineel::validarToken()){

}else{
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 
    exit;
}


if(!isset($_SESSION['data_user'])){
  
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 
    exit;
}

if(isset($_GET['sair'])){Paineel::logOut();}




if($_POST){  if($cad_item = Item::setItem($_POST)){ header('location:');}} 



//var_dump($itens);
$pagina = new ContentPainelItem;
echo $pagina->renderHeader();

if(isset($_GET['pagina'])){
    echo $pagina->renderBody($_GET['pagina'], $familia['dados'], $itens['dados'], $itens_disponiveis['dados'], $itens_locados['dados'], $itens_quebrados['dados']);
}else{
    echo $pagina->renderBody(null, $familia['dados'], $itens['dados'], $itens_disponiveis['dados'], $itens_locados['dados'], $itens_quebrados['dados']);
}

?>


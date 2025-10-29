<?php
session_start();
ob_start();
include "classes/conteudo.r_clinico.pcnt.class.php";
include "../../../classes/index.class.php";

if (!Index::validaLogin($_SESSION['data_user'], $_SESSION['login_time'])) {
    $_SESSION['msg'] = '<p>Realize o login para acessar o painel</p>';
    header('Location:../');
    exit;
}
if (isset($_GET['sair'])) {
    Index::logOut();
}

$paciente_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$pagina = new ConteudoRClinicoPCNT($paciente_id);
echo $pagina->render();
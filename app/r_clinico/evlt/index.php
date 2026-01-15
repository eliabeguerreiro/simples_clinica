<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ob_start();

include "../../../classes/db.class.php";
include "../../../classes/index.class.php";
include "classes/conteudo.r_clinico.evlt.class.php";


if (!Index::validaLogin($_SESSION['data_user'] ?? [], $_SESSION['login_time'] ?? 0)) {
    $_SESSION['msg'] = 'Realize o login para acessar o painel';
    header('Location:../../');
    exit;
}
if (isset($_GET['sair'])) {
    Index::logOut();
}

// Passa o paciente_id para a classe de conteúdo, se existir
$paciente_id = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : null;

$pagina = new ConteudoRClinicoEvlt($paciente_id);
echo $pagina->render();
<?php
session_start();
ob_start();

include "../../../classes/index.class.php";
include "classes/conteudo.r_clinico.evlt.class.php";


if (!Index::validaLogin($_SESSION['data_user'] ?? [], $_SESSION['login_time'] ?? 0)) {
    $_SESSION['msg'] = '<p>Realize o login para acessar o painel</p>';
    header('Location:../../');
    exit;
}
if (isset($_GET['sair'])) {
    Index::logOut();
}

// Passa o paciente_id para a classe de conteúdo, se existir
$paciente_id = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : null;

$pagina = new ConteudoRClinicoEvlt($paciente_id); // <-- agora aceita parâmetro
echo $pagina->render();
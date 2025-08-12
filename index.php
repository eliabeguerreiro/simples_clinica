<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}
include_once "classes/painel.class.php";
include_once "classes/db.class.php";
/*
$clinica = Painel::GetClinica();
$profissionais = Painel::GetProfissionais();
$pacientes = Painel::GetPacientes();
$procedimentos = Painel::GetProcedimentos();
*/
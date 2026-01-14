<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ob_start();
include "../../../classes/db.class.php";
include "../../../classes/index.class.php";
include "classes/conteudo.r_clinico.pcnt.class.php";

// 1. Valida login
if (!Index::validaLogin($_SESSION['data_user'] ?? [], $_SESSION['login_time'] ?? 0)) {
    $_SESSION['msg'] = 'Realize o login para acessar o painel';
    header('Location: ../../');
    exit;
} 

// 2. Verifica se o perfil foi carregado
if (!isset($_SESSION['data_user']['perfil_id'])) {
    $_SESSION['msg'] = 'Seu usuário não possui perfil definido.';
    header('Location: ../../');
    exit;
}

// 3. Carrega permissões na sessão (se ainda não estiverem)
if (!isset($_SESSION['data_user']['permissoes'])) {
    $gestor = new Paciente();
    if (!$gestor->carregarPermissoesDoPerfilNaSessao($_SESSION['data_user']['perfil_id'])) {
        $_SESSION['msg'] = 'Erro ao carregar permissões.';
        header('Location: ../../');
        exit;
    }
}

// 4. Verifica se o usuário tem pelo menos uma permissão relacionada ao Registro Clínico
$temAcesso = false;
if (isset($_SESSION['data_user']['permissoes'])) {
    foreach ($_SESSION['data_user']['permissoes'] as $permissao) {
        if (
            strpos($permissao, 'pacientes.') === 0 ||
            strpos($permissao, 'atendimentos.') === 0 ||
            strpos($permissao, 'evolucoes.') === 0
        ) {
            $temAcesso = true;
            break;
        }
    }
}

if (!$temAcesso) {
    $_SESSION['msg'] = 'Você não tem permissão para acessar o Registro Clínico.';
    header('Location: ../../');
    exit;
}

// 5. Se chegou aqui, tudo ok — instancia e renderiza
$paciente_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$pagina = new ConteudoRClinicoPCNT($paciente_id);
echo $pagina->render();
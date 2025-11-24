<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ob_start();

// Classes
include "../../classes/index.class.php";
include "classes/gest-user.class.php";
include "classes/conteudo.painel-user.class.php";

// 1. Valida login
if (!Index::validaLogin($_SESSION['data_user'] ?? [], $_SESSION['login_time'] ?? 0)) {
    $_SESSION['msg'] = 'Realize o login para acessar o painel.';
    header('Location: ../');
    exit;
}

// 2. Verifica se o perfil foi carregado
if (!isset($_SESSION['data_user']['perfil_id'])) {
    $_SESSION['msg'] = 'Seu usuário não possui perfil definido. Contate o administrador.';
    header('Location: ../');
    exit;
}

// 3. Carrega permissões do perfil (se ainda não estiverem na sessão)
if (!isset($_SESSION['data_user']['permissoes'])) {
    try {
        $gestor = new GestUser();
        if (!$gestor->carregarPermissoesDoPerfilNaSessao($_SESSION['data_user']['perfil_id'])) {
            throw new Exception("Falha ao carregar permissões.");
        }
    } catch (Exception $e) {
        error_log("Erro ao carregar permissões: " . $e->getMessage());
        $_SESSION['msg'] = 'Erro ao carregar permissões do usuário.';
        header('Location: ../');
        exit;
    }
}

// 4. Verifica permissão de acesso ao c_admin
if (!in_array('cadmin.acessar', $_SESSION['data_user']['permissoes'] ?? [])) {
    $_SESSION['msg'] = 'Você não tem permissão para acessar o painel administrativo.';
    header('Location: ../');
    exit;
}

// 5. Logout
if (isset($_GET['sair'])) {
    Index::logOut();
}

// 6. Renderiza
$pagina = new ConteudoPainelUser();
echo $pagina->render();
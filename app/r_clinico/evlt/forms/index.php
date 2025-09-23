<?php
session_start();

// Verifica autenticação
if (!isset($_SESSION['data_user'])) {
    header('Location: ../../');
    exit;
}

// Determina a ação
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'criar';

// Inclui a classe principal
include_once "classes/conteudo.forms.class.php";

switch($acao) {
    case 'criar':
        // Página de criação de formulários
        $conteudo = new ConteudoFormsCriar();
        echo $conteudo->render();
        break;
        
    case 'gerenciar':
        // Página de gerenciamento de formulários
        $conteudo = new ConteudoFormsGerenciar();
        echo $conteudo->render();
        break;
        
    case 'aplicar':
        // Página de aplicação de formulários
        $conteudo = new ConteudoFormsAplicar();
        echo $conteudo->render();
        break;
        
    default:
        // Redireciona para criação de formulário por padrão
        header('Location: ?acao=criar');
        exit;
}
?>
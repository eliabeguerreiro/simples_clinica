<?php
session_start();

// Verifica autenticação
if (!isset($_SESSION['data_user'])) {
    header('Location: ../../');
    exit;
}

// Determina a ação
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'principal';

switch($acao) {
    case 'criar':
        // Incluir classe de conteúdo para criação
        include_once "classes/conteudo.forms.class.php";
        $conteudo = new ConteudoFormsCriar();
        echo $conteudo->render();
        break;
        
    case 'gerenciar':
        // Incluir classe de conteúdo para gerenciamento
        include_once "classes/conteudo.forms.class.php";
        $conteudo = new ConteudoFormsGerenciar();
        echo $conteudo->render();
        break;
        
    case 'aplicar':
        // Incluir classe de conteúdo para aplicação
        include_once "classes/conteudo.forms.class.php";
        $conteudo = new ConteudoFormsAplicar();
        echo $conteudo->render();
        break;
        
    default:
        // Página principal de formulários
        include_once "classes/conteudo.forms.class.php";
        $conteudo = new ConteudoFormsPrincipal();
        echo $conteudo->render();
}

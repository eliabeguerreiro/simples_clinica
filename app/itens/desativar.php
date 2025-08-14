<?php
session_start();
include_once"classes/gest-item.class.php";
include_once"classes/db.class.php";

//var_dump($_SESSION);
//var_dump($_GET);

$item = Item::getItens($_GET['id']);

if (isset($_GET['desativar'])) {
    if (Item::desativarItem($_GET['id'])) {
        // Success message is already set in the method
    } else {
        // Error message is already set in the method
    }
}

$id = $_GET['id'];
$id_fam = $item['dados'][0]['id_familia'];
$fam = Item::getFamilia($id_fam);


$fami = $fam['dados'];
$familia = $fami[0]['ds_familia'];



$cod = $item['dados'][0]['cod_patrimonio'];
$nome = $item['dados'][0]['ds_item'];

if(Paineel::validarToken()){

}else{
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 
}
if(!isset($_SESSION['data_user'])){
  
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 

}





$html = <<<HTML


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desativar Item</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #0d6efd;
            color: white;
        }
        nav .logo {
            font-weight: bold;
        }
        nav a {
            color: white;
            text-decoration: none;
        }
        .box2 {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .box2 h1, .box2 h2 {
            text-align: center;
        }
        .box2 button {
            margin-top: 1rem;
        }
        .manutencao-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .manutencao-button:hover {
            background-color: #b02a37;
        }
        .cancel-button {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .cancel-button:hover {
            background-color: #565e64;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">Gerência de Movimentação</div>
        <a href="../"><button class="cancel-button"><i class="fa fa-sign-out"></i>Voltar</button></a>
    </nav>
    <main>
        <div class="box2">
            <h1>Tem certeza que deseja desativar o item abaixo?</h1>
            <h2>Código: $cod</h2>
            <h2>Família: $familia</h2>
            <h2>Nome: $nome</h2>
            <center>
HTML;

$html .="<a href='?desativar=1&id=$id'><button class='manutencao-button'>Desativar</button></a>";

$html.= <<<HTML

                <a href="index.php">
                    <button class="cancel-button">Cancelar</button>
                </a>
            </center>
        </div>
    </main>
</body>
</html>
HTML;


echo $html;

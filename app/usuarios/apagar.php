<?php
session_start();
include_once"classes/gest-user.class.php";
include_once"classes/db.class.php";

//var_dump($_SESSION);
//var_dump($_GET);

$usuarios = User::getUsuarios($_GET['id']);
$id = $_GET['id'];

if(Paineel::validarToken()){

}else{
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 
}
if(!isset($_SESSION['data_user'])){
  
    $_SESSION['msg'] = '<p>Você precisa logar para acessar o painel</p>';
    header('Location:../'); 

}

if(isset($_GET['apagar'])){
    if ($reserva = User::deleteUsuario($_GET['id'])) {
        $_SESSION['msg'] = "Usuario removido com sucesso!";
        header('Location:index.php');
    } else {
        echo "Falha ao apagar o item.";
    }
}


$nome = $usuarios['dados'][0]['nm_usuario'];


$html = <<<HTML
<!DOCTYPE html>
    <html>
        <head>
            <title>Movimentação N:$id</title>
            <link rel="stylesheet" href="src/style.css">
            
        </head>
<body>
    <nav>
        <i class="fa fa-user"></i> Olá, Usuário!
        <div class="logo">Gerencia de movimentação</div>
        <a href='../'><button class="sair"><i class="fa fa-sign-out"></i>Voltar</button></a>
    </nav>
    <main>
        
    <div class="box2">
    <center>
        <h1>Tem certeza que quer apagar o usuario abaixo?</h1>
        <h2>Nome: $nome</h2>
HTML;

$html .="<a href='?apagar=apagar&id=$id'><button class='manutencao-button'>Apagar</button></a>";

$html.= <<<HTML

    </center>
    </div>     
    </main>
    <br><br>
    <script>

    </script>
</body>
</html>

HTML;

echo $html;

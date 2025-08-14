<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include_once "classes/index.class.php";
include_once "classes/db.class.php";

if (!empty($_POST)) {
    $dados_login = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    if ($login = Index::login($dados_login)) {
        header('Location: app/');
        exit;
    } else {
        $_SESSION['msg'] = "<p id='aviso'>Login ou senha incorreto</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/style.css">
    <link rel="icon" type="image/png" href="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>Página de Login</title>
</head>
<body>
    <div id='login_place'>
        <!-- Adicione o logo aqui -->
        <div class="logo-container">
            <img src="" alt="Logo" class="logo">
        </div>
        
        <?php 
            if (isset($_SESSION['msg'])) {
                echo '<div class="msg ' . (strpos($_SESSION['msg'], 'logado') ? '' : 'error') . '">' . $_SESSION['msg'] . '</div>';
                unset($_SESSION['msg']);
            }
        ?>
        <!-- Formulário de login -->
        <form action="" method="POST">
            <label for="username" class="center-label">Login</label>
            <input type="text" id="username" name="login" required>
            <label for="password" class="center-label">Senha</label>
            <input type="password" id="password" name="senha" required>
            <button type="submit">Logar</button>
        </form>
    </div>
</body>
</html>
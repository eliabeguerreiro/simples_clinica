<?php
include_once "classes/index.class.php";
include_once "classes/db.class.php";


error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

/* Se já estiver logado, redireciona para o app
if (isset($_SESSION['data_user']) && Index::validaLogin($_SESSION['data_user'], $_SESSION['login_time'])) {
    header('Location: app/');
    exit;
}
*/


if (!empty($_POST)) {
    $dados_login = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    if (Index::login($dados_login)) {
        header('Location: app/');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simples Clínica - Login</title>
    <link rel="stylesheet" href="src/login.css">
    <link rel="icon" type="image/png" href="src/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <div class="logo-section">
                <i class="fas fa-clinic-medical logo-icon"></i>
                <h1>Simples Clínica</h1>
                <p class="subtitle">Sistema de Gestão Clínica</p>
            </div>

            <?php 
            if (isset($_SESSION['msg'])) {
                echo '<div class="alert ' . (strpos($_SESSION['msg'], 'sucesso') ? 'alert-success' : 'alert-error') . '">' . $_SESSION['msg'] . '</div>';
                unset($_SESSION['msg']);
            }
            ?>

            <form action="" method="POST" class="login-fields">
                <div class="input-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Usuário
                    </label>
                    <input type="text" id="username" name="login" required autocomplete="username">
                </div>

                <div class="input-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Senha
                    </label>
                    <input type="password" id="password" name="senha" required autocomplete="current-password">
                </div>

                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i>
                    Entrar no Sistema
                </button>
            </form>

            <div class="footer-info">
                <p><i class="fas fa-shield-alt"></i> Ambiente seguro</p>
                <p class="version">Simples Clínica v1.0</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
include_once "classes/index.class.php";
include_once "classes/db.class.php";

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
    <title>Sistema CLINIG - Login</title>
    <link rel="stylesheet" href="src/login.css">
    <link rel="icon" type="image/png" href="src/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <section class="login-section">
        <div class="login-container">
            <div class="login-box">
                <div class="login-header">
                    <img src="src/vivenciar_logov2.png" alt="Logo CLINIG" class="login-logo">
                    <h1 class="login-title">Espaço Terapêutico Vivenciar</h1>
                    <p class="login-subtitle">Acesso ao CLINIG</p>
                </div>

                <?php 
                if (isset($_SESSION['msg'])) {
                    echo '<div class="alert ' . (strpos($_SESSION['msg'], 'sucesso') !== false ? 'alert-success' : 'alert-error') . '">' . htmlspecialchars($_SESSION['msg']) . '</div>';
                    unset($_SESSION['msg']);
                }
                ?>

                <form action="" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="username" class="form-label"><i class="fas fa-user"></i> Usuário</label>
                        <input type="text" id="username" name="login" class="form-input" required autocomplete="username" autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label"><i class="fas fa-lock"></i> Senha</label>
                        <input type="password" id="password" name="senha" class="form-input" required autocomplete="current-password">
                    </div>

                    <button type="submit" class="login-button">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
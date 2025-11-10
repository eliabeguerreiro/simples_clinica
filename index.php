<?php
include_once "classes/index.class.php";
include_once "classes/db.class.php";


error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    <title>Sistema Simples de Gestão Clínica - Login</title>
    <link rel="stylesheet" href="src/login.css">
    <link rel="icon" type="image/png" href="src/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <section class="login-section">
        <div class="login-container container">
            <div class="login-box">
                <div class="login-header">
                    <div class="login-logo"><i class="fas fa-clinic-medical"></i></div>
                    <h1 class="login-title">Simples Clínica</h1>
                    <p class="login-subtitle">Acesso ao sistema</p>
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

                    <div class="form-group form-remember">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" class="checkbox-input">
                            <span>Lembrar-me neste computador</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block login-button">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>

                <div class="login-footer">
                    <p class="login-footer-text"><a href="#" class="link">Esqueceu sua senha?</a></p>
                </div>

                <!--div class="demo-credentials">
                    <h4 class="demo-title">🔓 Credenciais de Demonstração</h4>
                    <div class="demo-item">
                        <p><strong>Usuário:</strong> teste</p>
                        <p><strong>Senha:</strong> teste</p>
                    </div>
                </!--div-->
            </div>

            <div class="login-info">
                <div class="info-card">
                    <div class="info-icon">🔐</div>
                    <h3 class="info-title">Segurança</h3>
                    <p class="info-text">Dados protegidos; use HTTPS em produção.</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">⚡</div>
                    <h3 class="info-title">Acesso Rápido</h3>
                    <p class="info-text">Painel com agilidade para operações clínicas.</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">📱</div>
                    <h3 class="info-title">Responsivo</h3>
                    <p class="info-text">Funciona bem em desktop e mobile.</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">💬</div>
                    <h3 class="info-title">Suporte</h3>
                    <p class="info-text">Contate o administrador para credenciais reais.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
<?php
session_start();
include "classes/gest-user.class.php";

if (!isset($_SESSION['data_user'])) {
    $_SESSION['msg'] = '<p>Realize o login para acessar o painel</p>';
    header('Location: ../');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['msg'] = '<p>ID inválido.</p>';
    header('Location: index.php');
    exit;
}

$usuario = new GestUser();
$resultado = null;

if ($_POST && isset($_POST['acao']) && $_POST['acao'] == 'atualizar') {
    $resultado = $usuario->atualizar($id, $_POST);
}

$usuarioAtual = $usuario->buscarPorId($id);

if (!$usuarioAtual) {
    $_SESSION['msg'] = '<p>Usuário não encontrado.</p>';
    header('Location: index.php');
    exit;
}

// Renderizar formulário de edição
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="src/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="#" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="./">INICIO</a></li>
                <li><a href="/paciente">PACIENTES</a></li>
                <li><a href="/atendimentos.php">SUPORTE</a></li>
                <li><a href="/usuarios">SAIR</a></li>
            </ul>
        </nav>
    </header>

    <section class="simple-box">
        <h2>Editar Usuário</h2>

        <?php
        // Exibir mensagens
        if ($resultado && isset($resultado['sucesso']) && $resultado['sucesso']) {
            echo '<div class="form-message success">' . $resultado['mensagem'] . '</div>';
        } elseif ($resultado && isset($resultado['erros'])) {
            echo '<div class="form-message error">';
            foreach ($resultado['erros'] as $erro) {
                echo '<p>' . htmlspecialchars($erro) . '</p>';
            }
            echo '</div>';
        }
        ?>

        <div class="form-container">
            <form action="" method="POST">
                <input type="hidden" name="acao" value="atualizar">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF*</label>
                        <input required type="text" id="cpf" name="cpf" required maxlength="11" placeholder="Digite o CPF"
                               value="<?php echo htmlspecialchars($usuarioAtual['cpf']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="login">Login*</label>
                        <input required type="text" id="login" name="login" required maxlength="60" placeholder="Digite o login"
                               value="<?php echo htmlspecialchars($usuarioAtual['login']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="nm_usuario">Nome Completo*</label>
                        <input required type="text" id="nm_usuario" name="nm_usuario" required maxlength="45" placeholder="Digite o nome"
                               value="<?php echo htmlspecialchars($usuarioAtual['nm_usuario']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Nova Senha (opcional)</label>
                        <input type="password" id="senha" name="senha" maxlength="30" placeholder="Deixe em branco para manter a mesma">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo de Usuário*</label>
                        <select required id="tipo" name="tipo" required>
                            <option value="">Selecionar</option>
                            <option value="admin" <?php echo ($usuarioAtual['tipo'] == 'admin' ? 'selected' : ''); ?>>Administrador</option>
                            <option value="user" <?php echo ($usuarioAtual['tipo'] == 'user' ? 'selected' : ''); ?>>Usuário Comum</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-add">
                    <i class="fas fa-save"></i> Atualizar Usuário
                </button>
            </form>
        </div>

        <div style="margin-top: 20px;">
            <a href="index.php" class="btn-clear">
                <i class="fas fa-arrow-left"></i> Voltar à Listagem
            </a>
        </div>
    </section>

    <script src="src/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</body>
</html>
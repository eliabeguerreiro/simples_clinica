<?php
session_start();
include "classes/gest-user.class.php";
if (!isset($_SESSION['data_user'])) {
    $_SESSION['msg'] = 'Realize o login para acessar o painel';
    header('Location: ../');
    exit;
}

include "../../classes/db.class.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['msg'] = '<p>ID inválido.</p>';
    header('Location: index.php');
    exit;
}

$gestor = new GestUser();
$usuarioAtual = $gestor->buscarPorId($id);
if (!$usuarioAtual) {
    $_SESSION['msg'] = '<p>Usuário não encontrado.</p>';
    header('Location: index.php');
    exit;
}

// Carregar perfis para o select
$perfis = $gestor->listarPerfis();

$resultado = null;
if ($_POST && isset($_POST['acao']) && $_POST['acao'] == 'atualizar') {
    $resultado = $gestor->atualizar($id, $_POST);
    // Recarregar dados em caso de erro
    if (!$resultado['sucesso']) {
        $usuarioAtual = $_POST;
    } else {
        // Redireciona após sucesso
        $_SESSION['msg'] = '<div class="form-message success">Usuário atualizado com sucesso!</div>';
        header('Location: index.php');
        exit;
    }
}

// Gerar opções do select
$opcoesPerfis = '<option value="">Selecionar</option>';
foreach ($perfis as $perfil) {
    $selected = (isset($usuarioAtual['perfil_id']) && $usuarioAtual['perfil_id'] == $perfil['id']) ? 'selected' : '';
    $opcoesPerfis .= '<option value="' . $perfil['id'] . '" ' . $selected . '>' . htmlspecialchars(ucfirst($perfil['nome'])) . '</option>';
}
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
                <li><a href="#">SUPORTE</a></li>
                <li><a href="?sair">SAIR</a></li>
            </ul>
        </nav>
    </header>
    <section class="simple-box">
        <h2>Editar Usuário</h2>
        <?php if ($resultado && isset($resultado['erros'])): ?>
            <div class="form-message error">
                <?php foreach ($resultado['erros'] as $erro): ?>
                    <p><?= htmlspecialchars($erro) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="" method="POST">
                <input type="hidden" name="acao" value="atualizar">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF*</label>
                        <input required type="text" id="cpf" name="cpf" maxlength="11"
                               value="<?= htmlspecialchars($usuarioAtual['cpf'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="login">Login*</label>
                        <input required type="text" id="login" name="login" maxlength="60"
                               value="<?= htmlspecialchars($usuarioAtual['login'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="nm_usuario">Nome Completo*</label>
                        <input required type="text" id="nm_usuario" name="nm_usuario" maxlength="45"
                               value="<?= htmlspecialchars($usuarioAtual['nm_usuario'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Nova Senha (opcional)</label>
                        <input type="password" id="senha" name="senha" maxlength="30" placeholder="Deixe em branco para manter a mesma">
                    </div>
                    <div class="form-group">
                        <label for="perfil_id">Tipo de Usuário*</label>
                        <select required name="perfil_id">
                            <?= $opcoesPerfis ?>
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
</body>
</html>
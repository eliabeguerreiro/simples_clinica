<?php
session_start();
include "../../../classes/db.class.php";

if (!isset($_GET['form_id']) || !is_numeric($_GET['form_id'])) {
    die("<h2>Formulário não especificado</h2>");
}

$form_id = (int)$_GET['form_id'];

try {
    $db = DB::connect();
    $stmt = $db->prepare("SELECT * FROM formulario WHERE id = ?");
    $stmt->execute([$form_id]);
    $formulario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$formulario) {
        die("<h2>Formulário não encontrado</h2>");
    }
} catch (Exception $e) {
    die("<h2>Erro ao carregar formulário</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}

// Processa atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $especialidade = trim($_POST['especialidade'] ?? '');
        $s_n_anexo = isset($_POST['s_n_anexo']) && $_POST['s_n_anexo'] === 'S' ? 'S' : 'N';
        $ativo = isset($_POST['ativo']) && $_POST['ativo'] === '1' ? 1 : 0;

        if (empty($nome) || empty($especialidade)) {
            throw new Exception("Nome e especialidade são obrigatórios.");
        }

        $stmt = $db->prepare("
            UPDATE formulario 
            SET nome = ?, descricao = ?, especialidade = ?, s_n_anexo = ?, ativo = ?
            WHERE id = ?
        ");
        $stmt->execute([$nome, $descricao, $especialidade, $s_n_anexo, $ativo, $form_id]);

        $_SESSION['mensagem'] = ['sucesso' => true, 'texto' => 'Formulário atualizado com sucesso!'];
        header("Location: editar_forms.php?form_id=$form_id");
        exit;
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Formulário</title>
    <link rel="stylesheet" href="construtor_forms.css">
    <style>
        .btn-voltar {
            background: #f5f5f7;
            color: #574b90;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-voltar:hover {
            background: #e8e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Dados do Formulário</h2>
        <p><strong>Formulário ID:</strong> <?= $form_id ?></p>

        <?php if (isset($erro)): ?>
            <div class="alert alert-erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-<?= $_SESSION['mensagem']['sucesso'] ? 'sucesso' : 'erro' ?>">
                <?= htmlspecialchars($_SESSION['mensagem']['texto']) ?>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome do Formulário*</label>
                    <input type="text" id="nome" name="nome" required maxlength="100"
                           value="<?= htmlspecialchars($formulario['nome']) ?>">
                </div>
                <div class="form-group">
                    <label for="s_n_anexo">Recebe anexos de arquivos?*</label>
                    <select id="s_n_anexo" name="s_n_anexo" required>
                        <option value="N" <?= $formulario['s_n_anexo'] === 'N' ? 'selected' : '' ?>>Não</option>
                        <option value="S" <?= $formulario['s_n_anexo'] === 'S' ? 'selected' : '' ?>>Sim</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="especialidade">Especialidade*</label>
                    <select id="especialidade" name="especialidade" required>
                        <option value="">Selecionar</option>
                        <option value="FISIO" <?= $formulario['especialidade'] === 'FISIO' ? 'selected' : '' ?>>Fisioterapia</option>
                        <option value="FONO" <?= $formulario['especialidade'] === 'FONO' ? 'selected' : '' ?>>Fonoaudiologia</option>
                        <option value="TEOC" <?= $formulario['especialidade'] === 'TEOC' ? 'selected' : '' ?>>Terapia Ocupacional</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ativo">Ativo*</label>
                    <select id="ativo" name="ativo" required>
                        <option value="1" <?= $formulario['ativo'] == 1 ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= $formulario['ativo'] == 0 ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" name="descricao" maxlength="255"
                           value="<?= htmlspecialchars($formulario['descricao'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="index.php" class="btn-voltar">Voltar para Listagem</a>
        </form>
    </div>
</body>
</html>
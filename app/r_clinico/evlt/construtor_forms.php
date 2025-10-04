<?php
// filepath: c:\laragon\www\simples_clinica\app\r_clinico\evlt\construtor_forms.php
session_start();

var_dump($_SESSION);
echo '<br>';
var_dump($_POST);

// Conexão com o banco (ajuste conforme seu projeto)
require_once "classes/db.class.php";


//$pdo = Db::getInstance();

// Recebe o ID do formulário via GET
$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;

// Processa o cadastro da pergunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo = $_POST['titulo'];
    $tipo = $_POST['tipo'];
    $opcoes = isset($_POST['opcoes']) ? $_POST['opcoes'] : '';

    $sql = "INSERT INTO perguntas_forms (form_id, titulo, tipo, opcoes) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$form_id, $titulo, $tipo, $opcoes]);

    echo "<script>alert('Pergunta adicionada!');window.location='construtor_forms.php?form_id=$form_id';</script>";
    exit;
}

// Busca perguntas já cadastradas
$perguntas = [];
if ($form_id) {
    $sql = "SELECT * FROM perguntas_forms WHERE form_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$form_id]);
    $perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Construtor de Perguntas do Formulário</title>
    <link rel="stylesheet" href="src/style.css">
    <style>
        .form-builder { max-width: 800px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px #0001; padding: 32px; }
        .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
        .form-group { flex: 1; display: flex; flex-direction: column; }
        .questions-list { margin-top: 24px; }
        .question-item { background: #f7f7ff; border-radius: 8px; padding: 16px; margin-bottom: 12px; border: 1px solid #eee; }
        .btn { background: #6c63ff; color: #fff; border: none; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-weight: bold; }
        .btn-danger { background: #e74c3c; }
        .btn + .btn { margin-left: 8px; }
    </style>
</head>
<body>
    <div class="form-builder">
        <h2>Adicionar Pergunta ao Formulário</h2>
        <form id="form-pergunta" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="titulo">Título da Pergunta*</label>
                    <input type="text" id="titulo" name="titulo" required maxlength="100" placeholder="Ex: Descreva a evolução">
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo*</label>
                    <select id="tipo" name="tipo" required>
                        <option value="texto">Texto contínuo</option>
                        <option value="radio">Escolha única (Radio)</option>
                        <option value="select">Seleção (Select)</option>
                        <option value="anexo">Anexo de arquivo</option>
                    </select>
                </div>
                <div class="form-group" id="opcoes-container" style="display:none;">
                    <label for="opcoes">Opções (separadas por vírgula)</label>
                    <input type="text" id="opcoes" name="opcoes" maxlength="200" placeholder="Ex: Sim,Não,Parcial">
                </div>
            </div>
            <button type="submit" class="btn">Salvar Pergunta</button>
        </form>

        <hr>
        <h3>Perguntas já cadastradas</h3>
        <div class="questions-list">
            <?php if ($perguntas): ?>
                <?php foreach ($perguntas as $p): ?>
                    <div class="question-item">
                        <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                        <span style="color:#574b90;">[<?= htmlspecialchars($p['tipo']) ?>]</span>
                        <?php if ($p['opcoes']): ?>
                            <br><small>Opções: <?= htmlspecialchars($p['opcoes']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma pergunta cadastrada ainda.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        // Mostra campo de opções para radio/select
        document.getElementById('tipo').addEventListener('change', function() {
            document.getElementById('opcoes-container').style.display = (this.value === 'radio' || this.value === 'select') ? 'block' : 'none';
        });
    </script>
</body>
</html>
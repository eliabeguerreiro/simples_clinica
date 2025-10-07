<?php
session_start();
include "classes/db.class.php";

// Função auxiliar para mensagens
function setMensagem($texto, $tipo = 'sucesso') {
    $_SESSION['mensagem'] = ['texto' => $texto, 'tipo' => $tipo];
}

// Limpa mensagem antiga (exibe só uma vez)
$mensagem = null;
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}

try {
    $db = DB::connect();
} catch (Exception $e) {
    die("Erro crítico: não foi possível conectar ao banco. " . $e->getMessage());
}

// ==============================================
// 1. CRIAÇÃO DE NOVO FORMULÁRIO (via POST sem form_id)
// ==============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['form_id'])) {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $especialidade = trim($_POST['especialidade'] ?? '');

    if (empty($nome) || empty($especialidade)) {
        setMensagem('Erro: Nome e especialidade são obrigatórios.', 'erro');
        header("Location: construtor_forms.php");
        exit;
    }

    try {
        // Insere o formulário
        $sql = "INSERT INTO formulario (nome, descricao, especialidade) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $descricao, $especialidade]);

        $ultimoId = $db->lastInsertId();

        // Cria pasta anexo/ID
        $caminhoAnexo = __DIR__ . '/anexo';
        if (!is_dir($caminhoAnexo)) {
            mkdir($caminhoAnexo, 0755, true);
        }

        $novaPasta = $caminhoAnexo . '/' . $ultimoId;
        if (!is_dir($novaPasta)) {
            mkdir($novaPasta, 0755);
        }

        setMensagem("Formulário '$nome' criado com sucesso! Pasta anexo/$ultimoId criada.");
        header("Location: construtor_forms.php?form_id=$ultimoId");
        exit;

    } catch (Exception $e) {
        setMensagem("Erro ao criar formulário: " . $e->getMessage(), 'erro');
        header("Location: construtor_forms.php");
        exit;
    }
}

// ==============================================
// 2. ADIÇÃO DE PERGUNTA (POST com form_id na URL)
// ==============================================
$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $form_id > 0 && isset($_POST['titulo'])) {
    $titulo = trim($_POST['titulo']);
    $tipo = $_POST['tipo'] ?? 'texto';
    $opcoes = trim($_POST['opcoes'] ?? '');

    if (empty($titulo)) {
        setMensagem('Título da pergunta é obrigatório.', 'erro');
    } else {
        try {
            $sql = "INSERT INTO perguntas_forms (form_id, titulo, tipo, opcoes) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$form_id, $titulo, $tipo, $opcoes]);
            setMensagem('Pergunta adicionada com sucesso!');
        } catch (Exception $e) {
            setMensagem('Erro ao salvar pergunta: ' . $e->getMessage(), 'erro');
        }
    }

    header("Location: construtor_forms.php?form_id=$form_id");
    exit;
}

// ==============================================
// 3. BUSCA DADOS PARA EXIBIÇÃO
// ==============================================
$perguntas = [];
$formulario = null;

if ($form_id > 0) {
    // Busca o formulário (opcional, para título)
    $stmt = $db->prepare("SELECT * FROM formulario WHERE id = ?");
    $stmt->execute([$form_id]);
    $formulario = $stmt->fetch();

    if (!$formulario) {
        setMensagem('Formulário não encontrado.', 'erro');
        header("Location: construtor_forms.php");
        exit;
    }

    // Busca perguntas
    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE form_id = ? ORDER BY id");
    $stmt->execute([$form_id]);
    $perguntas = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Construtor de Formulários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 6px; }
        .alert-sucesso { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-erro { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-row { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 200px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .btn { background: #6c63ff; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn:hover { opacity: 0.9; }
        .btn-secundario { background: #6c757d; }
        .question-item { background: #f8f9fa; padding: 12px; margin: 10px 0; border-left: 4px solid #6c63ff; }
        .question-item small { color: #666; }
    </style>
</head>
<body>
    <div class="container">

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $mensagem['tipo'] === 'erro' ? 'erro' : 'sucesso' ?>">
                <?= htmlspecialchars($mensagem['texto']) ?>
            </div>
        <?php endif; ?>

        <?php if ($form_id && $formulario): ?>
            <!-- Construtor de Perguntas -->
            <h2>Construtor: <?= htmlspecialchars($formulario['nome']) ?> (ID: <?= $form_id ?>)</h2>
            <p><a href="construtor_forms.php" class="btn btn-secundario" style="text-decoration: none; display: inline-block; margin-bottom: 20px;">+ Novo Formulário</a></p>

            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="titulo">Pergunta *</label>
                        <input type="text" id="titulo" name="titulo" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="tipo">Tipo *</label>
                        <select id="tipo" name="tipo" required>
                            <option value="texto">Texto contínuo</option>
                            <option value="radio">Escolha única (Radio)</option>
                            <option value="select">Lista suspensa</option>
                            <option value="anexo">Anexo de arquivo</option>
                        </select>
                    </div>
                    <div class="form-group" id="opcoes-container" style="display:none;">
                        <label for="opcoes">Opções (separadas por vírgula)</label>
                        <input type="text" id="opcoes" name="opcoes" maxlength="200" placeholder="Ex: Sim,Não,Talvez">
                    </div>
                </div>
                <button type="submit" class="btn">Adicionar Pergunta</button>
            </form>

            <hr style="margin: 25px 0;">

            <h3>Perguntas Cadastradas (<?= count($perguntas) ?>)</h3>
            <?php if ($perguntas): ?>
                <?php foreach ($perguntas as $p): ?>
                    <div class="question-item">
                        <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                        <br><small>Tipo: <?= htmlspecialchars($p['tipo']) ?></small>
                        <?php if (!empty($p['opcoes'])): ?>
                            <br><small>Opções: <?= htmlspecialchars($p['opcoes']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhuma pergunta cadastrada ainda.</p>
            <?php endif; ?>

        <?php else: ?>
            <!-- Formulário para criar novo formulário -->
            <h2>Novo Formulário Clínico</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome do Formulário *</label>
                        <input type="text" id="nome" name="nome" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="especialidade">Especialidade *</label>
                        <input type="text" id="especialidade" name="especialidade" required maxlength="100">
                    </div>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <input type="text" id="descricao" name="descricao" maxlength="255">
                </div>
                <button type="submit" class="btn">Criar Formulário</button>
            </form>
        <?php endif; ?>

    </div>

    <script>
        document.getElementById('tipo')?.addEventListener('change', function() {
            const container = document.getElementById('opcoes-container');
            if (this.value === 'radio' || this.value === 'select') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                document.getElementById('opcoes').value = '';
            }
        });
    </script>
</body>
</html>
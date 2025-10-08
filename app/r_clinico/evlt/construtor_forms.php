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

// Redireciona se não houver form_id
$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
if ($form_id <= 0) {
    header("Location: index.php");
    exit;
}

// Busca formulário
$stmt = $db->prepare("SELECT * FROM formulario WHERE id = ?");
$stmt->execute([$form_id]);
$formulario = $stmt->fetch();

if (!$formulario) {
    setMensagem('Formulário não encontrado.', 'erro');
    header("Location: index.php");
    exit;
}

// Processa adição de pergunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo_input = $_POST['tipo_input'] ?? 'texto';
    $nome_unico = trim($_POST['nome_unico'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $placeholder = trim($_POST['placeholder'] ?? '');
    $tamanho_maximo = (int)($_POST['tamanho_maximo'] ?? 255);
    $ordem = (int)($_POST['ordem'] ?? 1);
    $obrigatorio = (int)($_POST['obrigatorio'] ?? 0);
    $multipla_escolha = (int)($_POST['multipla_escolha'] ?? 0);

    if (empty($titulo)) {
        setMensagem('Título da pergunta é obrigatório.', 'erro');
    } else {
        try {
            // Gera nome único se não fornecido
            if (empty($nome_unico)) {
                $nome_unico = preg_replace('/[^a-z0-9_]/', '_', strtolower($titulo));
                $nome_unico = substr($nome_unico, 0, 50);
                if (empty($nome_unico)) $nome_unico = 'campo_' . time();
            }

            // Processa opções SOMENTE para tipos que usam
            $tiposComOpcoes = ['radio', 'checkbox', 'select'];
            $opcoes = null; // padrão: NULL

            if (in_array($tipo_input, $tiposComOpcoes)) {
                $opcoesRaw = trim($_POST['opcoes'] ?? '');
                if (!empty($opcoesRaw)) {
                    $opcoesArray = array_map('trim', explode(',', $opcoesRaw));
                    $opcoes = json_encode($opcoesArray, JSON_UNESCAPED_UNICODE);
                }
                // Se vazio, mantém $opcoes = null
            }

            $sql = "INSERT INTO formulario_perguntas (
                        formulario_id, nome_unico, titulo, descricao, tipo_input, opcoes,
                        obrigatorio, multipla_escolha, tamanho_maximo, placeholder, ordem,
                        ativo, data_criacao, data_atualizacao
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $form_id,
                $nome_unico,
                $titulo,
                $descricao,
                $tipo_input,
                $opcoes, // pode ser string JSON ou NULL
                $obrigatorio,
                $multipla_escolha,
                $tamanho_maximo,
                $placeholder,
                $ordem,
                1 // ativo
            ]);

            setMensagem('Pergunta adicionada com sucesso!');
        } catch (Exception $e) {
            setMensagem('Erro ao salvar pergunta: ' . $e->getMessage(), 'erro');
        }
    }

    header("Location: construtor_forms.php?form_id=$form_id");
    exit;
}

// Busca perguntas cadastradas
$stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? ORDER BY ordem, id");
$stmt->execute([$form_id]);
$perguntas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Construtor de Formulários</title>
    <link rel="stylesheet" href="construtor_forms.css">
</head>
<body>
    <div class="container">

        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $mensagem['tipo'] === 'erro' ? 'erro' : 'sucesso' ?>">
                <?= htmlspecialchars($mensagem['texto']) ?>
            </div>
        <?php endif; ?>

        <h2>Construtor: <?= htmlspecialchars($formulario['nome']) ?> (ID: <?= $form_id ?>)</h2>
        <p><a href="index.php" class="btn-secundario">Voltar</a></p>

        <!-- Formulário para adicionar pergunta -->
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="titulo">Título da Pergunta</label>
                    <input type="text" id="titulo" name="titulo" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="tipo_input">Tipo de Campo</label>
                    <select id="tipo_input" name="tipo_input" required>
                        <option value="texto">Texto Livre</option>
                        <option value="textarea">Área de Texto</option>
                        <option value="radio">Escolha Única (Radio)</option>
                        <option value="checkbox">Múltipla Escolha (Checkbox)</option>
                        <option value="select">Lista Suspensa</option>
                        <option value="date">Data</option>
                        <option value="number">Número</option>
                        <option value="file">Anexo de Arquivo</option>
                    </select>
                </div>
            </div>

            <!-- Opções (só para radio, checkbox, select) -->
            <div class="form-row" id="opcoes-container" style="display:none;">
                <div class="form-group">
                    <label for="opcoes">Opções (separadas por vírgula)</label>
                    <input type="text" id="opcoes" name="opcoes" maxlength="1000" placeholder="Ex: Sim,Não,Talvez">
                </div>
            </div>

            <!-- Campos comuns -->
            <div class="form-row">
                <div class="form-group">
                    <label for="nome_unico">Nome Único (identificador interno)</label>
                    <input type="text" id="nome_unico" name="nome_unico" maxlength="50" placeholder="Ex: sintomas_paciente">
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição / Ajuda</label>
                    <input type="text" id="descricao" name="descricao" maxlength="255" placeholder="Ex: Descreva os sintomas observados">
                </div>
            </div>

            <!-- Placeholder e tamanho (só para texto, textarea, number) -->
            <div class="form-row" id="texto-container" style="display:none;">
                <div class="form-group">
                    <label for="placeholder">Placeholder</label>
                    <input type="text" id="placeholder" name="placeholder" maxlength="100" placeholder="Ex: Digite aqui...">
                </div>
                <div class="form-group">
                    <label for="tamanho_maximo">Tamanho Máximo (caract.)</label>
                    <input type="number" id="tamanho_maximo" name="tamanho_maximo" min="1" max="10000" value="255">
                </div>
            </div>

            <!-- Ordem e obrigatório (sempre visíveis) -->
            <div class="form-row">
                <div class="form-group">
                    <label for="ordem">Ordem de Exibição</label>
                    <input type="number" id="ordem" name="ordem" min="1" value="1">
                </div>
                <div class="form-group">
                    <label for="obrigatorio">Obrigatório?</label>
                    <select id="obrigatorio" name="obrigatorio">
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>

            <!-- Múltipla escolha (só para checkbox) -->
            <div class="form-row" id="multipla-container" style="display:none;">
                <div class="form-group">
                    <label for="multipla_escolha">Múltipla Escolha?</label>
                    <select id="multipla_escolha" name="multipla_escolha">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn">Adicionar Pergunta</button>
        </form>

        <hr>

        <h3>Perguntas Cadastradas (<?= count($perguntas) ?>)</h3>
        <?php if ($perguntas): ?>
            <?php foreach ($perguntas as $p): ?>
                <div class="question-item">
                    <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                    <br><small>Tipo: <?= htmlspecialchars($p['tipo_input']) ?></small>
                    <?php if (!empty($p['descricao'])): ?>
                        <br><small>Descrição: <?= htmlspecialchars($p['descricao']) ?></small>
                    <?php endif; ?>
                    <?php if (!is_null($p['opcoes']) && $p['opcoes'] !== 'null'): ?>
                        <?php
                        $opcoesArray = json_decode($p['opcoes'], true);
                        if (is_array($opcoesArray) && !empty($opcoesArray)):
                        ?>
                            <br><small>Opções: <?= htmlspecialchars(implode(', ', $opcoesArray)) ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                    <br><small>Ordem: <?= (int)$p['ordem'] ?> | Obrigatório: <?= $p['obrigatorio'] ? 'Sim' : 'Não' ?></small>
                    <?php if ($p['multipla_escolha']): ?>
                        <br><small>Múltipla escolha: Sim</small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhuma pergunta cadastrada ainda.</p>
        <?php endif; ?>

    </div>

    <script src="construtor_forms.js"></script>
</body>
</html>
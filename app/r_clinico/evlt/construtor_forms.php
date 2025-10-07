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

        setMensagem("Formulário '$nome' criado com sucesso!");
        header("Location: construtor_forms.php?form_id=$ultimoId");
        exit;

    } catch (Exception $e) {
        setMensagem("Erro ao criar formulário: " . $e->getMessage(), 'erro');
        header("Location: construtor_forms.php");
        exit;
    }
}


$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $form_id > 0 && isset($_POST['titulo'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo_input = $_POST['tipo_input'] ?? 'texto';
    $opcoes = trim($_POST['opcoes'] ?? '');
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
            if (empty($nome_unico)) {
                // Gera nome único baseado no título
                $nome_unico = preg_replace('/[^a-z0-9_]/', '_', strtolower($titulo));
                $nome_unico = substr($nome_unico, 0, 50);
                if (empty($nome_unico)) $nome_unico = 'campo_' . time();
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
                $opcoes,
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

// ==============================================
// 3. BUSCA DADOS PARA EXIBIÇÃO
// ==============================================
$perguntas = [];
$formulario = null;

if ($form_id > 0) {
    $stmt = $db->prepare("SELECT * FROM formulario WHERE id = ?");
    $stmt->execute([$form_id]);
    $formulario = $stmt->fetch();

    if (!$formulario) {
        setMensagem('Formulário não encontrado.', 'erro');
        header("Location: construtor_forms.php");
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? ORDER BY ordem, id");
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
        .btn-secundario { background: #6c757d; text-decoration: none; display: inline-block; padding: 8px 16px; }
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
            <h2>Construtor: <?= htmlspecialchars($formulario['nome']) ?> (ID: <?= $form_id ?>)</h2>
            <p><a href="index.php" class="btn-secundario">Voltar</a></p>

            <!-- Formulário para adicionar pergunta -->
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="titulo">Título da Pergunta *</label>
                        <input type="text" id="titulo" name="titulo" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="tipo_input">Tipo de Campo *</label>
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

            <hr style="margin: 25px 0;">

            <h3>Perguntas Cadastradas (<?= count($perguntas) ?>)</h3>
            <?php if ($perguntas): ?>
                <?php foreach ($perguntas as $p): ?>
                    <div class="question-item">
                        <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                        <br><small>Tipo: <?= htmlspecialchars($p['tipo_input']) ?></small>
                        <?php if (!empty($p['descricao'])): ?>
                            <br><small>Descrição: <?= htmlspecialchars($p['descricao']) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($p['opcoes'])): ?>
                            <br><small>Opções: <?= htmlspecialchars($p['opcoes']) ?></small>
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

         
        <?php endif; ?>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_input');
        const opcoesContainer = document.getElementById('opcoes-container');
        const textoContainer = document.getElementById('texto-container');
        const multiplaContainer = document.getElementById('multipla-container');

        function atualizarCampos() {
            const tipo = tipoSelect.value;

            // Esconde todos
            opcoesContainer.style.display = 'none';
            textoContainer.style.display = 'none';
            multiplaContainer.style.display = 'none';

            // Limpa valores irrelevantes
            if (!['radio', 'checkbox', 'select'].includes(tipo)) {
                document.getElementById('opcoes').value = '';
            }
            if (tipo !== 'checkbox') {
                document.getElementById('multipla_escolha').value = '0';
            }

            // Mostra conforme o tipo
            if (tipo === 'radio' || tipo === 'checkbox' || tipo === 'select') { 
                opcoesContainer.style.display = 'flex';
            }

            if (tipo === 'checkbox') {
                multiplaContainer.style.display = 'flex';
            }

            if (tipo === 'texto' || tipo === 'textarea' || tipo === 'number') {
                textoContainer.style.display = 'flex';
            }
        }

        if (tipoSelect) {
            tipoSelect.addEventListener('change', atualizarCampos);
            atualizarCampos(); // Inicializa
        }
    });
    </script>
</body>
</html>
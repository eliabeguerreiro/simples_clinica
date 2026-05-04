<?php
session_start();
include "../../../classes/db.class.php";

function setMensagem($texto, $tipo = 'sucesso') {
    $_SESSION['mensagem'] = ['texto' => $texto, 'tipo' => $tipo];
}

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

$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
if ($form_id <= 0) {
    header("Location: index.php");
    exit;
}

// ==================== BUSCAR DADOS PARA EDIÇÃO (AJAX) ====================
if (isset($_GET['acao']) && $_GET['acao'] === 'editar_dados') {
    header('Content-Type: application/json');
    $perguntaId = (int)($_GET['pergunta_id'] ?? 0);
    
    try {
        $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE id = ? AND formulario_id = ?");
        $stmt->execute([$perguntaId, $form_id]);
        $pergunta = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pergunta) {
            echo json_encode(['sucesso' => true, 'dados' => $pergunta]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => 'Pergunta não encontrada']);
        }
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
    }
    exit;
}

// ==================== SALVAR EDIÇÃO (POST AJAX) ====================
if (isset($_POST['acao']) && $_POST['acao'] === 'salvar_edicao') {
    header('Content-Type: application/json');
    
    try {
        $perguntaId = (int)($_POST['pergunta_id'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $placeholder = trim($_POST['placeholder'] ?? '');
        $tamanhoMaximo = max(1, (int)($_POST['tamanho_maximo'] ?? 255));
        $obrigatorio = (int)($_POST['obrigatorio'] ?? 0);
        
        // Grid columns (com fallback se não existirem no banco)
        $gridCol = isset($_POST['grid_col']) ? max(1, min(3, (int)$_POST['grid_col'])) : 1;
        $gridRow = isset($_POST['grid_row']) ? max(1, (int)$_POST['grid_row']) : 1;
        $gridColspan = isset($_POST['grid_colspan']) ? max(1, min(3, (int)$_POST['grid_colspan'])) : 1;
        $gridRowspan = isset($_POST['grid_rowspan']) ? max(1, min(5, (int)$_POST['grid_rowspan'])) : 1;
        
        if (empty($titulo)) {
            throw new Exception("Título é obrigatório");
        }
        
        // Verifica se colunas de grid existem (fallback seguro)
        $cols = $db->query("SHOW COLUMNS FROM formulario_perguntas LIKE 'grid_col'")->fetch();
        $hasGrid = (bool)$cols;
        
        if ($hasGrid) {
            $stmt = $db->prepare("
                UPDATE formulario_perguntas 
                SET titulo = ?, descricao = ?, placeholder = ?, tamanho_maximo = ?, 
                    obrigatorio = ?, grid_col = ?, grid_row = ?, grid_colspan = ?, 
                    grid_rowspan = ?, data_atualizacao = NOW()
                WHERE id = ? AND formulario_id = ?
            ");
            $stmt->execute([
                $titulo, $descricao, $placeholder, $tamanhoMaximo,
                $obrigatorio, $gridCol, $gridRow, $gridColspan, $gridRowspan,
                $perguntaId, $form_id
            ]);
        } else {
            // Fallback sem grid
            $stmt = $db->prepare("
                UPDATE formulario_perguntas 
                SET titulo = ?, descricao = ?, placeholder = ?, tamanho_maximo = ?, 
                    obrigatorio = ?, data_atualizacao = NOW()
                WHERE id = ? AND formulario_id = ?
            ");
            $stmt->execute([
                $titulo, $descricao, $placeholder, $tamanhoMaximo,
                $obrigatorio, $perguntaId, $form_id
            ]);
        }
        
        echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta atualizada com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
    }
    exit;
}

// ==================== ATUALIZAR POSIÇÃO GRID (AJAX) ====================
if (isset($_POST['acao']) && $_POST['acao'] === 'atualizar_posicao_grid') {
    header('Content-Type: application/json');
    try {
        $perguntaId = (int)($_POST['pergunta_id'] ?? 0);
        $gridCol = max(1, min(3, (int)($_POST['grid_col'] ?? 1)));
        $gridRow = max(1, (int)($_POST['grid_row'] ?? 1)));
        $colspan = max(1, min(3, (int)($_POST['grid_colspan'] ?? 1)));
        $rowspan = max(1, min(5, (int)($_POST['grid_rowspan'] ?? 1)));
        if ($perguntaId <= 0) throw new Exception("ID da pergunta inválido.");
        
        $cols = $db->query("SHOW COLUMNS FROM formulario_perguntas LIKE 'grid_col'")->fetch();
        if ($cols) {
            $stmt = $db->prepare("UPDATE formulario_perguntas SET grid_col = ?, grid_row = ?, grid_colspan = ?, grid_rowspan = ?, data_atualizacao = NOW() WHERE id = ? AND formulario_id = ?");
            $stmt->execute([$gridCol, $gridRow, $colspan, $rowspan, $perguntaId, $form_id]);
        }
        echo json_encode(['sucesso' => true, 'mensagem' => 'Posição atualizada!']);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
    }
    exit;
}

// ==================== EXCLUSÃO DE PERGUNTA ====================
if (isset($_GET['excluir'])) {
    $perguntaId = (int)$_GET['excluir'];
    try {
        $stmt = $db->prepare("SELECT titulo FROM formulario_perguntas WHERE id = ? AND formulario_id = ?");
        $stmt->execute([$perguntaId, $form_id]);
        $pergunta = $stmt->fetch();
        if (!$pergunta) throw new Exception("Pergunta não encontrada.");
        $db->beginTransaction();
        $stmt = $db->prepare("DELETE FROM formulario_perguntas WHERE id = ?");
        $stmt->execute([$perguntaId]);
        $db->commit();
        setMensagem('Pergunta "' . htmlspecialchars($pergunta['titulo']) . '" excluída com sucesso!');
    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        setMensagem('Não foi possível excluir a pergunta. ' . $e->getMessage(), 'erro');
    }
    header("Location: construtor_forms.php?form_id=$form_id");
    exit;
}

// ==================== BUSCA FORMULÁRIO ====================
$stmt = $db->prepare("SELECT * FROM formulario WHERE id = ?");
$stmt->execute([$form_id]);
$formulario = $stmt->fetch();
if (!$formulario) {
    setMensagem('Formulário não encontrado.', 'erro');
    header("Location: index.php");
    exit;
}

// ==================== ADIÇÃO DE PERGUNTA ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo']) && !isset($_POST['acao'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo_input = $_POST['tipo_input'] ?? 'texto';
    $nome_unico = trim($_POST['nome_unico'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $placeholder = trim($_POST['placeholder'] ?? '');
    $tamanho_maximo = (int)($_POST['tamanho_maximo'] ?? 255);
    $obrigatorio = (int)($_POST['obrigatorio'] ?? 0);
    $multipla_escolha = (int)($_POST['multipla_escolha'] ?? 0);
    $opcoes = null;

    $grid_col = max(1, min(3, (int)($_POST['grid_col'] ?? 1)));
    $grid_row = max(1, (int)($_POST['grid_row'] ?? 1)));
    $grid_colspan = max(1, min(3, (int)($_POST['grid_colspan'] ?? 1)));
    $grid_rowspan = max(1, min(5, (int)($_POST['grid_rowspan'] ?? 1)));

    if (empty($titulo)) {
        setMensagem('Título da pergunta é obrigatório.', 'erro');
    } else {
        try {
            if (empty($nome_unico)) {
                $nome_unico = preg_replace('/[^a-z0-9_]/', '_', strtolower($titulo));
                $nome_unico = substr($nome_unico, 0, 50);
                if (empty($nome_unico)) $nome_unico = 'campo_' . time();
            }

            if ($tipo_input === 'tabela') {
                $linhas = explode(',', trim($_POST['linhas_tabela'] ?? ''));
                $colunas = explode(',', trim($_POST['colunas_tabela'] ?? ''));
                $linhas = array_filter(array_map('trim', $linhas));
                $colunas = array_filter(array_map('trim', $colunas));
                if (empty($linhas) || empty($colunas)) {
                    setMensagem('Linhas e colunas da tabela são obrigatórias.', 'erro');
                    header("Location: construtor_forms.php?form_id=$form_id");
                    exit;
                }
                $opcoes = json_encode(['linhas' => $linhas, 'colunas' => $colunas], JSON_UNESCAPED_UNICODE);
            }
            elseif ($tipo_input === 'sim_nao_justificativa') {
                $justificativaCondicao = $_POST['justificativa_condicao'] ?? 'nao';
                $placeholderJustificativa = trim($_POST['placeholder_justificativa'] ?? 'Justifique');
                $opcoes = json_encode(['condicao' => $justificativaCondicao, 'placeholder' => $placeholderJustificativa], JSON_UNESCAPED_UNICODE);
            }
            elseif (in_array($tipo_input, ['radio', 'checkbox', 'select'])) {
                $opcoesRaw = trim($_POST['opcoes'] ?? '');
                if (!empty($opcoesRaw)) {
                    $opcoesArray = array_filter(array_map('trim', explode(',', $opcoesRaw)));
                    $opcoes = json_encode($opcoesArray, JSON_UNESCAPED_UNICODE);
                }
            }

            $cols = $db->query("SHOW COLUMNS FROM formulario_perguntas LIKE 'grid_col'")->fetch();
            if ($cols) {
                $sql = "INSERT INTO formulario_perguntas (
                    formulario_id, nome_unico, titulo, descricao, tipo_input, opcoes,
                    obrigatorio, multipla_escolha, tamanho_maximo, placeholder,
                    ativo, ordem, grid_col, grid_row, grid_colspan, grid_rowspan, data_criacao, data_atualizacao
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $form_id, $nome_unico, $titulo, $descricao, $tipo_input, $opcoes,
                    $obrigatorio, $multipla_escolha, $tamanho_maximo, $placeholder,
                    1, 0, $grid_col, $grid_row, $grid_colspan, $grid_rowspan
                ]);
            } else {
                $sql = "INSERT INTO formulario_perguntas (
                    formulario_id, nome_unico, titulo, descricao, tipo_input, opcoes,
                    obrigatorio, multipla_escolha, tamanho_maximo, placeholder,
                    ativo, ordem, data_criacao, data_atualizacao
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $form_id, $nome_unico, $titulo, $descricao, $tipo_input, $opcoes,
                    $obrigatorio, $multipla_escolha, $tamanho_maximo, $placeholder,
                    1, 0
                ]);
            }
            setMensagem('Pergunta adicionada com sucesso!');
        } catch (Exception $e) {
            $msgErro = $e->getMessage();
            if (strpos($msgErro, 'Duplicate entry') !== false && strpos($msgErro, 'unique_nome_unico_formulario') !== false) {
                setMensagem('Já existe uma pergunta com este nome único.', 'erro');
            } else {
                setMensagem('Erro ao salvar: ' . htmlspecialchars($msgErro), 'erro');
            }
        }
    }
    header("Location: construtor_forms.php?form_id=$form_id");
    exit;
}

// ==================== LISTAGEM DE PERGUNTAS ====================
$cols = $db->query("SHOW COLUMNS FROM formulario_perguntas LIKE 'grid_col'")->fetch();
if ($cols) {
    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? ORDER BY grid_row ASC, grid_col ASC");
} else {
    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? ORDER BY ordem ASC");
}
$stmt->execute([$form_id]);
$perguntas = $stmt->fetchAll();
$hasGrid = (bool)$cols;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Construtor de Formulários</title>
    <link rel="stylesheet" href="construtor_forms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        .question-item { background: #f9f8ff; border-left: 4px solid var(--cor-primaria); padding: 16px; margin: 12px 0; border-radius: 8px; position: relative; }
        .ordem-badge { position: absolute; top: 10px; right: 10px; background: var(--cor-primaria); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; }
        .action-buttons { display: flex; gap: 8px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee; }
        .btn-action { background: #f0f0ff; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .btn-action.edit { background: #fff3cd; color: #856404; }
        .btn-action.edit:hover { background: #ffc107; color: white; transform: scale(1.1); }
        .btn-action.delete { background: #ffebee; color: var(--cor-erro); }
        .btn-action.delete:hover { background: var(--cor-erro); color: white; transform: scale(1.1); }
        .ajax-message { position: fixed; top: 20px; right: 20px; z-index: 10000; max-width: 400px; animation: slideIn 0.3s ease, fadeOut 0.5s ease 2.5s forwards; }
        @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; transform: translateX(400px); } }
        .grid-fields-container { background: #f0f0ff; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px dashed var(--cor-primaria); }
        .grid-info { font-size: 0.85em; color: var(--cor-secundaria); margin-top: 5px; }
        .questions-grid-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 20px; background: #f4f4fa; border-radius: 16px; min-height: 100px; }
        .question-item.grid-mode { margin: 0; cursor: grab; display: flex; flex-direction: column; justify-content: space-between; height: 100%; background: white; }
        .question-item.grid-mode:active { cursor: grabbing; }
        .question-item.sortable-ghost { opacity: 0.4; background: #e1e1ff; border: 2px dashed var(--cor-primaria); }
        .question-item.sortable-drag { opacity: 0.9; box-shadow: 0 15px 30px rgba(0,0,0,0.2); transform: scale(1.02); z-index: 1000; }
        .col-span-2 { grid-column: span 2; } .col-span-3 { grid-column: span 3; }
        .row-span-2 { grid-row: span 2; } .row-span-3 { grid-row: span 3; }
        
        /* Modal de Edição */
        .modal-edit { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 3000; }
        .modal-edit.active { display: flex; }
        .modal-edit-content { background: white; border-radius: 16px; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal-edit-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; z-index: 10; }
        .modal-edit-header h3 { margin: 0; color: var(--cor-primaria); }
        .modal-edit-close { font-size: 28px; cursor: pointer; color: #999; transition: color 0.2s; }
        .modal-edit-close:hover { color: var(--cor-texto); }
        .modal-edit-body { padding: 20px; }
        .modal-edit-footer { padding: 20px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 12px; position: sticky; bottom: 0; background: white; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $mensagem['tipo'] === 'erro' ? 'erro' : 'sucesso' ?>"><?= htmlspecialchars($mensagem['texto']) ?></div>
        <?php endif; ?>
        
        <h2>Construtor: <?= htmlspecialchars($formulario['nome']) ?> (ID: <?= $form_id ?>)</h2>
        <p>
            <a href="index.php" class="btn-secundario">Voltar</a>
            <a href="render_forms.php?form_id=<?= $form_id ?>&from=construtor" class="btn-secundario" style="margin-left:12px;">Visualizar Formulário</a>
        </p>

        <!-- FORMULÁRIO DE ADIÇÃO -->
        <form method="POST" id="form-adicionar">
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
                        <option value="number">Número</option>
                        <?php if ($formulario['s_n_anexo'] === 'S'): ?><option value="file">Anexo de Arquivo</option><?php endif; ?>
                        <option value="tabela">Tabela de Opções</option>
                        <option value="sim_nao_justificativa">Sim/Não com Justificativa</option>
                    </select>
                </div>
            </div>

            <!-- Opções para radio/checkbox/select -->
            <div class="form-row" id="opcoes-container" style="display:none;">
                <div class="form-group">
                    <label for="opcoes">Opções (separadas por vírgula)</label>
                    <input type="text" id="opcoes" name="opcoes" maxlength="1000" placeholder="Ex: Sim,Não,Talvez">
                </div>
            </div>

            <!-- Configuração da justificativa -->
            <div class="form-row" id="justificativa-container" style="display:none;">
                <div class="form-group">
                    <label for="justificativa_condicao">Exibir justificativa quando:</label>
                    <select id="justificativa_condicao" name="justificativa_condicao">
                        <option value="sim">Sim</option><option value="nao">Não</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="placeholder_justificativa">Placeholder da justificativa</label>
                    <input type="text" id="placeholder_justificativa" name="placeholder_justificativa" placeholder="Justifique..." maxlength="100">
                </div>
            </div>

            <!-- Linhas e Colunas para Tabela -->
            <div class="form-row" id="tabela-container" style="display:none;">
                <div class="form-group">
                    <label for="linhas_tabela">Itens (separados por vírgula)</label>
                    <input type="text" id="linhas_tabela" name="linhas_tabela" maxlength="1000" placeholder="Item1,Item2,Item3">
                </div>
                <div class="form-group">
                    <label for="colunas_tabela">Opções (separadas por vírgula)</label>
                    <input type="text" id="colunas_tabela" name="colunas_tabela" maxlength="500" placeholder="Sim,Não">
                </div>
            </div>

            <!-- Campos comuns -->
            <div class="form-row">
                <div class="form-group"><label for="descricao">Descrição / Ajuda</label><input type="text" id="descricao" name="descricao" maxlength="255" placeholder="Ex: Avalie o risco"></div>
            </div>

            <!-- Placeholder e tamanho -->
            <div class="form-row" id="texto-container" style="display:none;">
                <div class="form-group"><label for="placeholder">Placeholder</label><input type="text" id="placeholder" name="placeholder" maxlength="100" placeholder="Digite aqui..."></div>
                <div class="form-group"><label for="tamanho_maximo">Tamanho Máximo</label><input type="number" id="tamanho_maximo" name="tamanho_maximo" min="1" max="10000" value="255"></div>
            </div>

            <!-- Configuração de Grid (se existir no banco) -->
            <?php if ($hasGrid): ?>
            <div class="grid-fields-container">
                <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                    <div class="form-group"><label for="grid_col">Coluna (1-3)</label><select id="grid_col" name="grid_col" required><option value="1">1ª</option><option value="2">2ª</option><option value="3">3ª</option></select></div>
                    <div class="form-group"><label for="grid_row">Linha</label><input type="number" id="grid_row" name="grid_row" min="1" value="1" required></div>
                    <div class="form-group"><label for="grid_colspan">Colspan</label><select id="grid_colspan" name="grid_colspan" required><option value="1">1</option><option value="2">2</option><option value="3">3</option></select></div>
                    <div class="form-group"><label for="grid_rowspan">Rowspan</label><select id="grid_rowspan" name="grid_rowspan" required><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div>
                </div>
                <div class="grid-info"><i class="fas fa-info-circle"></i> Use Colspan 3 para títulos. Arraste para reordenar.</div>
            </div>
            <?php endif; ?>

            <!-- Obrigatório e Múltipla Escolha -->
            <div class="form-row">
                <div class="form-group"><label for="obrigatorio">Obrigatório?</label><select id="obrigatorio" name="obrigatorio"><option value="1">Sim</option><option value="0">Não</option></select></div>
                <div class="form-group" id="multipla-container" style="display:none;"><label for="multipla_escolha">Múltipla Escolha?</label><select id="multipla_escolha" name="multipla_escolha"><option value="0">Não</option><option value="1">Sim</option></select></div>
            </div>

            <button type="submit" class="btn">Adicionar Pergunta</button>
        </form>

        <hr>
        <h3>Perguntas Cadastradas (<?= count($perguntas) ?>)</h3>

        <?php if ($perguntas): ?>
            <div id="questions-list" class="<?= $hasGrid ? 'questions-grid-container' : '' ?>">
                <?php foreach ($perguntas as $p): ?>
                    <div class="question-item <?= $hasGrid ? 'grid-mode' : '' ?>" data-id="<?= $p['id'] ?>" 
                         <?= $hasGrid ? 'data-col="'.($p['grid_col'] ?? 1).'" data-row="'.($p['grid_row'] ?? 1).'"' : '' ?>
                         <?= $hasGrid ? 'data-colspan="'.($p['grid_colspan'] ?? 1).'" data-rowspan="'.($p['grid_rowspan'] ?? 1).'"' : '' ?>>
                        <span class="ordem-badge"><?= $hasGrid ? 'L'.($p['grid_row'] ?? 1) : ($p['ordem'] ?? '?') ?></span>
                        <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                        <br><small>Tipo: <?= htmlspecialchars($p['tipo_input']) ?></small>
                        <?php if (!empty($p['descricao'])): ?><br><small>Descrição: <?= htmlspecialchars($p['descricao']) ?></small><?php endif; ?>
                        <?php if (!is_null($p['opcoes']) && $p['opcoes'] !== 'null' && !in_array($p['tipo_input'], ['tabela','sim_nao_justificativa'])): ?><?php $op = json_decode($p['opcoes'], true); if (is_array($op)): ?><br><small>Opções: <?= count($op) ?></small><?php endif; ?><?php endif; ?>
                        <br><small>Obrigatório: <?= $p['obrigatorio'] ? 'Sim' : 'Não' ?></small>
                        <div class="action-buttons">
                            <button class="btn-action edit" title="Editar pergunta" onclick="abrirModalEditar(<?= $p['id'] ?>)"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" title="Excluir pergunta" onclick="abrirModalExclusao(<?= $form_id ?>, <?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['titulo'])) ?>')"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">Nenhuma pergunta cadastrada ainda.</div>
        <?php endif; ?>
    </div>

    <!-- Modal de Exclusão -->
    <div id="modal-exclusao" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header"><h3>Confirmar Exclusão</h3><span class="close-modal" onclick="fecharModalExclusao()">&times;</span></div>
            <div class="modal-body"><p>Excluir: <strong id="titulo-excluir"></strong></p><p><strong>Esta ação não pode ser desfeita.</strong></p></div>
            <div class="modal-footer"><button class="btn-cancel" onclick="fecharModalExclusao()">Cancelar</button><a href="#" id="btn-confirmar-exclusao" class="btn-delete">Excluir</a></div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="modal-editar" class="modal-edit">
        <div class="modal-edit-content">
            <div class="modal-edit-header">
                <h3>✏️ Editar Pergunta</h3>
                <span class="modal-edit-close" onclick="fecharModalEditar()">&times;</span>
            </div>
            <div class="modal-edit-body">
                <form id="form-editar-pergunta">
                    <input type="hidden" id="editar-pergunta-id" name="pergunta_id">
                    <input type="hidden" name="acao" value="salvar_edicao">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-titulo">Título da Pergunta *</label>
                            <input type="text" id="editar-titulo" name="titulo" required maxlength="200">
                        </div>
                        <div class="form-group">
                            <label for="editar-descricao">Descrição / Ajuda</label>
                            <input type="text" id="editar-descricao" name="descricao" maxlength="255">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-placeholder">Placeholder</label>
                            <input type="text" id="editar-placeholder" name="placeholder" maxlength="200">
                        </div>
                        <div class="form-group">
                            <label for="editar-tamanho-maximo">Tamanho Máximo</label>
                            <input type="number" id="editar-tamanho-maximo" name="tamanho_maximo" min="1" max="10000" value="255">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-obrigatorio">Obrigatório?</label>
                            <select id="editar-obrigatorio" name="obrigatorio">
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                        <?php if ($hasGrid): ?>
                        <div class="form-group">
                            <label for="editar-grid-col">Coluna (1-3)</label>
                            <select id="editar-grid-col" name="grid_col">
                                <option value="1">1ª Coluna</option>
                                <option value="2">2ª Coluna</option>
                                <option value="3">3ª Coluna</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editar-grid-row">Linha</label>
                            <input type="number" id="editar-grid-row" name="grid_row" min="1" value="1">
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($hasGrid): ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editar-grid-colspan">Colspan</label>
                            <select id="editar-grid-colspan" name="grid_colspan">
                                <option value="1">1 Coluna</option>
                                <option value="2">2 Colunas</option>
                                <option value="3">3 Colunas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editar-grid-rowspan">Rowspan</label>
                            <select id="editar-grid-rowspan" name="grid_rowspan">
                                <option value="1">1 Linha</option>
                                <option value="2">2 Linhas</option>
                                <option value="3">3 Linhas</option>
                                <option value="4">4 Linhas</option>
                                <option value="5">5 Linhas</option>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="modal-edit-footer">
                <button class="btn-cancel" onclick="fecharModalEditar()">Cancelar</button>
                <button class="btn-submit" onclick="salvarEdicaoPergunta()">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
            </div>
        </div>
    </div>

    <script>
    // === CONTROLES DINÂMICOS DO FORMULÁRIO DE ADIÇÃO ===
    document.addEventListener('DOMContentLoaded', function() {
        const tipoInput = document.getElementById('tipo_input');
        if (tipoInput) {
            tipoInput.addEventListener('change', function() {
                const t = this.value;
                document.getElementById('opcoes-container').style.display = ['radio','checkbox','select'].includes(t) ? 'block' : 'none';
                document.getElementById('justificativa-container').style.display = t === 'sim_nao_justificativa' ? 'block' : 'none';
                document.getElementById('tabela-container').style.display = t === 'tabela' ? 'block' : 'none';
                document.getElementById('texto-container').style.display = ['texto','textarea','number'].includes(t) ? 'block' : 'none';
                document.getElementById('multipla-container').style.display = t === 'checkbox' ? 'block' : 'none';
            });
            tipoInput.dispatchEvent(new Event('change'));
        }
    });

    // === MODAL DE EXCLUSÃO ===
    function abrirModalExclusao(formId, perguntaId, titulo) {
        document.getElementById('titulo-excluir').textContent = titulo;
        document.getElementById('btn-confirmar-exclusao').href = '?form_id=' + formId + '&excluir=' + perguntaId;
        document.getElementById('modal-exclusao').style.display = 'flex';
    }
    function fecharModalExclusao() { document.getElementById('modal-exclusao').style.display = 'none'; }
    document.addEventListener('click', function(e) { const m = document.getElementById('modal-exclusao'); if (m && e.target === m) fecharModalExclusao(); });

    // === MODAL DE EDIÇÃO ===
    async function abrirModalEditar(perguntaId) {
        try {
            const res = await fetch(`?form_id=<?= $form_id ?>&acao=editar_dados&pergunta_id=${perguntaId}`);
            const data = await res.json();
            if (!data.sucesso) throw new Error(data.erro);
            
            const p = data.dados;
            document.getElementById('editar-pergunta-id').value = p.id;
            document.getElementById('editar-titulo').value = p.titulo;
            document.getElementById('editar-descricao').value = p.descricao || '';
            document.getElementById('editar-placeholder').value = p.placeholder || '';
            document.getElementById('editar-tamanho-maximo').value = p.tamanho_maximo || 255;
            document.getElementById('editar-obrigatorio').value = p.obrigatorio ? '1' : '0';
            
            <?php if ($hasGrid): ?>
            document.getElementById('editar-grid-col').value = p.grid_col || 1;
            document.getElementById('editar-grid-row').value = p.grid_row || 1;
            document.getElementById('editar-grid-colspan').value = p.grid_colspan || 1;
            document.getElementById('editar-grid-rowspan').value = p.grid_rowspan || 1;
            <?php endif; ?>
            
            document.getElementById('modal-editar').classList.add('active');
        } catch (err) {
            mostrarMensagem('error', 'Erro ao carregar: ' + err.message);
        }
    }
    
    function fecharModalEditar() { document.getElementById('modal-editar').classList.remove('active'); }
    
    async function salvarEdicaoPergunta() {
        const form = document.getElementById('form-editar-pergunta');
        const formData = new FormData(form);
        try {
            const res = await fetch('', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.sucesso) {
                mostrarMensagem('success', data.mensagem);
                fecharModalEditar();
                setTimeout(() => location.reload(), 1000);
            } else {
                mostrarMensagem('error', data.erro || 'Erro ao salvar');
            }
        } catch (err) {
            mostrarMensagem('error', 'Erro: ' + err.message);
        }
    }
    
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modal-editar');
        if (modal && e.target === modal) fecharModalEditar();
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { fecharModalEditar(); fecharModalExclusao(); }
    });

    // === MENSAGENS AJAX ===
    function mostrarMensagem(tipo, texto) {
        document.querySelectorAll('.ajax-message').forEach(m => m.remove());
        const msg = document.createElement('div');
        msg.className = `form-message ${tipo==='success'?'success':'error'} ajax-message`;
        msg.innerHTML = texto;
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 3000);
    }

    // === DRAG & DROP (se grid existir) ===
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('questions-list');
        <?php if ($hasGrid): ?>
        if (list && window.Sortable) {
            new Sortable(list, {
                animation: 150, ghostClass: 'sortable-ghost', dragClass: 'sortable-drag',
                onStart: e => e.item.classList.add('dragging'),
                onEnd: async function(e) {
                    e.item.classList.remove('dragging');
                    const item = e.item, idx = e.newIndex, id = item.dataset.id;
                    if (!id) return;
                    const col = (idx % 3) + 1, row = Math.floor(idx / 3) + 1;
                    item.dataset.col = col; item.dataset.row = row;
                    
                    const formData = new FormData();
                    formData.append('acao', 'atualizar_posicao_grid');
                    formData.append('pergunta_id', id);
                    formData.append('grid_col', col);
                    formData.append('grid_row', row);
                    formData.append('grid_colspan', item.dataset.colspan || 1);
                    formData.append('grid_rowspan', item.dataset.rowspan || 1);
                    
                    try {
                        const res = await fetch('', { method: 'POST', body: formData });
                        const data = await res.json();
                        if (data.sucesso) {
                            mostrarMensagem('success', 'Posição: C'+col+'/L'+row);
                            item.querySelector('.ordem-badge').textContent = 'L'+row;
                        } else {
                            mostrarMensagem('error', data.erro);
                            setTimeout(() => location.reload(), 1500);
                        }
                    } catch (err) {
                        mostrarMensagem('error', 'Erro: '+err.message);
                    }
                }
            });
        }
        <?php endif; ?>
    });
    </script>
    <script src="construtor_forms.js"></script>
</body>
</html>
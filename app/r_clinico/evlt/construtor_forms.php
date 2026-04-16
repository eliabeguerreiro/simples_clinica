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

// ==================== ATUALIZAR POSIÇÃO GRID (AJAX) ====================
if (isset($_POST['acao']) && $_POST['acao'] === 'atualizar_posicao_grid') {
    header('Content-Type: application/json');
    
    try {
        $perguntaId = (int)($_POST['pergunta_id'] ?? 0);
        $gridCol = max(1, min(3, (int)($_POST['grid_col'] ?? 1)));
        $gridRow = max(1, (int)($_POST['grid_row'] ?? 1));
        $colspan = max(1, min(3, (int)($_POST['grid_colspan'] ?? 1)));
        $rowspan = max(1, min(5, (int)($_POST['grid_rowspan'] ?? 1)));
        
        if ($perguntaId <= 0) throw new Exception("ID da pergunta inválido.");
        
        $stmt = $db->prepare("
            UPDATE formulario_perguntas 
            SET grid_col = ?, grid_row = ?, grid_colspan = ?, grid_rowspan = ?, use_grid = 1, data_atualizacao = NOW()
            WHERE id = ? AND formulario_id = ?
        ");
        $stmt->execute([$gridCol, $gridRow, $colspan, $rowspan, $perguntaId, $form_id]);
        
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $tipo_input = $_POST['tipo_input'] ?? 'texto';
    $nome_unico = trim($_POST['nome_unico'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $placeholder = trim($_POST['placeholder'] ?? '');
    $tamanho_maximo = (int)($_POST['tamanho_maximo'] ?? 255);
    $obrigatorio = (int)($_POST['obrigatorio'] ?? 0);
    $multipla_escolha = (int)($_POST['multipla_escolha'] ?? 0);
    $opcoes = null;

    // Campos de Grid (sempre ativos)
    $grid_col = max(1, min(3, (int)($_POST['grid_col'] ?? 1)));
    $grid_row = max(1, (int)($_POST['grid_row'] ?? 1));
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

            // Tratamento para tipos especiais
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

            // INSERT com grid sempre ativo
            $sql = "INSERT INTO formulario_perguntas (
                formulario_id, nome_unico, titulo, descricao, tipo_input, opcoes,
                obrigatorio, multipla_escolha, tamanho_maximo, placeholder,
                ativo, ordem, use_grid, grid_col, grid_row, grid_colspan, grid_rowspan, data_criacao, data_atualizacao
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $form_id, $nome_unico, $titulo, $descricao, $tipo_input, $opcoes,
                $obrigatorio, $multipla_escolha, $tamanho_maximo, $placeholder,
                1, 0,  // ordem=0 (não usado), use_grid=1 (sempre)
                $grid_col, $grid_row, $grid_colspan, $grid_rowspan
            ]);

            setMensagem('Pergunta adicionada com sucesso!');
        } catch (Exception $e) {
            $msgErro = $e->getMessage();
            if (strpos($msgErro, 'Duplicate entry') !== false && strpos($msgErro, 'unique_nome_unico_formulario') !== false) {
                setMensagem('Já existe uma pergunta com este nome único neste formulário.', 'erro');
            } else {
                setMensagem('Erro ao salvar pergunta: ' . htmlspecialchars($msgErro), 'erro');
            }
        }
    }
    header("Location: construtor_forms.php?form_id=$form_id");
    exit;
}

// ==================== LISTAGEM DE PERGUNTAS (SEMPRE EM GRID) ====================
$stmt = $db->prepare("
    SELECT * FROM formulario_perguntas 
    WHERE formulario_id = ? 
    ORDER BY grid_row ASC, grid_col ASC
");
$stmt->execute([$form_id]);
$perguntas = $stmt->fetchAll();
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
        .question-item {
            background: #f9f8ff;
            border-left: 4px solid var(--cor-primaria);
            padding: 16px;
            margin: 12px 0;
            border-radius: 8px;
            position: relative;
        }
        .ordem-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--cor-primaria);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        .reorder-buttons {
            display: flex;
            gap: 6px;
            margin-top: 10px;
        }
        .btn-delete-small {
            background: #ffebee;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            color: var(--cor-erro);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .btn-delete-small:hover {
            background: var(--cor-erro);
            color: white;
            transform: scale(1.1);
        }
        .ajax-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
            animation: slideIn 0.3s ease, fadeOut 0.5s ease 2.5s forwards;
        }
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; transform: translateX(400px); }
        }
        .grid-fields-container {
            background: #f0f0ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px dashed var(--cor-primaria);
        }
        .grid-info {
            font-size: 0.85em;
            color: var(--cor-secundaria);
            margin-top: 5px;
        }
        /* Estilos para o container grid na listagem */
        .questions-grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
            background: #f4f4fa;
            border-radius: 16px;
            min-height: 100px;
        }
        .question-item.grid-mode {
            margin: 0;
            cursor: grab;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            background: white;
        }
        .question-item.grid-mode:active { cursor: grabbing; }
        .question-item.sortable-ghost { opacity: 0.4; background: #e1e1ff; border: 2px dashed var(--cor-primaria); }
        .question-item.sortable-drag { opacity: 0.9; box-shadow: 0 15px 30px rgba(0,0,0,0.2); transform: scale(1.02); z-index: 1000; }
        .col-span-2 { grid-column: span 2; }
        .col-span-3 { grid-column: span 3; }
        .row-span-2 { grid-row: span 2; }
        .row-span-3 { grid-row: span 3; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $mensagem['tipo'] === 'erro' ? 'erro' : 'sucesso' ?>">
                <?= htmlspecialchars($mensagem['texto']) ?>
            </div>
        <?php endif; ?>
        <h2>Construtor: <?= htmlspecialchars($formulario['nome']) ?> (ID: <?= $form_id ?>)</h2>
        <p>
            <a href="index.php" class="btn-secundario">Voltar</a>
            <a href="render_forms.php?form_id=<?= $form_id ?>&from=construtor" class="btn-secundario" style="margin-left:12px;">
                Visualizar Formulário
            </a>
        </p>

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
                        <option value="number">Número</option>
                        <?php if ($formulario['s_n_anexo'] === 'S'): ?>
                            <option value="file">Anexo de Arquivo</option>
                        <?php endif; ?>
                        <option value="tabela">Tabela de Opções (Grupos)</option>
                        <option value="sim_nao_justificativa">Sim/Não com Justificativa Condicionada</option>
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
                    <label for="justificativa_condicao">Exibir justificativa quando a resposta for:</label>
                    <select id="justificativa_condicao" name="justificativa_condicao">
                        <option value="sim">Sim</option>
                        <option value="nao">Não</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="placeholder_justificativa">Placeholder da justificativa</label>
                    <input type="text" id="placeholder_justificativa" name="placeholder_justificativa"
                           placeholder="Ex: Justifique por que não foi realizado..." maxlength="100">
                </div>
            </div>

            <!-- Linhas e Colunas para Tabela -->
            <div class="form-row" id="tabela-container" style="display:none;">
                <div class="form-group">
                    <label for="linhas_tabela">Itens (uma por linha, separados por vírgula)</label>
                    <input type="text" id="linhas_tabela" name="linhas_tabela" maxlength="1000" placeholder="MORO,SUÇÃO,GAG,PLANTAR">
                </div>
                <div class="form-group">
                    <label for="colunas_tabela">Opções (separadas por vírgula)</label>
                    <input type="text" id="colunas_tabela" name="colunas_tabela" maxlength="500" placeholder="SIM,NÃO">
                </div>
            </div>

            <!-- Campos comuns -->
            <div class="form-row">
                <div class="form-group">
                    <label for="descricao">Descrição / Ajuda</label>
                    <input type="text" id="descricao" name="descricao" maxlength="255" placeholder="Ex: Avalie o risco de queda">
                </div>
            </div>

            <!-- Placeholder e tamanho (só para texto, textarea, number) -->
            <div class="form-row" id="texto-container" style="display:none;">
                <div class="form-group">
                    <label for="placeholder">Placeholder</label>
                    <input type="text" id="placeholder" name="placeholder" maxlength="100" placeholder="Digite aqui...">
                </div>
                <div class="form-group">
                    <label for="tamanho_maximo">Tamanho Máximo (caract.)</label>
                    <input type="number" id="tamanho_maximo" name="tamanho_maximo" min="1" max="10000" value="255">
                </div>
            </div>

            <!-- Configuração de Grid (SEMPRE VISÍVEL) -->
            <div class="grid-fields-container">
                <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                    <div class="form-group">
                        <label for="grid_col">Coluna (1-3)</label>
                        <select id="grid_col" name="grid_col">
                            <option value="1">1ª</option>
                            <option value="2">2ª</option>
                            <option value="3">3ª</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="grid_row">Linha</label>
                        <input type="number" id="grid_row" name="grid_row" min="1" value="1">
                    </div>
                    <div class="form-group">
                        <label for="grid_colspan">Colspan</label>
                        <select id="grid_colspan" name="grid_colspan">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="grid_rowspan">Rowspan</label>
                        <select id="grid_rowspan" name="grid_rowspan">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
                <div class="grid-info">
                    <i class="fas fa-info-circle"></i> Use Colspan 3 para títulos de seção. Arraste as perguntas para reordenar visualmente.
                </div>
            </div>

            <!-- Obrigatório -->
            <div class="form-row">
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
            <div id="questions-list" class="questions-grid-container">
                <?php foreach ($perguntas as $index => $p): ?>
                    <div class="question-item grid-mode" data-id="<?= $p['id'] ?>" 
                         data-col="<?= $p['grid_col'] ?? 1 ?>" 
                         data-row="<?= $p['grid_row'] ?? 1 ?>" 
                         data-colspan="<?= $p['grid_colspan'] ?? 1 ?>" 
                         data-rowspan="<?= $p['grid_rowspan'] ?? 1 ?>">
                        
                        <span class="ordem-badge">L<?= $p['grid_row'] ?></span>
                        
                        <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                        <br><small>Tipo: <?= htmlspecialchars($p['tipo_input']) ?></small>
                        
                        <br><small style="color:var(--cor-primaria);">
                            <i class="fas fa-th"></i> C<?= $p['grid_col'] ?>/L<?= $p['grid_row'] ?> (<?= $p['grid_colspan'] ?>x<?= $p['grid_rowspan'] ?>)
                        </small>

                        <?php if (!empty($p['descricao'])): ?>
                            <br><small>Descrição: <?= htmlspecialchars($p['descricao']) ?></small>
                        <?php endif; ?>
                        
                        <br><small>Obrigatório: <?= $p['obrigatorio'] ? 'Sim' : 'Não' ?></small>
                        
                        <div class="reorder-buttons">
                            <a href="javascript:void(0)" 
                               class="btn-delete-small" 
                               title="Excluir pergunta"
                               onclick="abrirModalExclusaoPergunta(<?= $form_id ?>, <?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['titulo'])) ?>')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">Nenhuma pergunta cadastrada ainda. Adicione a primeira!</div>
        <?php endif; ?>
    </div>

    <!-- Modal de confirmação de exclusão -->
    <div id="modal-exclusao-pergunta" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmar Exclusão</h3>
                <span class="close-modal" onclick="fecharModalExclusaoPergunta()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Você tem certeza que deseja excluir a pergunta:</p>
                <p><strong id="titulo-pergunta-excluir"></strong></p>
                <p><strong>Esta ação não pode ser desfeita.</strong></p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="fecharModalExclusaoPergunta()">Cancelar</button>
                <a href="#" id="btn-confirmar-exclusao" class="btn-delete">Excluir</a>
            </div>
        </div>
    </div>

    <script>
    // Funções de modal
    function abrirModalExclusaoPergunta(formId, perguntaId, titulo) {
        document.getElementById('titulo-pergunta-excluir').textContent = titulo;
        document.getElementById('btn-confirmar-exclusao').href = '?form_id=' + formId + '&excluir=' + perguntaId;
        document.getElementById('modal-exclusao-pergunta').style.display = 'flex';
    }
    function fecharModalExclusaoPergunta() {
        document.getElementById('modal-exclusao-pergunta').style.display = 'none';
    }

    // Mensagens AJAX
    function mostrarMensagem(tipo, texto) {
        document.querySelectorAll('.ajax-message').forEach(msg => msg.remove());
        const mensagem = document.createElement('div');
        mensagem.className = `form-message ${tipo === 'success' ? 'success' : 'error'} ajax-message`;
        mensagem.innerHTML = texto;
        document.body.appendChild(mensagem);
        setTimeout(() => mensagem.remove(), 3000);
    }

    // Atualizar posição via AJAX após drag & drop
    function atualizarPosicaoGridServidor(perguntaId, gridCol, gridRow, itemElement) {
        const colspan = parseInt(itemElement.dataset.colspan) || 1;
        const rowspan = parseInt(itemElement.dataset.rowspan) || 1;
        const formData = new FormData();
        formData.append('acao', 'atualizar_posicao_grid');
        formData.append('pergunta_id', perguntaId);
        formData.append('grid_col', gridCol);
        formData.append('grid_row', gridRow);
        formData.append('grid_colspan', colspan);
        formData.append('grid_rowspan', rowspan);

        fetch('', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                mostrarMensagem('success', 'Posição atualizada: C' + gridCol + ' / L' + gridRow);
                const badge = itemElement.querySelector('.ordem-badge');
                if (badge) badge.textContent = 'L' + gridRow;
            } else {
                mostrarMensagem('error', data.erro || 'Erro ao atualizar');
                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(err => {
            mostrarMensagem('error', 'Erro: ' + err.message);
            setTimeout(() => location.reload(), 1500);
        });
    }

    // Inicialização do SortableJS para drag & drop
    document.addEventListener('DOMContentLoaded', function() {
        const questionsList = document.getElementById('questions-list');
        if (questionsList && window.Sortable) {
            new Sortable(questionsList, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onStart: function(evt) { evt.item.classList.add('dragging'); },
                onEnd: function(evt) {
                    evt.item.classList.remove('dragging');
                    const item = evt.item;
                    const newIndex = evt.newIndex;
                    const perguntaId = item.dataset.id;
                    if (!perguntaId) return;
                    
                    // Calcula coluna e linha baseado no índice (3 colunas)
                    const gridCol = (newIndex % 3) + 1;
                    const gridRow = Math.floor(newIndex / 3) + 1;
                    
                    item.dataset.col = gridCol;
                    item.dataset.row = gridRow;
                    atualizarPosicaoGridServidor(perguntaId, gridCol, gridRow, item);
                }
            });
        }

        // Controle de campos dinâmicos do formulário de adição
        const tipoInput = document.getElementById('tipo_input');
        if (tipoInput) {
            tipoInput.addEventListener('change', function() {
                const tipo = this.value;
                document.getElementById('opcoes-container').style.display = ['radio', 'checkbox', 'select'].includes(tipo) ? 'block' : 'none';
                document.getElementById('justificativa-container').style.display = tipo === 'sim_nao_justificativa' ? 'block' : 'none';
                document.getElementById('tabela-container').style.display = tipo === 'tabela' ? 'block' : 'none';
                document.getElementById('texto-container').style.display = ['texto', 'textarea', 'number'].includes(tipo) ? 'block' : 'none';
                document.getElementById('multipla-container').style.display = tipo === 'checkbox' ? 'block' : 'none';
            });
            tipoInput.dispatchEvent(new Event('change'));
        }

        // Fechar modal ao clicar fora ou ESC
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('modal-exclusao-pergunta');
            if (modal && e.target === modal) fecharModalExclusaoPergunta();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') fecharModalExclusaoPergunta();
        });
    });
    </script>
    <script src="construtor_forms.js"></script>
</body>
</html>
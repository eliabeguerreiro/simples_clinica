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

// ==================== REORDENAÇÃO DE PERGUNTA (AJAX) ====================
if (isset($_POST['acao']) && $_POST['acao'] === 'reordenar') {
    header('Content-Type: application/json');
    
    $perguntaId = (int)($_POST['pergunta_id'] ?? 0);
    $direcao = $_POST['direcao'] ?? 'subir';

    try {
        if ($perguntaId <= 0) throw new Exception("ID da pergunta inválido.");

        // Busca a pergunta atual
        $stmt = $db->prepare("SELECT ordem FROM formulario_perguntas WHERE id = ? AND formulario_id = ?");
        $stmt->execute([$perguntaId, $form_id]);
        $atual = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$atual) throw new Exception("Pergunta não encontrada.");

        $ordemAtual = (int)$atual['ordem'];
        $novaOrdem = $direcao === 'subir' ? $ordemAtual - 1 : $ordemAtual + 1;

        if ($novaOrdem < 1) {
            throw new Exception("Não é possível mover além da primeira posição.");
        }

        // Busca a pergunta vizinha na nova posição
        $stmt = $db->prepare("SELECT id FROM formulario_perguntas WHERE formulario_id = ? AND ordem = ? LIMIT 1");
        $stmt->execute([$form_id, $novaOrdem]);
        $vizinha = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vizinha) {
            // Troca as posições (atômico)
            $db->beginTransaction();
            $stmt = $db->prepare("UPDATE formulario_perguntas SET ordem = ? WHERE id = ?");
            $stmt->execute([$novaOrdem, $perguntaId]);
            $stmt->execute([$ordemAtual, $vizinha['id']]);
            $db->commit();
            echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta reordenada com sucesso!']);
        } else {
            // Move para posição vazia
            $stmt = $db->prepare("UPDATE formulario_perguntas SET ordem = ? WHERE id = ?");
            $stmt->execute([$novaOrdem, $perguntaId]);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta movida para posição ' . $novaOrdem . '.']);
        }
    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        echo json_encode(['sucesso' => false, 'erro' => 'Erro ao reordenar: ' . $e->getMessage()]);
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

        $stmt = $db->prepare("DELETE FROM formulario_perguntas WHERE id = ?");
        $stmt->execute([$perguntaId]);

        // Reordena as perguntas restantes
        $stmt = $db->prepare("SELECT id FROM formulario_perguntas WHERE formulario_id = ? ORDER BY ordem ASC");
        $stmt->execute([$form_id]);
        $perguntasRestantes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($perguntasRestantes as $novaOrdem => $idPergunta) {
            $stmt = $db->prepare("UPDATE formulario_perguntas SET ordem = ? WHERE id = ?");
            $stmt->execute([$novaOrdem + 1, $idPergunta]);
        }

        setMensagem('Pergunta "' . htmlspecialchars($pergunta['titulo']) . '" excluída com sucesso!');
    } catch (Exception $e) {
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
    $ordem = (int)($_POST['ordem'] ?? 0);
    $opcoes = null;

    if (empty($titulo)) {
        setMensagem('Título da pergunta é obrigatório.', 'erro');
    } else {
        try {
            if (empty($nome_unico)) {
                $nome_unico = preg_replace('/[^a-z0-9_]/', '_', strtolower($titulo));
                $nome_unico = substr($nome_unico, 0, 50);
                if (empty($nome_unico)) $nome_unico = 'campo_' . time();
            }

            // Calcula ordem automática se não informada (última posição + 1)
            if ($ordem <= 0) {
                $stmt = $db->prepare("SELECT MAX(ordem) AS max_ordem FROM formulario_perguntas WHERE formulario_id = ?");
                $stmt->execute([$form_id]);
                $max = $stmt->fetch(PDO::FETCH_ASSOC);
                $ordem = ($max['max_ordem'] ?? 0) + 1;
            }

            // Tratamento para tipo "tabela"
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
                $opcoes = json_encode([
                    'linhas' => $linhas,
                    'colunas' => $colunas
                ], JSON_UNESCAPED_UNICODE);
            }
            // Tratamento para tipo "sim_nao_justificativa"
            elseif ($tipo_input === 'sim_nao_justificativa') {
                $justificativaCondicao = $_POST['justificativa_condicao'] ?? 'nao';
                $placeholderJustificativa = trim($_POST['placeholder_justificativa'] ?? 'Justifique');
                $opcoes = json_encode([
                    'condicao' => $justificativaCondicao,
                    'placeholder' => $placeholderJustificativa
                ], JSON_UNESCAPED_UNICODE);
            }
            // Tratamento para tipos com opções
            elseif (in_array($tipo_input, ['radio', 'checkbox', 'select'])) {
                $opcoesRaw = trim($_POST['opcoes'] ?? '');
                if (!empty($opcoesRaw)) {
                    $opcoesArray = array_filter(array_map('trim', explode(',', $opcoesRaw)));
                    $opcoes = json_encode($opcoesArray, JSON_UNESCAPED_UNICODE);
                }
            }

            $sql = "INSERT INTO formulario_perguntas (
                formulario_id, nome_unico, titulo, descricao, tipo_input, opcoes,
                obrigatorio, multipla_escolha, tamanho_maximo, placeholder,
                ativo, ordem, data_criacao, data_atualizacao
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
                1,
                $ordem
            ]);

            setMensagem('Pergunta adicionada com sucesso na posição ' . $ordem . '!');
        } catch (Exception $e) {
            $msgErro = $e->getMessage();
            if (strpos($msgErro, 'Duplicate entry') !== false && strpos($msgErro, 'unique_nome_unico_formulario') !== false) {
                setMensagem('Já existe uma pergunta com este nome único neste formulário. Escolha outro nome.', 'erro');
            } else {
                setMensagem('Erro ao salvar pergunta: ' . htmlspecialchars($msgErro), 'erro');
            }
        }
    }
    header("Location: construtor_forms.php?form_id=$form_id");
    exit;
}

// ==================== LISTAGEM DE PERGUNTAS (ORDENADAS) ====================
$stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? ORDER BY ordem ASC");
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
        .btn-reorder {
            background: #e1e1ff;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            color: var(--cor-primaria);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .btn-reorder:hover {
            background: var(--cor-primaria);
            color: white;
            transform: scale(1.1);
        }
        .btn-reorder:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .form-row-ordem {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
            <a href="render_forms.php?form_id=<?= $form_id ?>" class="btn-secundario" style="margin-left:12px;">
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

            <!-- Ordem -->
            <div class="form-row-ordem">
                <div class="form-group">
                    <label for="ordem">Posição na Lista</label>
                    <input type="number" id="ordem" name="ordem" min="1" value="<?= count($perguntas) + 1 ?>" placeholder="Ex: 1">
                    <small style="color:#666; display:block; margin-top:4px;">Deixe em branco para adicionar no final</small>
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
            <?php foreach ($perguntas as $index => $p): ?>
                <div class="question-item" data-id="<?= $p['id'] ?>">
                    <span class="ordem-badge"><?= $p['ordem'] ?></span>
                    <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                    <br><small>Tipo: <?= htmlspecialchars($p['tipo_input']) ?></small>
                    <?php if (!empty($p['descricao'])): ?>
                        <br><small>Descrição: <?= htmlspecialchars($p['descricao']) ?></small>
                    <?php endif; ?>
                    <?php if ($p['tipo_input'] === 'sim_nao_justificativa' && !is_null($p['opcoes']) && $p['opcoes'] !== 'null'): ?>
                        <?php
                        $dados = json_decode($p['opcoes'], true);
                        if (is_array($dados)):
                        ?>
                            <br><small>Justificativa em: <?= $dados['condicao'] === 'sim' ? 'Sim' : 'Não' ?></small>
                            <br><small>Placeholder: <?= htmlspecialchars($dados['placeholder']) ?></small>
                        <?php endif; ?>
                    <?php elseif ($p['tipo_input'] === 'tabela' && !is_null($p['opcoes']) && $p['opcoes'] !== 'null'): ?>
                        <?php
                        $dados = json_decode($p['opcoes'], true);
                        if (is_array($dados)):
                        ?>
                            <br><small>Itens: <?= htmlspecialchars(implode(', ', $dados['linhas'] ?? [])) ?></small>
                            <br><small>Opções: <?= htmlspecialchars(implode(', ', $dados['colunas'] ?? [])) ?></small>
                        <?php endif; ?>
                    <?php elseif (!is_null($p['opcoes']) && $p['opcoes'] !== 'null'): ?>
                        <?php
                        $opcoesArray = json_decode($p['opcoes'], true);
                        if (is_array($opcoesArray)):
                        ?>
                            <br><small>Opções: <?= htmlspecialchars(implode(', ', $opcoesArray)) ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                    <br><small>Obrigatório: <?= $p['obrigatorio'] ? 'Sim' : 'Não' ?></small>
                    <div class="reorder-buttons">
                        <?php if ($p['ordem'] > 1): ?>
                            <button class="btn-reorder" title="Mover para cima" onclick="reordenarPergunta(<?= $p['id'] ?>, 'subir')">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                        <?php else: ?>
                            <button class="btn-reorder" disabled title="Já está na primeira posição">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($p['ordem'] < count($perguntas)): ?>
                            <button class="btn-reorder" title="Mover para baixo" onclick="reordenarPergunta(<?= $p['id'] ?>, 'descer')">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        <?php else: ?>
                            <button class="btn-reorder" disabled title="Já está na última posição">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        <?php endif; ?>
                        
                        <a href="javascript:void(0)" 
                           class="btn-delete-small" 
                           title="Excluir pergunta"
                           onclick="abrirModalExclusaoPergunta(<?= $form_id ?>, <?= $p['id'] ?>, '<?= addslashes(htmlspecialchars($p['titulo'])) ?>')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">Nenhuma pergunta cadastrada ainda.</div>
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
    function abrirModalExclusaoPergunta(formId, perguntaId, titulo) {
        document.getElementById('titulo-pergunta-excluir').textContent = titulo;
        document.getElementById('btn-confirmar-exclusao').href = 
            '?form_id=' + formId + '&excluir=' + perguntaId;
        document.getElementById('modal-exclusao-pergunta').style.display = 'flex';
    }

    function fecharModalExclusaoPergunta() {
        document.getElementById('modal-exclusao-pergunta').style.display = 'none';
    }

    function reordenarPergunta(perguntaId, direcao) {
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `acao=reordenar&pergunta_id=${perguntaId}&direcao=${direcao}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                mostrarMensagem('success', data.mensagem);
                setTimeout(() => location.reload(), 800);
            } else {
                mostrarMensagem('error', data.erro || 'Erro ao reordenar.');
            }
        })
        .catch(error => {
            mostrarMensagem('error', 'Erro na requisição: ' + error.message);
        });
    }

    function mostrarMensagem(tipo, texto) {
        // Remove mensagens antigas
        const mensagensAntigas = document.querySelectorAll('.ajax-message');
        mensagensAntigas.forEach(msg => msg.remove());
        
        // Cria nova mensagem
        const mensagem = document.createElement('div');
        mensagem.className = `form-message ${tipo === 'success' ? 'success' : 'error'} ajax-message`;
        mensagem.innerHTML = texto;
        document.body.appendChild(mensagem);
        
        // Remove após 3 segundos
        setTimeout(() => {
            mensagem.remove();
        }, 3000);
    }

    // Controle de campos dinâmicos
    document.addEventListener('DOMContentLoaded', function() {
        const tipoInput = document.getElementById('tipo_input');
        if (!tipoInput) return;
        
        tipoInput.addEventListener('change', function() {
            const tipo = this.value;
            document.getElementById('opcoes-container').style.display = 
                ['radio', 'checkbox', 'select'].includes(tipo) ? 'block' : 'none';
            document.getElementById('justificativa-container').style.display = 
                tipo === 'sim_nao_justificativa' ? 'block' : 'none';
            document.getElementById('tabela-container').style.display = 
                tipo === 'tabela' ? 'block' : 'none';
            document.getElementById('texto-container').style.display = 
                ['texto', 'textarea', 'number'].includes(tipo) ? 'block' : 'none';
            document.getElementById('multipla-container').style.display = 
                tipo === 'checkbox' ? 'block' : 'none';
        });
        
        // Dispara change inicial para configurar campos
        tipoInput.dispatchEvent(new Event('change'));
    });
    </script>
</body>
</html>
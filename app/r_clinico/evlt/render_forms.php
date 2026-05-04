<?php
session_start();

function getDbConnection()
{
    static $db = null;
    if ($db === null) {
        try {
            include "../../../classes/db.class.php";
            $db = DB::connect();
        } catch (Exception $e) {
            die("<h2>Erro de conexão</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
        }
    }
    return $db;
}

$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
$from = isset($_GET['from']);
$paciente_id = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : null;

if ($form_id <= 0) {
    die("<h2>Erro</h2><p>ID do formulário não especificado.</p>");
}

try {
    $db = getDbConnection();
    
    // Busca o formulário
    $stmt = $db->prepare("SELECT * FROM formulario WHERE id = ?");
    $stmt->execute([$form_id]);
    $formulario = $stmt->fetch();

    if (!$formulario) {
        die("<h2>Formulário não encontrado</h2>");
    }

    // =========================================================================
    // DETECÇÃO: É UM FORMULÁRIO DE "EVOLUÇÃO LIVRE"?
    // Critério: nome contém "Evolução Livre" (case-insensitive)
    // =========================================================================
    $is_evolucao_livre = stripos($formulario['nome'], 'Evolução Livre') !== false || 
                         stripos($formulario['nome'], 'Evolucao Livre') !== false;

    // =========================================================================
    // VERIFICA SE COLUNAS DE GRID EXISTEM NO BANCO (Fallback Seguro)
    // =========================================================================
    $cols = $db->query("SHOW COLUMNS FROM formulario_perguntas LIKE 'grid_col'")->fetch();
    $hasGridColumns = (bool)$cols;

    // =========================================================================
    // BUSCA PERGUNTAS (com ordenação adaptativa)
    // =========================================================================
    if ($hasGridColumns) {
        // Se colunas de grid existirem: ordena por grid_row, grid_col
        $stmt = $db->prepare("
            SELECT * FROM formulario_perguntas 
            WHERE formulario_id = ? AND ativo = 1 
            ORDER BY grid_row ASC, grid_col ASC
        ");
    } else {
        // Fallback: ordena por ordem tradicional
        $stmt = $db->prepare("
            SELECT * FROM formulario_perguntas 
            WHERE formulario_id = ? AND ativo = 1 
            ORDER BY ordem ASC
        ");
    }
    $stmt->execute([$form_id]);
    $perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se for Evolução Livre E não tiver perguntas configuradas, usa modo textarea único
    $usar_textarea_livre = $is_evolucao_livre && empty($perguntas);

} catch (Exception $e) {
    die("<h2>Erro ao carregar formulário</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($formulario['nome']) ?></title>
    <link rel="stylesheet" href="render_forms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        /* Estilo específico para Evolução Livre (textarea único) */
        .evolucao-livre-container {
            background: #f8f9ff;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border: 2px dashed #d1d1ff;
        }
        .evolucao-livre-container textarea {
            min-height: 300px;
            font-size: 15px;
            line-height: 1.6;
            resize: vertical;
        }
        .evolucao-livre-container .hint {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="form-container <?= $hasGridColumns ? 'grid-layout' : '' ?>">
        <div class="form-header">
            <h1><?= htmlspecialchars($formulario['nome']) ?></h1>
            <?php if (!empty($formulario['descricao'])): ?>
                <p><?= htmlspecialchars($formulario['descricao']) ?></p>
            <?php endif; ?>
        </div>

        <?php if ($paciente_id === null): ?>
            <div class="preview-mode">
                <i class="fas fa-eye"></i> Modo de Visualização (somente leitura)
            </div>
            <p>
                <?php if ($from): ?>
                    <a href="construtor_forms.php?form_id=<?= $form_id ?>" class="btn-secundario">voltar</a>
                <?php else: ?>
                    <a href="index.php" class="btn-secundario">Voltar</a>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <h2>Formulário de Evolução para Paciente ID: <?= $paciente_id ?></h2>
            <p>
                <a href="escolher_forms.php?paciente_id=<?= $paciente_id ?>" class="btn-secundario">Voltar</a>
            </p>
        <?php endif; ?>

        <form method="POST" action="salvar_resposta.php" enctype="multipart/form-data"
              <?= $paciente_id === null ? 'onsubmit="alert(\'Este é apenas um modo de visualização. Não é possível salvar.\'); return false;"' : '' ?>>
            
            <?php if ($paciente_id !== null): ?>
                <input type="hidden" name="formulario_id" value="<?= $form_id ?>">
                <input type="hidden" name="paciente_id" value="<?= $paciente_id ?>">
            <?php endif; ?>

            <!-- ==========================================================================
                 CASO 1: EVOLUÇÃO LIVRE (textarea único quando sem perguntas)
                 ========================================================================== -->
            <?php if ($usar_textarea_livre): ?>
                <div class="evolucao-livre-container">
                    <label class="required">
                        <i class="fas fa-edit"></i> Descreva a evolução clínica
                    </label>
                    <small>Registre livremente as observações, procedimentos e condutas realizadas.</small>
                    
                    <textarea name="evolucao_livre_texto" 
                              class="form-control" 
                              rows="15" 
                              required 
                              placeholder="Ex: Paciente compareceu para acompanhamento..."></textarea>
                    
                    <div class="hint">
                        <i class="fas fa-info-circle"></i> 
                        Dica: Você pode usar quebras de linha, listar procedimentos e descrever a evolução em texto livre.
                    </div>
                </div>

            <!-- ==========================================================================
                 CASO 2: FORMULÁRIO ESTRUTURADO (Grid ou Lista Vertical)
                 ========================================================================== -->
            <?php else: ?>
                <?php if ($hasGridColumns): ?>
                    <div class="fields-grid">
                <?php endif; ?>

                <?php foreach ($perguntas as $p): ?>
                    <?php
                    // Aplica classes de span APENAS se colunas de grid existirem no banco
                    $gridClasses = '';
                    if ($hasGridColumns) {
                        $colspan = isset($p['grid_colspan']) ? (int)$p['grid_colspan'] : 1;
                        $rowspan = isset($p['grid_rowspan']) ? (int)$p['grid_rowspan'] : 1;
                        
                        if ($colspan > 1) $gridClasses .= ' col-span-' . $colspan;
                        if ($rowspan > 1) $gridClasses .= ' row-span-' . $rowspan;
                    }
                    ?>

                    <div class="form-group <?= $gridClasses ?>" 
                         <?= $hasGridColumns ? 'data-grid-col="'.(isset($p['grid_col']) ? (int)$p['grid_col'] : 1).'"' : '' ?>
                         <?= $hasGridColumns ? 'data-grid-row="'.(isset($p['grid_row']) ? (int)$p['grid_row'] : 1).'"' : '' ?>>
                        
                        <label class="<?= $p['obrigatorio'] ? 'required' : '' ?>">
                            <?= htmlspecialchars($p['titulo']) ?>
                        </label>
                        
                        <?php if (!empty($p['descricao'])): ?>
                            <small><?= htmlspecialchars($p['descricao']) ?></small>
                        <?php endif; ?>

                        <?php
                        $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
                        $obrigatorio = $p['obrigatorio'] ? ' required' : '';
                        $placeholder = !empty($p['placeholder']) ? ' placeholder="' . htmlspecialchars($p['placeholder']) . '"' : '';
                        $tamanhoMax = $p['tamanho_maximo'] ?? 255;
                        ?>

                        <?php if ($p['tipo_input'] === 'texto'): ?>
                            <input type="text" name="<?= $nomeCampo ?>" class="form-control" maxlength="<?= $tamanhoMax ?>"<?= $placeholder ?><?= $obrigatorio ?>>

                        <?php elseif ($p['tipo_input'] === 'textarea'): ?>
                            <textarea name="<?= $nomeCampo ?>" class="form-control" rows="4" maxlength="<?= $tamanhoMax ?>"<?= $placeholder ?><?= $obrigatorio ?>></textarea>

                        <?php elseif ($p['tipo_input'] === 'number'): ?>
                            <input type="number" name="<?= $nomeCampo ?>" class="form-control"<?= $obrigatorio ?>>

                        <?php elseif ($p['tipo_input'] === 'date'): ?>
                            <input type="date" name="<?= $nomeCampo ?>" class="form-control"<?= $obrigatorio ?>>

                        <?php elseif ($p['tipo_input'] === 'file'): ?>
                            <input type="file" name="<?= $nomeCampo ?>" class="form-control" 
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.txt,.odt,.rtf,.xls,.xlsx,.ppt,.pptx"
                                   <?= $obrigatorio ?>>

                        <?php elseif (in_array($p['tipo_input'], ['radio', 'select'])):
                            $opcoes = [];
                            if (!is_null($p['opcoes']) && $p['opcoes'] !== 'null') {
                                $decoded = json_decode($p['opcoes'], true);
                                if (is_array($decoded)) $opcoes = $decoded;
                            }
                            ?>
                            <?php if ($p['tipo_input'] === 'select'): ?>
                                <select name="<?= $nomeCampo ?>" class="form-control"<?= $obrigatorio ?>>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($opcoes as $opcao): ?>
                                        <option value="<?= htmlspecialchars($opcao) ?>"><?= htmlspecialchars($opcao) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <div class="radio-group">
                                    <?php foreach ($opcoes as $opcao): ?>
                                        <div class="radio-item">
                                            <input type="radio" name="<?= $nomeCampo ?>" value="<?= htmlspecialchars($opcao) ?>" 
                                                   id="<?= $nomeCampo ?>_<?= md5($opcao) ?>"<?= $obrigatorio ?>>
                                            <label for="<?= $nomeCampo ?>_<?= md5($opcao) ?>"><?= htmlspecialchars($opcao) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        <?php elseif ($p['tipo_input'] === 'checkbox'):
                            $opcoes = [];
                            if (!is_null($p['opcoes']) && $p['opcoes'] !== 'null') {
                                $decoded = json_decode($p['opcoes'], true);
                                if (is_array($decoded)) $opcoes = $decoded;
                            }
                            ?>
                            <div class="checkbox-group">
                                <?php foreach ($opcoes as $opcao): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="<?= $nomeCampo ?>[]" value="<?= htmlspecialchars($opcao) ?>" 
                                               id="<?= $nomeCampo ?>_<?= md5($opcao) ?>">
                                        <label for="<?= $nomeCampo ?>_<?= md5($opcao) ?>"><?= htmlspecialchars($opcao) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php elseif ($p['tipo_input'] === 'sim_nao_justificativa'):
                            $config = json_decode($p['opcoes'], true);
                            $condicao = $config['condicao'] ?? 'nao';
                            $placeholderJust = $config['placeholder'] ?? 'Justifique';
                            $justNome = $nomeCampo . '_justificativa';
                            ?>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" name="<?= $nomeCampo ?>" value="sim" id="<?= $nomeCampo ?>_sim" <?= $obrigatorio ?>
                                           onchange="toggleJustificativa(this, '<?= $justNome ?>', '<?= $condicao ?>')">
                                    <label for="<?= $nomeCampo ?>_sim">Sim</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" name="<?= $nomeCampo ?>" value="nao" id="<?= $nomeCampo ?>_nao" <?= $obrigatorio ?>
                                           onchange="toggleJustificativa(this, '<?= $justNome ?>', '<?= $condicao ?>')">
                                    <label for="<?= $nomeCampo ?>_nao">Não</label>
                                </div>
                            </div>
                            <div id="<?= $justNome ?>_container" style="display:none; margin-top:12px;">
                                <textarea name="<?= $justNome ?>" class="form-control" rows="3" 
                                          placeholder="<?= htmlspecialchars($placeholderJust) ?>"></textarea>
                            </div>

                        <?php elseif ($p['tipo_input'] === 'tabela'):
                            $config = json_decode($p['opcoes'], true);
                            $linhas = $config['linhas'] ?? [];
                            $colunas = $config['colunas'] ?? [];
                            if (!empty($linhas) && !empty($colunas)):
                            ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <?php foreach ($colunas as $col): ?>
                                            <th><?= htmlspecialchars($col) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($linhas as $linha): ?>
                                        <tr>
                                            <td style="text-align: left; font-weight: bold;"><?= htmlspecialchars($linha) ?></td>
                                            <?php foreach ($colunas as $col): ?>
                                                <td>
                                                    <input type="radio" name="<?= $nomeCampo ?>[<?= urlencode($linha) ?>]" 
                                                           value="<?= htmlspecialchars($col) ?>" 
                                                           id="<?= $nomeCampo ?>_<?= md5($linha . $col) ?>"<?= $obrigatorio ?>>
                                                    <label for="<?= $nomeCampo ?>_<?= md5($linha . $col) ?>"></label>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                                <p style="color: #c62828;">Configuração inválida para tabela.</p>
                            <?php endif; ?>

                        <?php else: ?>
                            <input type="text" name="<?= $nomeCampo ?>" class="form-control"<?= $obrigatorio ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ($hasGridColumns): ?>
                    </div> <!-- Fecha .fields-grid -->
                <?php endif; ?>
            <?php endif; ?>

            <!-- Campo de observações (sempre disponível para paciente_id informado) -->
            <?php if ($paciente_id !== null): ?>
                <div class="form-group" style="margin-top: 30px; <?= $hasGridColumns ? 'grid-column: 1 / -1;' : '' ?>">
                    <label for="observacoes">Observações Complementares (opcional)</label>
                    <textarea name="observacoes" class="form-control" rows="3" placeholder="Adicione observações adicionais..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Salvar Evolução</button>
            <?php endif; ?>
        </form>
    </div>

    <script>
    function toggleJustificativa(radio, justificativaId, condicaoEsperada) {
        const container = document.getElementById(justificativaId + '_container');
        const valor = radio.value;
        const deveMostrar = (valor === 'sim' && condicaoEsperada === 'sim') ||
                            (valor === 'nao' && condicaoEsperada === 'nao');
        container.style.display = deveMostrar ? 'block' : 'none';
    }
    </script>
</body>
</html>
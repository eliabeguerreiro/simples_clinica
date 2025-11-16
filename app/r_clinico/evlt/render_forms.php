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
$paciente_id = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : null;

if ($form_id <= 0) {
    die("<h2>Erro</h2><p>ID do formulário não especificado.</p>");
}

try {
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT nome, descricao, especialidade FROM formulario WHERE id = ?");
    $stmt->execute([$form_id]);
    $formulario = $stmt->fetch();

    if (!$formulario) {
        die("<h2>Formulário não encontrado</h2>");
    }

    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? AND ativo = 1 ORDER BY id");
    $stmt->execute([$form_id]);
    $perguntas = $stmt->fetchAll();

    if (!$perguntas) {
        die("<h2>" . htmlspecialchars($formulario['nome']) . "</h2><p>Nenhuma pergunta configurada.</p>");
    }

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
</head>
<body>
    <div class="form-container">
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
                <a href="index.php" class="btn-secundario">Voltar</a>
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

            <?php foreach ($perguntas as $p): ?>
                <div class="form-group">
                    <label class="<?= $p['obrigatorio'] ? 'required' : '' ?>"><?= htmlspecialchars($p['titulo']) ?></label>
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
                        <input type="file" name="<?= $nomeCampo ?>" class="form-control"<?= $obrigatorio ?>>

                    <?php elseif (in_array($p['tipo_input'], ['radio', 'checkbox', 'select'])):
                        $opcoes = [];
                        if (!is_null($p['opcoes']) && $p['opcoes'] !== 'null') {
                            $decoded = json_decode($p['opcoes'], true);
                            if (is_array($decoded)) {
                                $opcoes = $decoded;
                            }
                        }
                        ?>

                        <?php if ($p['tipo_input'] === 'select'): ?>
                            <select name="<?= $nomeCampo ?>" class="form-control"<?= $obrigatorio ?>>
                                <option value="">Selecione...</option>
                                <?php foreach ($opcoes as $opcao): ?>
                                    <option value="<?= htmlspecialchars($opcao) ?>"><?= htmlspecialchars($opcao) ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php elseif ($p['tipo_input'] === 'radio'): ?>
                            <div class="radio-group">
                                <?php foreach ($opcoes as $opcao): ?>
                                    <div class="radio-item">
                                        <input type="radio" name="<?= $nomeCampo ?>" value="<?= htmlspecialchars($opcao) ?>" id="<?= $nomeCampo ?>_<?= md5($opcao) ?>"<?= $obrigatorio ?>>
                                        <label for="<?= $nomeCampo ?>_<?= md5($opcao) ?>"><?= htmlspecialchars($opcao) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php elseif ($p['tipo_input'] === 'checkbox'): ?>
                            <div class="checkbox-group">
                                <?php foreach ($opcoes as $opcao): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="<?= $nomeCampo ?>[]" value="<?= htmlspecialchars($opcao) ?>" id="<?= $nomeCampo ?>_<?= md5($opcao) ?>">
                                        <label for="<?= $nomeCampo ?>_<?= md5($opcao) ?>"><?= htmlspecialchars($opcao) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

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

            <?php if ($paciente_id !== null): ?>
                <div class="form-group">
                    <label for="observacoes">Observações (opcional)</label>
                    <textarea name="observacoes" class="form-control" rows="3" placeholder="Adicione observações clínicas..."></textarea>
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
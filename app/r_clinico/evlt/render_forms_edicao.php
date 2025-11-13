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

// Valida evolucao_id
$evolucao_id = isset($_GET['evolucao_id']) ? (int)$_GET['evolucao_id'] : 0;
if ($evolucao_id <= 0) {
    die("<h2>Erro</h2><p>ID da evolução não especificado.</p>");
}

try {
    $db = getDbConnection();

    // Busca a evolução completa
    $stmt = $db->prepare("
        SELECT ec.*, f.nome AS nome_formulario, f.descricao, f.especialidade
        FROM evolucao_clinica ec
        JOIN formulario f ON ec.formulario_id = f.id
        WHERE ec.id = ?
    ");
    $stmt->execute([$evolucao_id]);
    $evolucao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evolucao) {
        die("<h2>Evolução não encontrada</h2>");
    }

    // Decodifica respostas
    $respostas = json_decode($evolucao['dados'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $respostas = [];
    }

    // Busca perguntas
    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? AND ativo = 1 ORDER BY id");
    $stmt->execute([$evolucao['formulario_id']]);
    $perguntas = $stmt->fetchAll();

    if (!$perguntas) {
        die("<h2>" . htmlspecialchars($evolucao['nome_formulario']) . "</h2><p>Nenhuma pergunta configurada.</p>");
    }

} catch (Exception $e) {
    die("<h2>Erro ao carregar evolução</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar <?= htmlspecialchars($evolucao['nome_formulario']) ?></title>
    <link rel="stylesheet" href="render_forms.css">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h1><i class="fas fa-edit"></i> Editar <?= htmlspecialchars($evolucao['nome_formulario']) ?></h1>
            <?php if (!empty($evolucao['descricao'])): ?>
                <p><?= htmlspecialchars($evolucao['descricao']) ?></p>
            <?php endif; ?>
        </div>

        <h2>Evolução do Paciente ID: <?= $evolucao['paciente_id'] ?></h2>
        <p>
            <a href="javascript:history.back()" class="btn-secundario">← Voltar</a>
        </p>

        <form method="POST" action="salvar_resposta.php" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="atualizar">
            <input type="hidden" name="evolucao_id" value="<?= $evolucao_id ?>">
            <input type="hidden" name="formulario_id" value="<?= $evolucao['formulario_id'] ?>">
            <input type="hidden" name="paciente_id" value="<?= $evolucao['paciente_id'] ?>">

            <?php foreach ($perguntas as $p): ?>
                <div class="form-group">
                    <label class=""><?= htmlspecialchars($p['titulo']) ?></label>
                    <?php if (!empty($p['descricao'])): ?>
                        <small><?= htmlspecialchars($p['descricao']) ?></small>
                    <?php endif; ?>

                    <?php
                    $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
                    $valorAtual = $respostas[$nomeCampo] ?? null;
                    $justificativaAtual = $respostas[$nomeCampo . '_justificativa'] ?? '';
                    $obrigatorio = $p['obrigatorio'] ? ' required' : '';
                    $tamanhoMax = $p['tamanho_maximo'] ?? 255;
                    ?>

                    <?php if ($p['tipo_input'] === 'texto'): ?>
                        <input type="text" name="<?= $nomeCampo ?>" class="form-control" maxlength="<?= $tamanhoMax ?>" 
                               value="<?= htmlspecialchars($valorAtual) ?>">

                    <?php elseif ($p['tipo_input'] === 'textarea'): ?>
                        <textarea name="<?= $nomeCampo ?>" class="form-control" rows="4" maxlength="<?= $tamanhoMax ?>"><?= htmlspecialchars($valorAtual) ?></textarea>

                    <?php elseif ($p['tipo_input'] === 'number'): ?>
                        <input type="number" name="<?= $nomeCampo ?>" class="form-control" value="<?= htmlspecialchars($valorAtual) ?>">

                    <?php elseif ($p['tipo_input'] === 'date'): ?>
                        <input type="date" name="<?= $nomeCampo ?>" class="form-control" value="<?= htmlspecialchars($valorAtual) ?>">

                    <?php elseif ($p['tipo_input'] === 'file'): ?>
                        <input type="file" name="<?= $nomeCampo ?>" class="form-control">
                        <?php if ($valorAtual): ?>
                            <small>Arquivo atual: <?= htmlspecialchars(basename($valorAtual)) ?></small>
                        <?php endif; ?>

                    <?php elseif (in_array($p['tipo_input'], ['radio', 'select'])):
                        $opcoes = [];
                        if (!is_null($p['opcoes']) && $p['opcoes'] !== 'null') {
                            $decoded = json_decode($p['opcoes'], true);
                            if (is_array($decoded)) {
                                $opcoes = $decoded;
                            }
                        }
                        ?>
                        <?php if ($p['tipo_input'] === 'select'): ?>
                            <select name="<?= $nomeCampo ?>" class="form-control">
                                <option value="">Selecione...</option>
                                <?php foreach ($opcoes as $opcao): ?>
                                    <option value="<?= htmlspecialchars($opcao) ?>" <?= ($valorAtual === $opcao) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($opcao) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($p['tipo_input'] === 'radio'): ?>
                            <div class="radio-group">
                                <?php foreach ($opcoes as $opcao): ?>
                                    <div class="radio-item">
                                        <input type="radio" name="<?= $nomeCampo ?>" value="<?= htmlspecialchars($opcao) ?>" 
                                               <?= ($valorAtual === $opcao) ? 'checked' : '' ?>>
                                        <label><?= htmlspecialchars($opcao) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($p['tipo_input'] === 'checkbox'):
                        $opcoes = [];
                        if (!is_null($p['opcoes']) && $p['opcoes'] !== 'null') {
                            $decoded = json_decode($p['opcoes'], true);
                            if (is_array($decoded)) {
                                $opcoes = $decoded;
                            }
                        }
                        ?>
                        <div class="checkbox-group">
                            <?php foreach ($opcoes as $opcao): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="<?= $nomeCampo ?>[]" value="<?= htmlspecialchars($opcao) ?>" 
                                           <?= is_array($valorAtual) && in_array($opcao, $valorAtual) ? 'checked' : '' ?>>
                                    <label><?= htmlspecialchars($opcao) ?></label>
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
                                <input type="radio" name="<?= $nomeCampo ?>" value="sim"
                                       <?= ($valorAtual === 'sim') ? 'checked' : '' ?>
                                       onchange="toggleJustificativa(this, '<?= $justNome ?>', '<?= $condicao ?>')">
                                <label>Sim</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="<?= $nomeCampo ?>" value="nao"
                                       <?= ($valorAtual === 'nao') ? 'checked' : '' ?>
                                       onchange="toggleJustificativa(this, '<?= $justNome ?>', '<?= $condicao ?>')">
                                <label>Não</label>
                            </div>
                        </div>
                        <div id="<?= $justNome ?>_container" style="display:<?= ($valorAtual === $condicao) ? 'block' : 'none' ?>; margin-top:12px;">
                            <textarea name="<?= $justNome ?>" class="form-control" rows="3" 
                                      placeholder="<?= htmlspecialchars($placeholderJust) ?>"><?= htmlspecialchars($justificativaAtual) ?></textarea>
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
                                                       <?= (isset($valorAtual[urlencode($linha)]) && $valorAtual[urlencode($linha)] === $col) ? 'checked' : '' ?>
                                                       >
                                                <label></label>
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
                        <input type="text" name="<?= $nomeCampo ?>" class="form-control" value="<?= htmlspecialchars($valorAtual) ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="form-group">
                <label for="observacoes">Observações</label>
                <textarea name="observacoes" class="form-control" rows="3" placeholder="Adicione observações clínicas..."><?= htmlspecialchars($evolucao['observacoes'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-submit">Atualizar Evolução</button>
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
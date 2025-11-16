<?php
// Valida ID da evolução
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h2>Erro</h2><p>ID da evolução não especificado.</p>");
}
$evolucao_id = (int)$_GET['id'];

// Conexão com o banco
function getDbConnection() {
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

try {
    $db = getDbConnection();

    // Busca a evolução
    $stmt = $db->prepare("
        SELECT ec.*, f.nome AS nome_formulario, f.especialidade
        FROM evolucao_clinica ec
        LEFT JOIN formulario f ON ec.formulario_id = f.id
        WHERE ec.id = ?
    ");
    $stmt->execute([$evolucao_id]);
    $evolucao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evolucao) {
        die("<h2>Evolução não encontrada</h2>");
    }

    // Decodifica as respostas
    $respostas = json_decode($evolucao['dados'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $respostas = [];
    }

    // Busca as perguntas do formulário
    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? AND ativo = 1 ORDER BY id");
    $stmt->execute([$evolucao['formulario_id']]);
    $perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Busca dados do paciente
    $stmt = $db->prepare("SELECT nome, cns FROM paciente WHERE id = ?");
    $stmt->execute([$evolucao['paciente_id']]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Busca anexos relacionados à evolução
    $stmt = $db->prepare("SELECT * FROM evolucao_arquivos WHERE evolucao_id = ? ORDER BY id");
    $stmt->execute([$evolucao_id]);
    $arquivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("<h2>Erro ao carregar evolução</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}

// Processa atualização (se o formulário for submetido)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar') {
    try {
        $observacoes = trim($_POST['observacoes'] ?? '');
        $criado_por = $_SESSION['data_user']['nm_usuario'] ?? 'Usuário Anônimo';

        // Coleta respostas
        $dados = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['acao', 'observacoes'])) continue;
            if (is_array($value)) {
                $dados[$key] = array_map('trim', $value);
            } else {
                $dados[$key] = trim($value);
            }
        }

        $stmt = $db->prepare("
            UPDATE evolucao_clinica 
            SET dados = ?, observacoes = ?, criado_por = ?
            WHERE id = ?
        ");
        $stmt->execute([
            json_encode($dados, JSON_UNESCAPED_UNICODE),
            $observacoes,
            $criado_por,
            $evolucao_id
        ]);

        // Atualiza os dados locais
        $respostas = $dados;
        $evolucao['observacoes'] = $observacoes;
        $mensagem = '<div class="form-message success">Evolução atualizada com sucesso!</div>';

    } catch (Exception $e) {
        $mensagem = '<div class="form-message error">Erro ao atualizar: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Evolução #<?= $evolucao_id ?></title>
    <link rel="stylesheet" href="visualizar_evolucao.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Visualizar Evolução Clínica</h1>
            <p>
                <strong>Paciente:</strong> <?= htmlspecialchars($paciente['nome'] ?? 'Não informado') ?> (ID: <?= $evolucao['paciente_id'] ?>)<br>
                <strong>Formulário:</strong> <?= htmlspecialchars($evolucao['nome_formulario']) ?><br>
                <strong>Especialidade:</strong> <?= htmlspecialchars($evolucao['especialidade'] ?? 'Não informada') ?><br>
                <strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($evolucao['data_hora'])) ?>
            </p>
        </div>

        <?php if (isset($mensagem)): ?>
            <?= $mensagem ?>
        <?php endif; ?>

        <form method="POST" id="form-edicao">
            <input type="hidden" name="acao" value="atualizar">

            <?php foreach ($perguntas as $p): 
            ?>
            <?php
            $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
            $valor = $respostas[$nomeCampo] ?? null;
            $justificativa = $respostas[$nomeCampo . '_justificativa'] ?? '';

            // Ignorar perguntas do tipo 'file' — elas são exibidas na seção de anexos
            if ($p['tipo_input'] === 'file') {
                continue; // Pula para a próxima pergunta
            }
            ?>

            <div class="form-group">
                <label><?= htmlspecialchars($p['titulo']) ?></label>
                <?php if (!empty($p['descricao'])): ?>
                    <small><?= htmlspecialchars($p['descricao']) ?></small>
                <?php endif; ?>

                <?php if ($p['tipo_input'] === 'texto' || $p['tipo_input'] === 'number' || $p['tipo_input'] === 'date'): ?>
                    <div class="resposta-readonly"><?= $valor ? htmlspecialchars($valor) : '<em>Não respondido</em>' ?></div>

                <?php elseif ($p['tipo_input'] === 'textarea'): ?>
                    <div class="resposta-readonly"><?= $valor ? nl2br(htmlspecialchars($valor)) : '<em>Não respondido</em>' ?></div>

                <?php elseif (in_array($p['tipo_input'], ['radio', 'select'])): ?>
                    <div class="resposta-readonly"><?= $valor ? htmlspecialchars($valor) : '<em>Não respondido</em>' ?></div>

                <?php elseif ($p['tipo_input'] === 'checkbox'): ?>
                    <?php if (!empty($valor) && is_array($valor)): ?>
                        <ul class="resposta-lista">
                            <?php foreach ($valor as $v): ?>
                                <li><?= htmlspecialchars($v) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="resposta-readonly"><em>Não respondido</em></div>
                    <?php endif; ?>

                <?php elseif ($p['tipo_input'] === 'sim_nao_justificativa'): ?>
                    <div class="resposta-readonly">
                        <strong>Resposta:</strong> <?= $valor ? htmlspecialchars($valor) : '<em>Não respondido</em>' ?><br>
                        <?php if ($justificativa): ?>
                            <strong>Justificativa:</strong> <?= htmlspecialchars($justificativa) ?>
                        <?php endif; ?>
                    </div>

                <?php elseif ($p['tipo_input'] === 'tabela'): ?>
                    <?php
                    $config = json_decode($p['opcoes'], true);
                    $linhas = $config['linhas'] ?? [];
                    $colunas = $config['colunas'] ?? [];
                    ?>
                    <?php if (!empty($linhas) && !empty($colunas) && is_array($valor)): ?>
                        <table class="tabela-resposta">
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
                                        <td><?= htmlspecialchars($linha) ?></td>
                                        <?php foreach ($colunas as $col): ?>
                                            <td style="text-align: center;">
                                                <?= isset($valor[urlencode($linha)]) && $valor[urlencode($linha)] === $col ? '✅' : '' ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="resposta-readonly"><em>Não respondido</em></div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="resposta-readonly"><?= $valor ? htmlspecialchars($valor) : '<em>Não respondido</em>' ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
            <!-- Observações -->
            <div class="form-group">
                <label>Observações</label>
                <div class="resposta-readonly">
                    <?= !empty($evolucao['observacoes']) ? nl2br(htmlspecialchars($evolucao['observacoes'])) : '<em>Sem observações</em>' ?>
                </div>
            </div>

            <!-- Anexos -->
            <?php if (!empty($arquivos)): ?>
                <div class="form-group">
                    <label>Anexos</label>
                    <ul class="anexos-list" style="list-style:none;padding:0;">
                        <?php foreach ($arquivos as $a):
                            $mime = $a['mime'] ?? '';
                            $ext = pathinfo($a['nome_salvo'] ?? ($a['nome_original'] ?? ''), PATHINFO_EXTENSION);
                            // escolher ícone pelo mime/ext
                            if (preg_match('#^image/#', $mime)) { $icon = 'fa-file-image'; }
                            elseif (strpos($mime, 'pdf') !== false) { $icon = 'fa-file-pdf'; }
                            elseif (strpos($mime, 'word') !== false || in_array(strtolower($ext), ['doc','docx'])) { $icon = 'fa-file-word'; }
                            elseif (strpos($mime, 'excel') !== false || in_array(strtolower($ext), ['xls','xlsx','csv'])) { $icon = 'fa-file-excel'; }
                            elseif (in_array(strtolower($ext), ['zip','rar'])) { $icon = 'fa-file-zipper'; }
                            elseif (strpos($mime, 'text') !== false || in_array(strtolower($ext), ['txt','md'])) { $icon = 'fa-file-lines'; }
                            else { $icon = 'fa-file'; }

                            $canInline = preg_match('#^(image/|application/pdf|text/)#', $mime);
                            $urlBase = '../evlt/serve_anexo.php?id=' . (int)$a['id'];
                            $urlOpen = $urlBase . '&download=0';
                            $urlDownload = $urlBase . '&download=1';

                            // tamanho legível
                            $size = (int)($a['tamanho'] ?? 0);
                            if ($size >= 1048576) $sizeLabel = round($size/1048576,2).' MB';
                            elseif ($size >= 1024) $sizeLabel = round($size/1024,1).' KB';
                            else $sizeLabel = $size.' B';
                        ?>
                            <li class="anexo-item">
                                <div class="anexo-icon"><i class="fas <?= $icon ?>"></i></div>
                                <div class="anexo-meta">
                                    <span class="nome"><?= htmlspecialchars($a['nome_original'] ?: $a['nome_salvo']) ?></span>
                                    <span class="meta"><?= htmlspecialchars($mime ?: 'application/octet-stream') ?> &middot; <span class="small-note"><?= $sizeLabel ?></span></span>
                                </div>
                                <div class="anexo-actions">
                                    <?php if ($canInline): ?>
                                        <a class="btn-anexo btn-open" href="<?= $urlOpen ?>" target="_blank" rel="noopener">
                                            <i class="fas fa-eye"></i> Abrir
                                        </a>
                                    <?php endif; ?>
                                    <a class="btn-anexo btn-download" href="<?= $urlDownload ?>" target="_blank" rel="noopener">
                                        <i class="fas fa-download"></i> Baixar
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Botões -->
           <div class="botoes">
                <button type="button" onclick="toggleEdicao()" class="btn-editar">Editar Respostas</button>
                <button type="submit" class="btn-salvar" style="display:none;">Salvar Alterações</button>
                
                <a href="exportar_evolucao.php?formato=pdf&id=<?= $evolucao_id ?>" target="_blank" class="btn-exportar">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="exportar_evolucao.php?formato=csv&id=<?= $evolucao_id ?>" class="btn-exportar">
                    <i class="fas fa-file-csv"></i> CSV
                </a>
                
                <a href="../" class="btn-voltar">Voltar</a>
            </div>
        </form>
    </div>

    <script>
    function toggleEdicao() {
        const container = document.querySelector('.container');
        const botoes = document.querySelector('.botoes');
        const form = document.getElementById('form-edicao');
        
        if (botoes.querySelector('.btn-salvar').style.display === 'none') {
            // Modo edição: recarrega como formulário editável
            window.location.href = 'editar_evolucao.php?id=<?= $evolucao_id ?>';
        }
    }
    </script>
</body>
</html>
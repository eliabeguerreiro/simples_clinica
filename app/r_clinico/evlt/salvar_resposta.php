<?php
// salvar_resposta.php

session_start();

function getDbConnection() {
    static $db = null;
    if ($db === null) {
        try {
            include "../../../classes/db.class.php";
            $db = DB::connect();
        } catch (Exception $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
    return $db;
}

function exibirMensagemSucesso($titulo, $acaoTexto, $paciente_id) {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$titulo}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --cor-primaria: #6c63ff;
            --cor-secundaria: #574b90;
            --cor-sucesso: #28a745;
            --cor-fundo: #f9f6fc;
            --cor-fundo-card: rgba(255, 255, 255, 0.95);
            --sombra-media: 0 8px 24px rgba(0, 0, 0, 0.08);
            --borda-raio: 16px;
            --transicao: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--cor-fundo);
            color: #333;
            line-height: 1.6;
            background-image: url('../../src/img/bkcg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            padding: 20px;
        }

        .simple-box {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background: var(--cor-fundo-card);
            border-radius: var(--borda-raio);
            box-shadow: var(--sombra-media);
            text-align: center;
            backdrop-filter: blur(6px);
            transition: transform 0.2s ease;
        }

        .simple-box:hover {
            transform: translateY(-2px);
        }

        .simple-box h2 {
            color: var(--cor-sucesso);
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .simple-box h2 i {
            font-size: 28px;
        }

        .simple-box p {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.5;
        }

        .btn-clear {
            background: #f5f5f7;
            color: var(--cor-secundaria);
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 600;
            display: inline-block;
            transition: var(--transicao);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-clear:hover {
            background: #e8e8f0;
            transform: translateY(-2px);
        }

        @media (max-width: 600px) {
            .simple-box {
                margin: 40px 20px;
                padding: 24px;
            }
            .simple-box h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="simple-box">
        <h2><i class="fas fa-check-circle"></i> {$titulo}</h2>
        <p>Os dados foram {$acaoTexto} no banco de forma segura.</p>
        <a href="../pcnt/index.php?id={$paciente_id}&sub=historico" class="btn-clear">
            <i class="fas fa-history"></i> Voltar para o paciente
        </a>
    </div>
</body>
</html>
HTML;
    echo $html;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método não permitido.");
}

$form_id = (int)($_POST['formulario_id'] ?? 0);
$paciente_id = (int)($_POST['paciente_id'] ?? 0);
$observacoes = trim($_POST['observacoes'] ?? '');
$criado_por = $_SESSION['data_user']['id'] ?? 'Usuário Anônimo';

if ($form_id <= 0 || $paciente_id <= 0) {
    die("Formulário ou paciente inválido.");
}

/**
 * Validação dos arquivos usando finfo + checagem de extensão
 */
function validarArquivoDetalhado($arquivo) {
    $tiposPermitidos = [
        'image/jpeg' => ['jpg','jpeg'],
        'image/png'  => ['png'],
        'image/gif'  => ['gif'],
        'image/webp' => ['webp'],
        'application/pdf' => ['pdf'],
        'text/plain' => ['txt'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
        'text/csv' => ['csv'],
        'application/vnd.oasis.opendocument.text' => ['odt'],
        'application/zip' => ['zip'],
        'application/x-rar-compressed' => ['rar']
    ];

    $extWhitelist = [
        'jpg','jpeg','png','gif','webp',
        'pdf','txt','csv',
        'doc','docx','xls','xlsx','odt',
        'zip','rar'
    ];

    $tamanhoMaximo = 10 * 1024 * 1024; // 10MB

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erro no upload do arquivo: " . $arquivo['name']);
    }

    if ($arquivo['size'] > $tamanhoMaximo) {
        throw new Exception("Arquivo muito grande (máximo 10MB): " . $arquivo['name']);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo['tmp_name']);
    if ($mime === false) {
        throw new Exception("Não foi possível determinar o tipo do arquivo: " . $arquivo['name']);
    }

    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (array_key_exists($mime, $tiposPermitidos)) {
        if (!in_array($ext, $tiposPermitidos[$mime])) {
            throw new Exception("Extensão inválida para o tipo detectado: " . $arquivo['name']);
        }
    } else {
        if (!in_array($ext, $extWhitelist)) {
            throw new Exception("Tipo de arquivo não permitido: " . $arquivo['name']);
        }
        $mime = 'application/octet-stream';
    }

    return [
        'mime' => $mime,
        'ext' => $ext,
        'size' => $arquivo['size'],
        'original_name' => $arquivo['name']
    ];
}

function garantirPastaBaseAnexos() {
    $pastaBase = __DIR__ . '/anexo';
    if (!is_dir($pastaBase)) {
        if (!mkdir($pastaBase, 0755, true)) {
            throw new Exception("Erro ao criar pasta base de anexos.");
        }
    }
    return $pastaBase;
}

try {
    $db = getDbConnection();

    // ========== ATUALIZAÇÃO DE EVOLUÇÃO ==========
    if (isset($_POST['acao']) && $_POST['acao'] === 'atualizar') {
        $evolucao_id = (int)($_POST['evolucao_id'] ?? 0);
        $form_id = (int)($_POST['formulario_id'] ?? 0);
        $paciente_id = (int)($_POST['paciente_id'] ?? 0);
        $observacoes = trim($_POST['observacoes'] ?? '');
        $criado_por = $_SESSION['data_user']['nm_usuario'] ?? 'Usuário Anônimo';

        if ($evolucao_id <= 0 || $form_id <= 0 || $paciente_id <= 0) {
            die("Evolução, formulário ou paciente inválido.");
        }

        $dados = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['acao', 'evolucao_id', 'formulario_id', 'paciente_id', 'observacoes'])) continue;
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

        exibirMensagemSucesso("Evolução Atualizada com Sucesso!", "atualizados", $paciente_id);
    }

    // ========== INSERÇÃO DE NOVA EVOLUÇÃO ==========
    $dados = [];
    foreach ($_POST as $key => $value) {
        if (in_array($key, ['formulario_id', 'paciente_id', 'observacoes'])) continue;
        if (is_array($value)) {
            $dados[$key] = array_map('trim', $value);
        } else {
            $dados[$key] = trim($value);
        }
    }

    // Validação de arquivos
    $errosArquivos = [];
    $filesToProcess = [];
    foreach ($_FILES as $campo => $arquivo) {
        if (!is_array($arquivo) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) continue;
        try {
            $meta = validarArquivoDetalhado($arquivo);
            $filesToProcess[$campo] = [
                'tmp' => $arquivo['tmp_name'],
                'meta' => $meta,
                'original_name' => $arquivo['name'],
                'size' => $arquivo['size']
            ];
        } catch (Exception $e) {
            $errosArquivos[] = $e->getMessage();
        }
    }

    if (!empty($errosArquivos)) {
        $msg = '<h2>Erros no upload</h2><ul>';
        foreach ($errosArquivos as $err) {
            $msg .= '<li>' . htmlspecialchars($err) . '</li>';
        }
        $msg .= '</ul><p>Nenhum dado foi inserido. Corrija os arquivos e tente novamente.</p>';
        die($msg);
    }

    $db->beginTransaction();
    $stmt = $db->prepare("
        INSERT INTO evolucao_clinica (formulario_id, paciente_id, dados, observacoes, criado_por)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $form_id,
        $paciente_id,
        json_encode($dados, JSON_UNESCAPED_UNICODE),
        $observacoes,
        $criado_por
    ]);
    $evolucao_id = $db->lastInsertId();

    // Pasta de anexos
    $pastaBase = garantirPastaBaseAnexos();
    $pastaForm = $pastaBase . '/' . $form_id;
    if (!is_dir($pastaForm)) mkdir($pastaForm, 0755, true);
    $pastaEvol = $pastaForm . '/' . $evolucao_id;
    if (!is_dir($pastaEvol)) mkdir($pastaEvol, 0755, true);

    // Mover arquivos
    $movedFiles = [];
    if (!empty($filesToProcess)) {
        $insertArquivoStmt = $db->prepare("
            INSERT INTO evolucao_arquivos (evolucao_id, campo, nome_salvo, nome_original, mime, tamanho, caminho_relativo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($filesToProcess as $campo => $info) {
            $ext = $info['meta']['ext'];
            $uniq = bin2hex(random_bytes(6));
            $nomeSalvo = "form{$form_id}_pac{$paciente_id}_evo{$evolucao_id}_{$campo}_{$uniq}.{$ext}";
            $caminhoCompleto = $pastaEvol . '/' . $nomeSalvo;

            if (!move_uploaded_file($info['tmp'], $caminhoCompleto)) {
                foreach ($movedFiles as $f) { @unlink($f); }
                throw new Exception("Falha ao mover o arquivo: " . ($info['original_name'] ?? $campo));
            }

            @chmod($caminhoCompleto, 0644);
            $movedFiles[] = $caminhoCompleto;
            $caminhoRelativo = "anexo/{$form_id}/{$evolucao_id}/{$nomeSalvo}";

            $insertArquivoStmt->execute([
                $evolucao_id,
                $campo,
                $nomeSalvo,
                $info['original_name'],
                $info['meta']['mime'],
                $info['meta']['size'],
                $caminhoRelativo
            ]);
        }
    }

    $db->commit();
    exibirMensagemSucesso("Evolução Salva com Sucesso!", "registrados", $paciente_id);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    if (isset($movedFiles) && is_array($movedFiles)) {
        foreach ($movedFiles as $f) { @unlink($f); }
    }
    die("<h2>Erro ao salvar evolução</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
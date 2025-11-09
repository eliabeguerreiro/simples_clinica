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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método não permitido.");
}

$form_id = (int)($_POST['formulario_id'] ?? 0);
$paciente_id = (int)($_POST['paciente_id'] ?? 0);
$observacoes = trim($_POST['observacoes'] ?? '');
$criado_por = $_SESSION['data_user']['nm_usuario'] ?? 'Usuário Anônimo';

if ($form_id <= 0 || $paciente_id <= 0) {
    die("Formulário ou paciente inválido.");
}

/**
 * Validação dos arquivos usando finfo + checagem de extensão
 */
function validarArquivoDetalhado($arquivo) {
    // Mapeamento MIME -> extensões aceitas
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

    // Lista de extensões permitidas como fallback quando o MIME for genérico
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

    // Se o MIME estiver na lista, cheque extensão compatível
    if (array_key_exists($mime, $tiposPermitidos)) {
        if (!in_array($ext, $tiposPermitidos[$mime])) {
            throw new Exception("Extensão inválida para o tipo detectado: " . $arquivo['name']);
        }
    } else {
        // Fallback: aceitar se a extensão estiver na whitelist (trata casos com MIME genérico)
        if (!in_array($ext, $extWhitelist)) {
            throw new Exception("Tipo de arquivo não permitido: " . $arquivo['name']);
        }
        // normaliza o mime para um genérico aceitável
        $mime = 'application/octet-stream';
    }

    return [
        'mime' => $mime,
        'ext' => $ext,
        'size' => $arquivo['size'],
        'original_name' => $arquivo['name']
    ];
}

/**
 * Garante que a pasta de anexos existe e retorna o caminho base
 */
function garantirPastaBaseAnexos() {
    $pastaBase = __DIR__ . '/anexo';
    if (!is_dir($pastaBase)) {
        if (!mkdir($pastaBase, 0755)) {
            throw new Exception("Erro ao criar pasta base de anexos.");
        }
    }
    return $pastaBase;
}

try {
    $db = getDbConnection();

    // 1) Validar todos os arquivos primeiro — não mover nada ainda
    $errosArquivos = [];
    $filesToProcess = []; // guarda meta info
    foreach ($_FILES as $campo => $arquivo) {
        if (!is_array($arquivo) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) continue;
        try {
            $meta = validarArquivoDetalhado($arquivo);
            $filesToProcess[$campo] = [
                'tmp' => $arquivo['tmp_name'],
                'meta' => $meta
            ];
            // mantenha original name e size no array
            $filesToProcess[$campo]['original_name'] = $arquivo['name'];
            $filesToProcess[$campo]['size'] = $arquivo['size'];
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

    // 2) Coleta demais respostas (campos POST) antes da transação
    $dados = [];
    foreach ($_POST as $key => $value) {
        if (in_array($key, ['formulario_id', 'paciente_id', 'observacoes'])) continue;
        if (is_array($value)) {
            $dados[$key] = array_map('trim', $value);
        } else {
            $dados[$key] = trim($value);
        }
    }

    // 3) Inicia transação: insere evolução (sem arquivos ainda) e pega evolucao_id
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

    // 4) Prepara pasta de anexos específica: /anexo/{form_id}/{evolucao_id}
    $pastaBase = garantirPastaBaseAnexos();
    $pastaForm = $pastaBase . '/' . $form_id;
    if (!is_dir($pastaForm)) {
        if (!mkdir($pastaForm, 0755)) {
            throw new Exception("Erro ao criar pasta do formulário.");
        }
    }
    $pastaEvol = $pastaForm . '/' . $evolucao_id;
    if (!is_dir($pastaEvol)) {
        if (!mkdir($pastaEvol, 0755)) {
            throw new Exception("Erro ao criar pasta da evolução.");
        }
    }

    // 5) Mover arquivos validados para pasta definitiva e registrar na tabela evolucao_arquivos
    $movedFiles = [];
    if (!empty($filesToProcess)) {
        $insertArquivoStmt = $db->prepare("
            INSERT INTO evolucao_arquivos (evolucao_id, campo, nome_salvo, nome_original, mime, tamanho, caminho_relativo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($filesToProcess as $campo => $info) {
            $ext = $info['meta']['ext'];
            $uniq = bin2hex(random_bytes(6));
            // Nome padronizado: form{form}_pac{pac}_evo{evo}_{campo}_{uniq}.{ext}
            $nomeSalvo = "form{$form_id}_pac{$paciente_id}_evo{$evolucao_id}_{$campo}_{$uniq}.{$ext}";
            $caminhoCompleto = $pastaEvol . '/' . $nomeSalvo;

            if (!move_uploaded_file($info['tmp'], $caminhoCompleto)) {
                // se falhar, limpa movidos e lança exceção para rollback
                foreach ($movedFiles as $f) { @unlink($f); }
                throw new Exception("Falha ao mover o arquivo: " . ($info['original_name'] ?? $campo));
            }

            @chmod($caminhoCompleto, 0644);
            $movedFiles[] = $caminhoCompleto;

            // caminho_relativo para recuperar depois (ex: anexo/{form}/{evolucao}/{arquivo})
            $caminhoRelativo = "anexo/{$form_id}/{$evolucao_id}/{$nomeSalvo}";

            // Insere metadados na tabela evolucao_arquivos
            $insertArquivoStmt->execute([
                $evolucao_id,
                $campo,
                $nomeSalvo,
                $info['original_name'] ?? '',
                $info['meta']['mime'],
                $info['meta']['size'],
                $caminhoRelativo
            ]);
        }
    }

    // 6) Commit da transação
    $db->commit();

    // Sucesso — renderiza página de confirmação
    $html = <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Evolução Salva</title>
            <link rel="stylesheet" href="../../src/style.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        </head>
        <body>
            <div class="simple-box" style="max-width: 500px; margin: 100px auto; text-align: center;">
                <h2 style="color: #28a745; font-size: 24px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                    Evolução salva com sucesso!
                </h2>

                <p style="color: #666; margin-bottom: 30px; font-size: 16px;">
                    Os dados foram gravados no banco de forma segura.
                </p>

                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="../pcnt/index.php?id={$paciente_id}&sub=historico" class="btn-clear" style="padding: 12px 20px; font-size: 14px; display: inline-block; text-decoration: none;">
                        <i class="fas fa-history"></i> Voltar para o paciente
                    </a>
                </div>
            </div>

            <script src="../../src/script.js"></script>
        </body>
        </html>
HTML;

    echo $html;

} catch (Exception $e) {
    // Rollback caso transação esteja ativa
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    // Remove arquivos movidos se houver
    if (isset($movedFiles) && is_array($movedFiles)) {
        foreach ($movedFiles as $f) { @unlink($f); }
    }
    die("<h2>Erro ao salvar evolução</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
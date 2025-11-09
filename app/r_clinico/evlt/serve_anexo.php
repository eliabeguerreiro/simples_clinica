<?php
// serve_anexo.php
// Uso: serve_anexo.php?id=ARQUIVO_ID&download=0|1

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "Parâmetro inválido.";
    exit;
}
$arquivoId = (int)$_GET['id'];
$forceDownload = isset($_GET['download']) && (int)$_GET['download'] === 1;

function getDbConnection() {
    static $db = null;
    if ($db === null) {
        try {
            include __DIR__ . "/../../../classes/db.class.php";
            $db = DB::connect();
        } catch (Exception $e) {
            http_response_code(500);
            echo "Erro na conexão.";
            exit;
        }
    }
    return $db;
}

$db = getDbConnection();
$stmt = $db->prepare("SELECT * FROM evolucao_arquivos WHERE id = ?");
$stmt->execute([$arquivoId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo "Arquivo não encontrado.";
    exit;
}

// caminho relativo salvo ex: anexo/{form_id}/{evolucao_id}/{nome}
$caminhoRel = $row['caminho_relativo'] ?? '';
$fullPath = __DIR__ . '/' . $caminhoRel;
if (!file_exists($fullPath) || !is_readable($fullPath)) {
    http_response_code(404);
    echo "Arquivo não encontrado no servidor.";
    exit;
}

$mime = $row['mime'] ?: mime_content_type($fullPath);
$nomeOriginal = $row['nome_original'] ?: basename($fullPath);

// Cabeçalhos
$filesize = filesize($fullPath);
$disposition = $forceDownload ? 'attachment' : 'inline';

// Força download se mime não for inline-friendly
$inlineFriendly = preg_match('#^(image/|application/pdf|text/)#', $mime) && !$forceDownload;
if (!$inlineFriendly) { $disposition = 'attachment'; }

header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $filesize);
header('Cache-Control: private, max-age=10800, must-revalidate');
header('Pragma: public');
header('Expires: 0');
header('Content-Disposition: ' . $disposition . '; filename="' . basename($nomeOriginal) . '"');

@readfile($fullPath);
exit;
?>
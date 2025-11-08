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
$paciente_id = (int)($_POST['paciente_id'] ?? 0); // ajuste conforme sua estrutura
$observacoes = trim($_POST['observacoes'] ?? '');
$criado_por = $_SESSION['data_user']['nm_usuario'] ?? 'Usuário Anônimo';

if ($form_id <= 0 || $paciente_id <= 0) {
    die("Formulário ou paciente inválido.");
}

try {
    $db = getDbConnection();

    // Coleta todas as respostas (exceto campos de sistema)
    $dados = [];
    foreach ($_POST as $key => $value) {
        if (in_array($key, ['formulario_id', 'paciente_id', 'observacoes'])) continue;

        // Se for array (checkbox), mantém como array
        if (is_array($value)) {
            $dados[$key] = array_map('trim', $value);
        } else {
            $dados[$key] = trim($value);
        }
    }

    // Salva como JSON
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

        // ✅ Mensagem de sucesso estilizada
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
    die("<h2>Erro ao salvar evolução</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
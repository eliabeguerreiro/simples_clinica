<?php
// salvar_resposta.php

session_start();

function getDbConnection() {
    static $db = null;
    if ($db === null) {
        try {
            include_once "classes/db.class.php";
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

    echo "<h2>Evolução salva com sucesso!</h2>";
    echo "<a href='render_formulario.php?form_id=$form_id'>Voltar ao formulário</a>";

} catch (Exception $e) {
    die("<h2>Erro ao salvar evolução</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
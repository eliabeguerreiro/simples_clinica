<?php

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido.");
}
$evolucao_id = (int)$_GET['id'];

// Busca evolução
include "../../../classes/db.class.php";
$db = DB::connect();
$stmt = $db->prepare("SELECT * FROM evolucao_clinica WHERE id = ?");
$stmt->execute([$evolucao_id]);
$evolucao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evolucao) {
    die("Evolução não encontrada.");
}

// Redireciona para render_forms.php com os dados pré-preenchidos
// (você pode adaptar o render_forms.php para aceitar ?evolucao_id=4)
header("Location: ../evlt/render_forms.php?form_id=" . $evolucao['formulario_id'] . "&paciente_id=" . $evolucao['paciente_id'] . "&evolucao_id=" . $evolucao_id);
exit;
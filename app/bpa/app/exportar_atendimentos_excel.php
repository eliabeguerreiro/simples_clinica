<?php
session_start();
include_once "classes/painel.class.php";
include_once "classes/db.class.php";

// Recebe filtros via GET
$filtros = [
    'competencia' => $_GET['competencia'] ?? '',
    'data_inicio' => $_GET['data_inicio'] ?? '',
    'data_fim' => $_GET['data_fim'] ?? '',
    'profissional_id' => $_GET['profissional_id'] ?? '',
    'procedimento_id' => $_GET['procedimento_id'] ?? '',
    'paciente_nome' => $_GET['paciente_nome'] ?? '',
    'paciente_id' => $_GET['paciente_id'] ?? ''
];

// Busca todos os atendimentos filtrados (sem paginação)
$atendimentos = Painel::getAtendimentos($filtros, 1, 1000000); // um número grande para garantir tudo

// Monta o arquivo Excel (CSV para simplicidade)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=atendimentos_exportados_' . date('Ymd_His') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Data', 'Profissional', 'Paciente', 'Procedimento', 'Competência']);

foreach ($atendimentos['dados'] as $a) {
    fputcsv($output, [
        date('d/m/Y', strtotime($a['data_atendimento'])),
        $a['profissional_nome'],
        $a['paciente_nome'],
        $a['procedimento_codigo'],
        substr($a['competencia'], 4, 2) . '/' . substr($a['competencia'], 0, 4)
    ]);
}
fclose($output);
exit;
<?php
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['formato'])) {
    die("Parâmetros inválidos.");
}

$evolucao_id = (int)$_GET['id'];
$formato = $_GET['formato'];

// Carrega o Composer (se existir)
$composer = false;
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    $composer = true;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['formato'])) {
    die("Parâmetros inválidos.");
}

$evolucao_id = (int)$_GET['id'];
$formato = $_GET['formato'];

// Carrega o Composer (se existir)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    $composer = true;
} else {
    $composer = false;
}

// Conexão
include "../../../classes/db.class.php";
$db = DB::connect();

// Busca evolução
$stmt = $db->prepare("
    SELECT ec.*, f.nome AS nome_formulario, f.especialidade
    FROM evolucao_clinica ec
    LEFT JOIN formulario f ON ec.formulario_id = f.id
    WHERE ec.id = ?
");
$stmt->execute([$evolucao_id]);
$evolucao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evolucao) {
    die("Evolução não encontrada.");
}

$respostas = json_decode($evolucao['dados'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $respostas = [];
}

// Busca perguntas
$stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? AND ativo = 1 ORDER BY id");
$stmt->execute([$evolucao['formulario_id']]);
$perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca paciente
$stmt = $db->prepare("SELECT nome, cns FROM paciente WHERE id = ?");
$stmt->execute([$evolucao['paciente_id']]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

// Função para limpar texto
function limparTexto($texto) {
    return str_replace(["\r", "\n"], ' ', strip_tags($texto));
}

// ==================== EXPORTAÇÃO XLSX ====================
if ($formato === 'xlsx') {
    // Verifica se o Composer foi carregado e as classes existem
    if (!$composer || !class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
        die("Biblioteca PhpSpreadsheet não encontrada. Execute: composer require phpoffice/phpspreadsheet");
    }

    // Agora usa as classes diretamente (sem `use` dentro do if)
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Evolução Clínica');

    // Cabeçalho
    $sheet->setCellValue('A1', 'Evolução Clínica #' . $evolucao_id);
    $sheet->mergeCells('A1:D1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    $linha = 3;
    $sheet->setCellValue('A' . $linha, 'Paciente');
    $sheet->setCellValue('B' . $linha, $paciente['nome'] . ' (ID: ' . $evolucao['paciente_id'] . ')');
    $linha++;
    $sheet->setCellValue('A' . $linha, 'Formulário');
    $sheet->setCellValue('B' . $linha, $evolucao['nome_formulario']);
    $linha++;
    $sheet->setCellValue('A' . $linha, 'Especialidade');
    $sheet->setCellValue('B' . $linha, $evolucao['especialidade']);
    $linha++;
    $sheet->setCellValue('A' . $linha, 'Data');
    $sheet->setCellValue('B' . $linha, date('d/m/Y H:i', strtotime($evolucao['data_hora'])));
    $linha += 2;

    // Perguntas e respostas
    $sheet->setCellValue('A' . $linha, 'Pergunta');
    $sheet->setCellValue('B' . $linha, 'Resposta');
    $sheet->getStyle('A' . $linha . ':B' . $linha)->getFont()->setBold(true);
    $linha++;

    foreach ($perguntas as $p) {
        $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
        $valor = $respostas[$nomeCampo] ?? null;

        if ($p['tipo_input'] === 'checkbox' && is_array($valor)) {
            $valor = implode(', ', $valor);
        } elseif ($p['tipo_input'] === 'sim_nao_justificativa') {
            $justificativa = $respostas[$nomeCampo . '_justificativa'] ?? '';
            $valor = ($valor ?: 'Não respondido') . ($justificativa ? ' | Justificativa: ' . $justificativa : '');
        } elseif ($p['tipo_input'] === 'tabela') {
            $linhas = json_decode($p['opcoes'], true)['linhas'] ?? [];
            $resps = [];
            foreach ($linhas as $linha_item) {
                $chave = urlencode($linha_item);
                if (isset($valor[$chave])) {
                    $resps[] = $linha_item . ': ' . $valor[$chave];
                }
            }
            $valor = implode('; ', $resps);
        }

        $sheet->setCellValue('A' . $linha, limparTexto($p['titulo']));
        $sheet->setCellValue('B' . $linha, limparTexto($valor ?: 'Não respondido'));
        $linha++;
    }

    $linha++;
    $sheet->setCellValue('A' . $linha, 'Observações');
    $sheet->setCellValue('B' . $linha, limparTexto($evolucao['observacoes'] ?: 'Sem observações'));

    // Ajusta largura
    $sheet->getColumnDimension('A')->setWidth(40);
    $sheet->getColumnDimension('B')->setWidth(60);

    // Saída
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="evolucao_' . $evolucao_id . '.xlsx"');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// ==================== EXPORTAÇÃO CSV ====================
if ($formato === 'excel') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="evolucao_' . $evolucao_id . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Evolução Clínica #' . $evolucao_id]);
    fputcsv($output, []);
    fputcsv($output, ['Paciente', $paciente['nome'] . ' (ID: ' . $evolucao['paciente_id'] . ')']);
    fputcsv($output, ['Formulário', $evolucao['nome_formulario']]);
    fputcsv($output, ['Especialidade', $evolucao['especialidade']]);
    fputcsv($output, ['Data', date('d/m/Y H:i', strtotime($evolucao['data_hora']))]);
    fputcsv($output, []);

    foreach ($perguntas as $p) {
        $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
        $valor = $respostas[$nomeCampo] ?? null;

        if ($p['tipo_input'] === 'checkbox' && is_array($valor)) {
            $valor = implode(', ', $valor);
        } elseif ($p['tipo_input'] === 'sim_nao_justificativa') {
            $justificativa = $respostas[$nomeCampo . '_justificativa'] ?? '';
            $valor = ($valor ?: 'Não respondido') . ($justificativa ? ' | Justificativa: ' . $justificativa : '');
        } elseif ($p['tipo_input'] === 'tabela') {
            $linhas = json_decode($p['opcoes'], true)['linhas'] ?? [];
            $resps = [];
            foreach ($linhas as $linha) {
                $chave = urlencode($linha);
                if (isset($valor[$chave])) {
                    $resps[] = $linha . ': ' . $valor[$chave];
                }
            }
            $valor = implode('; ', $resps);
        }

        fputcsv($output, [
            limparTexto($p['titulo']),
            limparTexto($valor ?: 'Não respondido')
        ]);
    }

    fputcsv($output, []);
    fputcsv($output, ['Observações', limparTexto($evolucao['observacoes'] ?: 'Sem observações')]);

    fclose($output);
    exit;
}

// ==================== EXPORTAÇÃO PDF ====================
if ($formato === 'pdf') {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Evolução #' . $evolucao_id . '</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            h1 { color: #333; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f5f5f5; }
            .cabecalho { margin-bottom: 20px; }
            .cabecalho p { margin: 5px 0; }
        </style>
    </head>
    <body onload="window.print()">
        <div class="cabecalho">
            <h1>Evolução Clínica #' . $evolucao_id . '</h1>
            <p><strong>Paciente:</strong> ' . htmlspecialchars($paciente['nome']) . ' (ID: ' . $evolucao['paciente_id'] . ')</p>
            <p><strong>Formulário:</strong> ' . htmlspecialchars($evolucao['nome_formulario']) . '</p>
            <p><strong>Especialidade:</strong> ' . htmlspecialchars($evolucao['especialidade']) . '</p>
            <p><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($evolucao['data_hora'])) . '</p>
        </div>

        <table>
            <thead>
                <tr><th>Pergunta</th><th>Resposta</th></tr>
            </thead>
            <tbody>';

    foreach ($perguntas as $p) {
        $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
        $valor = $respostas[$nomeCampo] ?? null;

        if ($p['tipo_input'] === 'checkbox' && is_array($valor)) {
            $valor = implode(', ', array_map('htmlspecialchars', $valor));
        } elseif ($p['tipo_input'] === 'sim_nao_justificativa') {
            $justificativa = $respostas[$nomeCampo . '_justificativa'] ?? '';
            $valor = htmlspecialchars($valor ?: 'Não respondido');
            if ($justificativa) {
                $valor .= '<br><em>Justificativa:</em> ' . htmlspecialchars($justificativa);
            }
        } elseif ($p['tipo_input'] === 'tabela') {
            $config = json_decode($p['opcoes'], true);
            $linhas = $config['linhas'] ?? [];
            $colunas = $config['colunas'] ?? [];
            $htmlTabela = '<table style="width:100%; border:1px solid #ddd;"><thead><tr>';
            foreach ($colunas as $col) {
                $htmlTabela .= '<th>' . htmlspecialchars($col) . '</th>';
            }
            $htmlTabela .= '</tr></thead><tbody>';
            foreach ($linhas as $linha) {
                $htmlTabela .= '<tr><td>' . htmlspecialchars($linha) . '</td>';
                foreach ($colunas as $col) {
                    $chave = urlencode($linha);
                    $marcado = isset($valor[$chave]) && $valor[$chave] === $col ? '✅' : '';
                    $htmlTabela .= '<td style="text-align:center;">' . $marcado . '</td>';
                }
                $htmlTabela .= '</tr>';
            }
            $htmlTabela .= '</tbody></table>';
            $valor = $htmlTabela;
        } else {
            $valor = htmlspecialchars($valor ?: 'Não respondido');
        }

        echo '<tr>
            <td><strong>' . htmlspecialchars($p['titulo']) . '</strong></td>
            <td>' . ($valor ?: 'Não respondido') . '</td>
        </tr>';
    }

    echo '</tbody></table>';

    if (!empty($evolucao['observacoes'])) {
        echo '<div style="margin-top:20px;">
            <h3>Observações</h3>
            <p>' . nl2br(htmlspecialchars($evolucao['observacoes'])) . '</p>
        </div>';
    }

    echo '</body></html>';
    exit;
}

die("Formato inválido.");
?>
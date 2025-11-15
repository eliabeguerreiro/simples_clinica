<?php
session_start();

if (!isset($_SESSION['data_user'])) {
    die("Acesso negado.");
}

$formato = $_GET['formato'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0 || !in_array($formato, ['pdf', 'csv'])) {
    die("Parâmetros inválidos.");
}

function getDb() {
    static $db = null;
    if ($db === null) {
        include "../../../classes/db.class.php";
        $db = DB::connect();
    }
    return $db;
}

try {
    $db = getDb();

    // Busca evolução
    $stmt = $db->prepare("
        SELECT ec.*, f.nome AS nome_formulario, f.especialidade
        FROM evolucao_clinica ec
        LEFT JOIN formulario f ON ec.formulario_id = f.id
        WHERE ec.id = ?
    ");
    $stmt->execute([$id]);
    $evolucao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evolucao) die("Evolução não encontrada.");

    $respostas = json_decode($evolucao['dados'], true) ?: [];
    $pacienteStmt = $db->prepare("SELECT nome FROM paciente WHERE id = ?");
    $pacienteStmt->execute([$evolucao['paciente_id']]);
    $paciente = $pacienteStmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? AND ativo = 1 ORDER BY id");
    $stmt->execute([$evolucao['formulario_id']]);
    $perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Nome do arquivo base
    $nomeBase = "Evolucao_{$id}_Paciente_{$evolucao['paciente_id']}";

    if ($formato === 'pdf') {
        require_once '../../../classes/tcpdf/tcpdf.php';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema Clínico');
        $pdf->SetTitle("Evolução Clínica #$id");
        $pdf->SetSubject('Evolução Clínica');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        // Estilo CSS embutido para tabelas (TCPDF suporta CSS básico)
        $html = '<style>
            table { border-collapse: collapse; width: 100%; margin: 8px 0; }
            th, td { border: 1px solid #333; padding: 5px; text-align: left; font-size: 9pt; }
            th { background-color: #f0f0f0; font-weight: bold; }
            p { margin: 6px 0; }
            strong { color: #000; }
        </style>';

        $html .= '<h1 style="text-align:center; margin-bottom:15px;">Evolução Clínica #' . $id . '</h1>';
        $html .= '<p><strong>Paciente:</strong> ' . htmlspecialchars($paciente['nome'] ?? 'N/A') . '</p>';
        $html .= '<p><strong>Formulário:</strong> ' . htmlspecialchars($evolucao['nome_formulario'] ?? 'N/A') . '</p>';
        $html .= '<p><strong>Especialidade:</strong> ' . htmlspecialchars($evolucao['especialidade'] ?? 'N/A') . '</p>';
        $html .= '<p><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($evolucao['data_hora'])) . '</p>';
        $html .= '<hr>';

        foreach ($perguntas as $p) {
            if ($p['tipo_input'] === 'file') {
                continue; // Ignora campos de anexo nas respostas
            }
            $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
            $valor = $respostas[$nomeCampo] ?? null;
            $titulo = htmlspecialchars($p['titulo']);
            $resposta = '<em>Não respondido</em>';

            if ($p['tipo_input'] === 'texto' || $p['tipo_input'] === 'number' || $p['tipo_input'] === 'date') {
                $resposta = htmlspecialchars($valor ?: 'N/A');

            } elseif ($p['tipo_input'] === 'textarea') {
                $resposta = nl2br(htmlspecialchars($valor ?: 'N/A'));

            } elseif ($p['tipo_input'] === 'radio' || $p['tipo_input'] === 'select') {
                $resposta = htmlspecialchars($valor ?: 'N/A');

            } elseif ($p['tipo_input'] === 'checkbox') {
                if (is_array($valor) && !empty($valor)) {
                    $resposta = implode(', ', array_map('htmlspecialchars', $valor));
                }

            } elseif ($p['tipo_input'] === 'sim_nao_justificativa') {
                $resposta = htmlspecialchars($valor ?: 'N/A');
                $just = $respostas[$nomeCampo . '_justificativa'] ?? '';
                if ($just) {
                    $resposta .= "<br><strong>Justificativa:</strong> " . htmlspecialchars($just);
                }

            } elseif ($p['tipo_input'] === 'tabela') {
                $config = json_decode($p['opcoes'], true);
                $linhas = $config['linhas'] ?? [];
                $colunas = $config['colunas'] ?? [];

                if (!empty($linhas) && !empty($colunas) && is_array($valor)) {
                    $resposta = '<table>';
                    $resposta .= '<thead><tr><th>Item</th>';
                    foreach ($colunas as $col) {
                        $resposta .= '<th>' . htmlspecialchars($col) . '</th>';
                    }
                    $resposta .= '</tr></thead><tbody>';

                    foreach ($linhas as $linha) {
                        $resposta .= '<tr><td>' . htmlspecialchars($linha) . '</td>';
                        foreach ($colunas as $col) {
                            $checked = (isset($valor[urlencode($linha)]) && $valor[urlencode($linha)] === $col) ? '✓' : '';
                            $resposta .= '<td style="text-align:center;">' . $checked . '</td>';
                        }
                        $resposta .= '</tr>';
                    }
                    $resposta .= '</tbody></table>';
                }

            } elseif ($p['tipo_input'] === 'file') {
                if (!empty($valor) && file_exists('../../' . $valor)) {
                    $resposta = '<em>Anexo salvo</em>';
                }

            } else {
                $resposta = htmlspecialchars($valor ?: 'N/A');
            }

            $html .= "<p><strong>{$titulo}:</strong> {$resposta}</p>";
        }

        if (!empty($evolucao['observacoes'])) {
            $html .= '<p><strong>Observações:</strong> ' . nl2br(htmlspecialchars($evolucao['observacoes'])) . '</p>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output($nomeBase . '.pdf', 'I');

    } elseif ($formato === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$nomeBase.csv\"");

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

        fputcsv($output, ['Pergunta', 'Resposta'], ';');

        foreach ($perguntas as $p) {
            if ($p['tipo_input'] === 'file') {
                continue; // Ignora campos de anexo nas respostas
            }
            $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
            $valor = $respostas[$nomeCampo] ?? null;
            $titulo = $p['titulo'];

            if ($p['tipo_input'] === 'checkbox' && is_array($valor)) {
                $resposta = implode(', ', $valor);
            } elseif ($p['tipo_input'] === 'sim_nao_justificativa') {
                $resposta = $valor ?: '';
                $just = $respostas[$nomeCampo . '_justificativa'] ?? '';
                if ($just) $resposta .= " | Justificativa: $just";
            } elseif ($p['tipo_input'] === 'tabela') {
                $resposta = is_array($valor) ? json_encode($valor) : 'N/A';
            } elseif ($p['tipo_input'] === 'file') {
                $resposta = $valor ?: 'N/A';
            } else {
                $resposta = is_array($valor) ? json_encode($valor) : ($valor ?: '');
            }

            fputcsv($output, [$titulo, $resposta], ';');
        }

        if (!empty($evolucao['observacoes'])) {
            fputcsv($output, ['Observações', $evolucao['observacoes']], ';');
        }

        fclose($output);
        exit;
    }

} catch (Exception $e) {
    die("Erro ao exportar: " . htmlspecialchars($e->getMessage()));
}
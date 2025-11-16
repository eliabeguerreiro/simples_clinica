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

    // --- Busca dados da evolução ---
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
    $pacienteStmt = $db->prepare("SELECT nome, cns FROM paciente WHERE id = ?");
    $pacienteStmt->execute([$evolucao['paciente_id']]);
    $paciente = $pacienteStmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? AND ativo = 1 ORDER BY id");
    $stmt->execute([$evolucao['formulario_id']]);
    $perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $nomeBase = "Evolucao_{$id}_Paciente_{$evolucao['paciente_id']}";

    if ($formato === 'pdf') {
        require_once '../../../classes/tcpdf/tcpdf.php';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('Sistema CLINIG');
        $pdf->SetAuthor('CLINIG');
        $pdf->SetTitle("Evolução Clínica #$id");
        $pdf->SetSubject('Exportação de Evolução Clínica');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 20, 15); // margens em mm
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();

        // Define cores
        define('COR_PRIMARIA', [108, 99, 255]); // #6c63ff
        define('COR_SECUNDARIA', [87, 75, 144]); // #574b90

        // Cabeçalho
        $html = '
        <style>
            h1 { color: rgb(' . implode(',', COR_PRIMARIA) . '); text-align: center; margin-bottom: 16px; font-size: 16px; }
            .cabecalho { border-bottom: 1.5px solid rgb(' . implode(',', COR_PRIMARIA) . '); padding-bottom: 10px; margin-bottom: 16px; }
            .info-linha { margin: 4px 0; font-size: 10px; }
            .grupo { margin: 12px 0; page-break-inside: avoid; }
            .pergunta { font-weight: bold; color: rgb(' . implode(',', COR_SECUNDARIA) . '); margin: 6px 0 4px 0; font-size: 10px; }
            .resposta { margin-left: 8px; font-size: 9.5px; color: #333; line-height: 1.4; }
            .tabela-resposta { border: 1px solid #999; width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 8.5px; }
            .tabela-resposta th { background-color: rgb(' . implode(',', COR_PRIMARIA) . '); color: white; padding: 3px; }
            .tabela-resposta td { padding: 3px; text-align: center; border: 1px solid #999; }
            .observacoes { margin-top: 16px; padding-top: 10px; border-top: 1px dashed #aaa; }
            .rodape { margin-top: 25px; font-size: 8px; color: #777; text-align: right; }
        </style>

        <div class="cabecalho">
            <h1>Evolução Clínica</h1>
            <div class="info-linha"><strong>Paciente:</strong> ' . htmlspecialchars($paciente['nome'] ?? 'N/A') . '</div>
            <div class="info-linha"><strong>CNS:</strong> ' . htmlspecialchars($paciente['cns'] ?? '—') . '</div>
            <div class="info-linha"><strong>Formulário:</strong> ' . htmlspecialchars($evolucao['nome_formulario'] ?? 'N/A') . '</div>
            <div class="info-linha"><strong>Especialidade:</strong> ' . htmlspecialchars($evolucao['especialidade'] ?? 'N/A') . '</div>
            <div class="info-linha"><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($evolucao['data_hora'])) . '</div>
        </div>';

        // Corpo: perguntas e respostas
        foreach ($perguntas as $p) {
            if ($p['tipo_input'] === 'file') continue;

            $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
            $valor = $respostas[$nomeCampo] ?? null;
            $justificativa = $respostas[$nomeCampo . '_justificativa'] ?? '';

            $titulo = htmlspecialchars($p['titulo']);
            $respostaHtml = '<span style="color:#888;">Não respondido</span>';

            if ($p['tipo_input'] === 'texto' || $p['tipo_input'] === 'number' || $p['tipo_input'] === 'date') {
                $respostaHtml = htmlspecialchars($valor ?: '—');

            } elseif ($p['tipo_input'] === 'textarea') {
                $respostaHtml = nl2br(htmlspecialchars($valor ?: '—'));

            } elseif (in_array($p['tipo_input'], ['radio', 'select'])) {
                $respostaHtml = htmlspecialchars($valor ?: '—');

            } elseif ($p['tipo_input'] === 'checkbox') {
                if (is_array($valor) && !empty($valor)) {
                    $respostaHtml = implode(', ', array_map('htmlspecialchars', $valor));
                } else {
                    $respostaHtml = '—';
                }

            } elseif ($p['tipo_input'] === 'sim_nao_justificativa') {
                $respostaHtml = htmlspecialchars($valor ?: '—');
                if ($justificativa) {
                    $respostaHtml .= '<br><strong style="color:' . implode(',', COR_SECUNDARIA) . ');">Justificativa:</strong> ' . htmlspecialchars($justificativa);
                }

            } elseif ($p['tipo_input'] === 'tabela') {
                $config = json_decode($p['opcoes'], true);
                $linhas = $config['linhas'] ?? [];
                $colunas = $config['colunas'] ?? [];
                if (!empty($linhas) && !empty($colunas) && is_array($valor)) {
                    $tabela = '<table class="tabela-resposta"><thead><tr><th>Item</th>';
                    foreach ($colunas as $col) {
                        $tabela .= '<th>' . htmlspecialchars($col) . '</th>';
                    }
                    $tabela .= '</tr></thead><tbody>';
                    foreach ($linhas as $linha) {
                        $tabela .= '<tr><td style="text-align:left;font-weight:bold;">' . htmlspecialchars($linha) . '</td>';
                        foreach ($colunas as $col) {
                            $check = (isset($valor[urlencode($linha)]) && $valor[urlencode($linha)] === $col) ? '✓' : '';
                            $tabela .= '<td>' . $check . '</td>';
                        }
                        $tabela .= '</tr>';
                    }
                    $tabela .= '</tbody></table>';
                    $respostaHtml = $tabela;
                } else {
                    $respostaHtml = '—';
                }
            } else {
                $respostaHtml = htmlspecialchars($valor ?: '—');
            }

            $html .= '<div class="grupo">
                <div class="pergunta">' . $titulo . '</div>
                <div class="resposta">' . $respostaHtml . '</div>
            </div>';
        }

        // Observações
        if (!empty($evolucao['observacoes'])) {
            $html .= '<div class="observacoes">
                <div class="pergunta">Observações</div>
                <div class="resposta">' . nl2br(htmlspecialchars($evolucao['observacoes'])) . '</div>
            </div>';
        }

        // Rodapé
        $html .= '<div class="rodape">Documento gerado pelo Sistema CLINIG em ' . date('d/m/Y H:i') . '</div>';

        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($nomeBase . '.pdf', 'I');

    } elseif ($formato === 'csv') {
        // (mantém sua lógica de CSV — já está excelente)
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$nomeBase.csv\"");

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

        fputcsv($output, ['Pergunta', 'Resposta'], ';');

        foreach ($perguntas as $p) {
            if ($p['tipo_input'] === 'file') continue;
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
                $resposta = is_array($valor) ? json_encode($valor, JSON_UNESCAPED_UNICODE) : 'N/A';
            } else {
                $resposta = is_array($valor) ? json_encode($valor, JSON_UNESCAPED_UNICODE) : ($valor ?: '');
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
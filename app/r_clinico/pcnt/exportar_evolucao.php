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
        SELECT 
            ec.id,
            ec.formulario_id,
            ec.paciente_id,
            ec.atendimento_id,
            ec.data_referencia,
            ec.data_hora AS created_at,
            ec.dados,
            ec.observacoes,
            ec.criado_por,
            u.nm_usuario AS criado_por_nome,
            f.nome AS nome_formulario,
            f.especialidade
        FROM evolucao_clinica ec
        LEFT JOIN formulario f ON ec.formulario_id = f.id
        LEFT JOIN usuarios u ON ec.criado_por = u.id
        WHERE ec.id = ?
    ");
    $stmt->execute([$id]);
    $evolucao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evolucao) die("Evolução não encontrada.");

    $respostas = json_decode($evolucao['dados'], true) ?: [];
    $pacienteStmt = $db->prepare("SELECT nome, cns FROM paciente WHERE id = ?");
    $pacienteStmt->execute([$evolucao['paciente_id']]);
    $paciente = $pacienteStmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM formulario_perguntas WHERE formulario_id = ? AND ativo = 1 ORDER BY ordem ASC");
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
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->AddPage();

        // Define cores
        define('COR_PRIMARIA', [108, 99, 255]); // #6c63ff
        define('COR_SECUNDARIA', [87, 75, 144]); // #574b90

        // Verifica se GD está disponível
        $temGD = extension_loaded('gd') && function_exists('imagecreatefrompng');
        $logoPath = __DIR__ . '/src/vivenciar_logov2.png';
        if (!file_exists($logoPath)) {
            $logoPath = __DIR__ . '/../../../src/vivenciar_logov2.png';
        }
        $usarLogo = $temGD && file_exists($logoPath);

        $emitidoPor = htmlspecialchars($_SESSION['data_user']['nm_usuario'] ?? 'Sistema');
        $dataEmissao = date('d/m/Y H:i');

        $html = '
        <style>
            .cabecalho {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 0 12px 0;
                border-bottom: 2px solid #e2e6ff;
                margin-bottom: 15px;
            }
            .cabecalho-logo {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .cabecalho-logo h1 {
                color: #3d3f8f;
                margin: 0;
                font-size: 18px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.8px;
            }
            .cabecalho-info {
                text-align: right;
                font-size: 9px;
                color: #555;
                line-height: 1.4;
            }
            .info-linha {
                margin: 3px 0;
                font-size: 9px;
            }
            .grupo {
                margin: 10px 0;
                page-break-inside: avoid;
            }
            .pergunta {
                font-weight: bold;
                color: #574b90;
                margin: 5px 0 3px 0;
                font-size: 10px;
            }
            .resposta {
                margin-left: 10px;
                font-size: 9.5px;
                color: #333;
                line-height: 1.45;
            }
            .tabela-resposta {
                border: 1px solid #ccc;
                width: 100%;
                border-collapse: collapse;
                margin-top: 4px;
                font-size: 8.5px;
            }
            .tabela-resposta th {
                background-color: #6c63ff;
                color: white;
                padding: 4px;
                font-weight: bold;
            }
            .tabela-resposta td {
                padding: 4px;
                text-align: center;
                border: 1px solid #ddd;
            }
            .observacoes {
                margin-top: 18px;
                padding-top: 12px;
                border-top: 1px solid #e0e0ff;
            }
            .rodape {
                position: fixed;
                bottom: 15px;
                left: 15px;
                right: 15px;
                font-size: 8px;
                color: #777;
                text-align: center;
                border-top: 1px solid #eaeaea;
                padding-top: 6px;
                background: #fafaff;
            }
            .paciente-info {
                display: flex;
                gap: 25px;
                margin: 10px 0 15px 0;
                font-size: 10px;
                color: #444;
            }
            .paciente-info div {
                flex: 1;
            }
            .paciente-info strong {
                color: #574b90;
                font-weight: 600;
            }
        </style>

        <div class="cabecalho">
            <div class="cabecalho-logo">';

        // Renderiza logo ou fallback
        if ($usarLogo) {
            $logoAbsoluto = realpath($logoPath);
            $html .= '<img src="' . $logoAbsoluto . '" style="max-width:75px; max-height:32px; border:1px solid #e5e5ff; border-radius:4px; padding:2px; background:#fff;">';
        } else {
            $html .= '<div style="width:70px; height:30px; background:linear-gradient(135deg, #6c63ff, #574b90); border-radius:4px; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold; font-size:13px;">CLINIG</div>';
        }

        $html .= '<h1>' . htmlspecialchars($evolucao['nome_formulario'] ?? 'FORMULÁRIO CLÍNICO') . '</h1>
            </div>
            <div class="cabecalho-info">
                <div class="info-linha"><strong>Especialidade:</strong> ' . htmlspecialchars($evolucao['especialidade'] ?? 'N/A') . '</div>
                <div class="info-linha"><strong>Data da Evolução:</strong> ' . date('d/m/Y H:i', strtotime($evolucao['data_hora'])) . '</div>
            </div>
        </div>';

        // Informações do paciente (próximas ao título)
        $html .= '<div class="paciente-info">
            <div><strong>Paciente:</strong> ' . htmlspecialchars($paciente['nome'] ?? 'N/A') . '</div>
            <div><strong>CNS:</strong> ' . htmlspecialchars($paciente['cns'] ?? '—') . '</div>
        </div>';

        // Corpo: perguntas e respostas
        foreach ($perguntas as $p) {
            if ($p['tipo_input'] === 'file') continue;

            $nomeCampo = $p['nome_unico'] ?? 'campo_' . $p['id'];
            $valor = $respostas[$nomeCampo] ?? null;
            $justificativa = $respostas[$nomeCampo . '_justificativa'] ?? '';

            $titulo = htmlspecialchars($p['titulo']);
            $respostaHtml = '<span style="color:#999;">Não respondido</span>';

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
                    $respostaHtml .= '<br><strong style="color:#574b90;">Justificativa:</strong> ' . htmlspecialchars($justificativa);
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
                    // ✅ CORREÇÃO: Adicionado "as $linha" no foreach
                    foreach ($linhas as $linha) {
                        $tabela .= '<tr><td style="text-align:left;font-weight:bold;padding-left:6px;">' . htmlspecialchars($linha) . '</td>';
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

        // ✅ CORREÇÃO: Tag HTML completa no rodapé
        $html .= '<div class="rodape">
            <div class="info-linha"><strong>Emitido por:</strong> ' . htmlspecialchars($evolucao['criado_por_nome'] ?? 'Sistema') . ' | <strong>Emissão:</strong> ' . $dataEmissao . '</div>
            <div class="info-linha">Documento gerado pelo Sistema CLINIG | Evolução #' . $id . ' | ' . date('d/m/Y H:i') . '</div>
        </div>';

        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($nomeBase . '.pdf', 'I');

    } elseif ($formato === 'csv') {
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
?>
<?php
session_start();
require_once "classes/db.class.php";
require_once "classes/painel.class.php";
$valor_sub		= array('Ç','ç','Ã','ã','Í','í','Õ','õ','á','é','í','ó','ú','Á','É','Í','Ó','Ú','â','ê','ô','Â','Ê','Ô','à','À');
$valor_por		= array('C','c','A','a','I','i','O','o','a','e','i','o','u','A','E','I','O','U','a','e','o','A','E','O','a','A');

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = DB::connect();

        // Aplica os mesmos filtros da consulta anterior
        $filtros = [
            'competencia' => $_POST['competencia'] ?? '',
            'data_inicio' => $_POST['data_inicio'] ?? '',
            'data_fim' => $_POST['data_fim'] ?? '',
            'profissional_id' => $_POST['profissional_id'] ?? '',
            'procedimento_id' => $_POST['procedimento_id'] ?? '',
            'paciente_nome' => $_POST['paciente_nome'] ?? '',
            'paciente_id' => $_POST['paciente_id'] ?? ''
        ];

        $atendimentos = Painel::GetAtendimentos($filtros, 1, 1000000);
        $clinicaData = Painel::GetClinica();

        if (empty($atendimentos['dados']) || empty($clinicaData['dados'][0])) {
            throw new Exception("Nenhum atendimento ou clínica não encontrada.");
        }

        $clinica = $clinicaData['dados'][0];

        // Define o nome do arquivo
        $competencia = !empty($filtros['competencia']) ? $filtros['competencia'] : date('Ym');
        $meses = [
            '01' => 'JAN', '02' => 'FEV', '03' => 'MAR', '04' => 'ABR',
            '05' => 'MAI', '06' => 'JUN', '07' => 'JUL', '08' => 'AGO',
            '09' => 'SET', '10' => 'OUT', '11' => 'NOV', '12' => 'DEZ'
        ];
        $competenciaMes = substr($competencia, 4, 2);
        $competenciaAno = substr($competencia, 0, 4);
        $extensao = isset($meses[$competenciaMes]) ? $meses[$competenciaMes] : 'TXT';

        // Nome do arquivo no padrão PA[CNES][COMPETENCIA].[EXT]
        $nomeArquivo = "PA401960" . "." . $extensao;

        // Cabeçalho do arquivo BPA-I (iremos compor depois que soubermos o campo de controle)
        $conteudo = "";

        // Monta o header parcialmente agora (campo controle será inserido depois de calculado)
        $headerPrefix = [
            '01', // Tipo de linha
            '#BPA#',
            str_pad($competencia, 6, '0', STR_PAD_RIGHT), // Competência AAAAMM
            str_pad(count($atendimentos['dados']), 6, '0', STR_PAD_LEFT), // Nº de linhas
            '000001', // Nº de folhas (fixo)
        ];

        $headerSuffix = [
            str_pad(substr($clinica['nome'] ?? '', 0, 30), 30, ' ', STR_PAD_RIGHT), // Nome da clínica (max 30)
            str_pad($clinica['sigla'] ?? 'VIVENCIAR', 6, ' ', STR_PAD_RIGHT), // Nova coluna sigla (se existir)
            str_pad('', 14, ' ', STR_PAD_RIGHT), // CNPJ
            str_pad($clinica['secretaria_saude'] ?? 'SECRETARIA ESTADUAL DE SAUDE', 40, ' ', STR_PAD_RIGHT), // Secretaria Saúde
            $clinica['tipo_orgao_destino'] ?? 'M', // E - Estadual, M - Municipal
            str_pad($clinica['versao_sistema'] ?? '1.0.0', 10, ' ', STR_PAD_RIGHT) // Versão sistema
        ];

        // Armazenamos a posição onde o campo controle será inserido para possivelmente inspeção; mas
        // vamos gerar o header final apenas após calcular o campo de controle corretamente.
        $conteudo .= implode('', $headerPrefix);

        // 2. Linhas de atendimento (BPA-I)
        $sequencia = 1;
        $somaProcedimentos = 0;
        $folha = 1;

        foreach ($atendimentos['dados'] as $atendimento) {
            
            $paciente = Painel::getPacienteById($atendimento['paciente_id'] ?? 0) ?? [];
            $profissional = Painel::getProfissionalById($atendimento['profissional_id'] ?? 0) ?? [];
            $procedimento = Painel::getProcedimentoById($atendimento['procedimento_id'] ?? 0) ?? [];
            $codigoProcedimento = $procedimento['codigo'] ?? '0';
            $quantidade = $atendimento['quantidade'] ?? 1;
            $dataAtendimento = $atendimento['data_atendimento'] ?? date('Y-m-d');
            $cid10 = $atendimento['cid_10'] ?? '';
            $idadePaciente = $atendimento['idade_paciente'] ?? '0';
            $caracterAtendimento = $atendimento['caracter_atendimento'] ?? '1'; // está fixo o valor de 01 eletivo
            $numeroAutorizacao = $atendimento['numero_autorizacao'] ?? '';
            $codigoServico = $procedimento['servico'] ?? '@1@';
            $codigoClassificacao = $procedimento['classificacao'] ?? '@2@';
            $codigoSequenciaEquipe = $atendimento['codigo_sequencia_equipe'] ?? '';
            $codigoAreaEquipe = $atendimento['codigo_area_equipe'] ?? '';
            $ineEquipe = '';
            $cnsProfissional = $profissional['cns'] ?? '';
            $cboProfissional = $profissional['cbo'] ?? '';
            $cnsPaciente = $paciente['cns'] ?? '0';
            $sexo = strtoupper(substr($paciente['sexo'] ?? 'M', 0, 1));
            $municipioIbge = $paciente['municipio_ibge'] ?? ' ';
            $nomePaciente_sub = str_replace($valor_sub, $valor_por, $paciente['nome']);
            $nomePaciente = substr($nomePaciente_sub ?? '', 0, 30);
            $dataNascimento = $paciente['data_nascimento'] ?? date('Y-m-d');
            $racaCor = $paciente['raca_cor'] ?? '00';
            $etnia = $paciente['etnia'] ?? '';
            $nacionalidade = $paciente['nacionalidade'] ?? '10';
            $cep = $paciente['cep'] ?? '00000000';
            $codigoLogradouro = $paciente['codigo_logradouro'] ?? '000';
            $endereco_sub	= str_replace($valor_sub, $valor_por,$paciente['endereco']);
            $endereco = substr($endereco_sub ?? '', 0, 30);
            $complemento = substr($paciente['complemento'] ?? '', 0, 10);
            $numero = substr($paciente['numero'] ?? 'SN', 0, 5);
            $bairro_sub = str_replace($valor_sub, $valor_por, $paciente['bairro']);
            $bairro = substr($bairro_sub ?? '', 0, 30);
            $telefone = preg_replace('/[^0-9]/', '', $paciente['telefone'] ?? '');
            $email = substr($paciente['email'] ?? '', 0, 40);
            $cpf = $paciente['cpf'] ?? '';
            $situacaoRua = substr($paciente['situacao_rua'] ?? 'N', 0, 1);

            // Formatação de datas
            $dataAtendimentoFormatada = date('Ymd', strtotime($dataAtendimento));
            $dataNascimentoFormatada = date('Ymd', strtotime($dataNascimento));

            // Calcula campo de controle: somar apenas os dígitos do código do procedimento (se contiver letras)
            // e a quantidade. Isso evita conversões inválidas.
            $codigoDigits = preg_replace('/[^0-9]/', '', (string)$codigoProcedimento);
            $codigoInt = $codigoDigits === '' ? 0 : (int)$codigoDigits;
            $quantidadeInt = is_numeric($quantidade) ? (int)$quantidade : 0;
            $somaProcedimentos += $codigoInt + $quantidadeInt;

            // Monta linha do arquivo
            $linha = [
                '03', // prd-ident
                str_pad($clinica['cnes'] ?? '', 7, ' ', STR_PAD_LEFT), // prd-cnes
                str_pad($competencia, 6, ' ', STR_PAD_RIGHT), //prd-cmp
                str_pad($cnsProfissional, 15, ' ', STR_PAD_LEFT), // prd-cnsmed
                str_pad($cboProfissional, 6, ' ', STR_PAD_RIGHT), // prd-cbo
                $dataAtendimentoFormatada, // prd-dtten
                str_pad($folha, 3, '0', STR_PAD_LEFT), //prd-flh
                str_pad($sequencia, 2, '0', STR_PAD_LEFT), //prd-seq
                str_pad($codigoProcedimento, 10, '0', STR_PAD_LEFT), // prd-pa
                str_pad($cnsPaciente, 15, ' ', STR_PAD_LEFT), // prd-cnspac
                $sexo, // prd-sexo
                str_pad($municipioIbge, 6, ' ', STR_PAD_LEFT), // prd-ibge
                str_pad($cid10, 4, ' ', STR_PAD_RIGHT), // prd-cid
                str_pad($idadePaciente, 3, '0', STR_PAD_LEFT), // prd-idade
                str_pad($quantidade, 6, '0', STR_PAD_LEFT), // prd-qt
                str_pad($caracterAtendimento, 2, '0', STR_PAD_LEFT), // prd-caten
                str_pad($numeroAutorizacao, 13, ' ', STR_PAD_LEFT), // prd-naut
                'BPA', // prd-org
                str_pad($nomePaciente, 30, ' ', STR_PAD_RIGHT), // prd-nmppac
                $dataNascimentoFormatada, // prd-dtnasc
                str_pad($racaCor, 2, ' ', STR_PAD_LEFT), // prd-raca
                str_pad($etnia, 4, ' ', STR_PAD_LEFT), // prd-etnia
                str_pad($nacionalidade, 3, '0', STR_PAD_LEFT), // prd-nac
                str_pad($codigoServico, 3, ' ', STR_PAD_LEFT), // prd-srv
                substr(str_pad($codigoClassificacao, 3, ' ', STR_PAD_RIGHT), 0, 3), // prd-clf
                str_pad($codigoSequenciaEquipe, 8, ' ', STR_PAD_LEFT), // prd-eqip-Seq
                str_pad($codigoAreaEquipe, 4, ' ', STR_PAD_LEFT), // prd-eqip-Area
                str_pad('', 14, ' ', STR_PAD_LEFT), // prd-cnpj
                str_pad($cep, 8, ' ', STR_PAD_LEFT), // prd-cep-pcnte
                str_pad($codigoLogradouro, 3, '0', STR_PAD_LEFT), // prd-lograd-pcnte
                str_pad($endereco, 30, ' ', STR_PAD_RIGHT), // prd-end-pcnte
                str_pad($complemento, 10, ' ', STR_PAD_RIGHT), // prd-compl-pcnte
                str_pad($numero, 5, ' ', STR_PAD_RIGHT), // prd-num-pcnte
                str_pad($bairro, 30, ' ', STR_PAD_RIGHT), // prd-bairro-pcnte
                str_pad($telefone, 11, ' ', STR_PAD_RIGHT), // prd-tel-pcnte
                str_pad($email, 40, ' ', STR_PAD_RIGHT), // prd-email-pcnte
                str_pad('', 10, ' ', STR_PAD_LEFT), // prd-ine
                str_pad($cpf, 11, ' ', STR_PAD_LEFT), // prd-cpf-pcnte
                str_pad($situacaoRua, 1, ' ', STR_PAD_RIGHT), // prd-situacao-rua
                "\r\n"
            ];

            $conteudo .= implode('', $linha);
            $sequencia++;

            // Reinicia sequência se passar de 99 (máximo por folha)
            if ($sequencia > 99) {
                $sequencia = 1;
                $folha++;
            }
        }

    // Calcula o campo de controle final conforme especificação
    $resto = $somaProcedimentos % 1111;
    $campoControle = 1111 + $resto;

    // Garante que o campo de controle tem 4 dígitos (ou mais se necessário) - pad à esquerda com zeros
    $campoControleStr = str_pad($campoControle, 4, '0', STR_PAD_LEFT);

    // Agora compomos o header completo: prefix + campoControle + suffix + nova linha
    $header = array_merge($headerPrefix, [$campoControleStr], $headerSuffix);
    // Substitui o início do conteúdo pelo header completo seguido do restante (que atualmente contém apenas as linhas de atendimento)
    // Como $conteudo atualmente tem o headerPrefix concatenado seguido das linhas (adicionadas no loop), removemos o prefix atual e inserimos o header completo.
    $conteudo = implode('', $header) . "\r\n" . substr($conteudo, strlen(implode('', $headerPrefix)));

        // Força o download do arquivo
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        echo $conteudo;
        exit();

    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao gerar arquivo BPA-I: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
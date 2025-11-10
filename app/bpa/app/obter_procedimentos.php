<?php
session_start();
require_once "classes/db.class.php";
require_once "classes/painel.class.php";

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = DB::connect();

        // Aplica os mesmos filtros da consulta anterior
        $filtros = [
            'competencia' => $_POST['competencia'] ?? '',
            'data_inicio' => $_POST['data_inicio'] ?? '',
            'data_fim' => $_POST['data_fim'] ?? ''
        ];

        $atendimentos = Painel::GetAtendimentos($filtros);
        $clinicaData = Painel::GetClinica();

        if (empty($atendimentos['dados']) || empty($clinicaData['dados'][0])) {
            throw new Exception("Nenhum atendimento ou clínica não encontrada.");
        }

        $clinica = $clinicaData['dados'][0];

        // Define o nome do arquivo
        $competencia = !empty($filtros['competencia']) ? $filtros['competencia'] : date('Ym');
        $nomeArquivo = "BPAI_{$clinica['cnes']}_{$competencia}_" . date('YmdHis') . ".txt";

        // Cabeçalho do arquivo BPA-I
        $conteudo = "";

        // 1. Header (cabeçalho do arquivo)
        $header = [
            '01', // Identificação de linha do Header
            '#BPA#',
            str_pad($competencia, 6, '0', STR_PAD_RIGHT),
            str_pad(count($atendimentos['dados']), 6, '0', STR_PAD_LEFT),
            '000001',
            '1111',
            str_pad(substr($clinica['nome'] ?? '', 0, 30), 30, ' ', STR_PAD_RIGHT),
            str_pad($clinica['sigla'] ?? 'VIVENCIAR', 6, ' ', STR_PAD_RIGHT),
            str_pad($clinica['cnpj'] ?? '0', 14, '0', STR_PAD_LEFT),
            str_pad($clinica['secretaria_saude'] ?? 'SECRETARIA ESTADUAL DE SAUDE', 40, ' ', STR_PAD_RIGHT),
            $clinica['tipo_orgao_destino'] ?? 'E',
            str_pad($clinica['versao_sistema'] ?? '1.0.0', 10, ' ', STR_PAD_RIGHT),
            "\r\n"
        ];

        $conteudo .= implode('', $header) . "\r\n";

        // 2. Linhas de atendimento (BPA-I)
        $sequencia = 1;
        $somaProcedimentos = 0;
        $folha = 1;

        foreach ($atendimentos['dados'] as $atendimento) {
            // Busca dados relacionados com fallback para arrays vazios
            $paciente = Painel::getPacienteById($atendimento['paciente_id'] ?? 0) ?? [];
            $profissional = Painel::getProfissionalById($atendimento['profissional_id'] ?? 0) ?? [];
            $procedimento = Painel::getProcedimentoById($atendimento['procedimento_id'] ?? 0) ?? [];

            // Campos do atendimento com fallback
            $codigoProcedimento = $procedimento['codigo'] ?? '0000000000';
            $quantidade = $atendimento['quantidade'] ?? 1;
            $dataAtendimento = $atendimento['data_atendimento'] ?? date('Y-m-d');
            $cid10 = $atendimento['cid_10'] ?? '';
            $idadePaciente = $atendimento['idade_paciente'] ?? '0';
            $caracterAtendimento = $atendimento['caracter_atendimento'] ?? '';
            $numeroAutorizacao = $atendimento['numero_autorizacao'] ?? '0';
            $codigoServico = $atendimento['codigo_servico'] ?? '000'; // Precisa ser preenchido
            $codigoClassificacao = $atendimento['codigo_classificacao'] ?? '000'; // Precisa ser preenchido
            $codigoSequenciaEquipe = $atendimento['codigo_sequencia_equipe'] ?? '00000000';
            $codigoAreaEquipe = $atendimento['codigo_area_equipe'] ?? '0000';

            // Campos do profissional com fallback
            $cnsProfissional = $profissional['cns'] ?? '0';
            $cboProfissional = $profissional['cbo'] ?? '';

            // Campos do paciente com fallback
            $cnsPaciente = $paciente['cns'] ?? '';
            $sexo = strtoupper(substr($paciente['sexo'] ?? 'M', 0, 1));
            $municipioIbge = $paciente['municipio_ibge'] ?? '000000'; // ✅ Agora pega do paciente
            $nomePaciente = substr($paciente['nome'] ?? '', 0, 30);
            $dataNascimento = $paciente['data_nascimento'] ?? date('Y-m-d');
            $racaCor = $paciente['raca_cor'] ?? '00';
            $etnia = $paciente['etnia'] ?? '0000';
            $nacionalidade = $paciente['nacionalidade'] ?? '000';
            $cep = $paciente['cep'] ?? '00000000';
            $codigoLogradouro = $paciente['codigo_logradouro'] ?? '000';
            $endereco = substr($paciente['endereco'] ?? '', 0, 30);
            $complemento = substr($paciente['complemento'] ?? '', 0, 10);
            $numero = substr($paciente['numero'] ?? 'SN', 0, 5);
            $bairro = substr($paciente['bairro'] ?? '', 0, 30);
            $telefone = preg_replace('/[^0-9]/', '', $paciente['telefone'] ?? '');
            $email = substr($paciente['email'] ?? '', 0, 40);
            $cpf = $paciente['cpf'] ?? '';
            $situacaoRua = substr($paciente['situacao_rua'] ?? 'N', 0, 1);

            // Regra: CNS ou CPF, nunca ambos
            if (!empty($cpf)) {
                $cnsPaciente = '';
            } else {
                $cpf = '';
            }

            // Formatação de datas
            $dataAtendimentoFormatada = date('Ymd', strtotime($dataAtendimento));
            $dataNascimentoFormatada = date('Ymd', strtotime($dataNascimento));

            // Calcula campo de controle
            $somaProcedimentos += (int)$codigoProcedimento + (int)$quantidade;

            // Monta linha do arquivo
            $linha = [
                '03',
                str_pad($clinica['cnes'] ?? '0', 7, '0', STR_PAD_LEFT),
                str_pad($competencia, 6, '0', STR_PAD_RIGHT),
                str_pad($cnsProfissional, 15, '0', STR_PAD_LEFT),
                str_pad($cboProfissional, 6, ' ', STR_PAD_RIGHT),
                $dataAtendimentoFormatada,
                str_pad($folha, 3, '0', STR_PAD_LEFT),
                str_pad($sequencia, 2, '0', STR_PAD_LEFT),
                str_pad($codigoProcedimento, 10, '0', STR_PAD_LEFT),
                str_pad($cnsPaciente, 15, '0', STR_PAD_LEFT),
                $sexo,
                str_pad($municipioIbge, 6, '0', STR_PAD_LEFT), // ✅ Município IBGE do paciente
                str_pad($cid10, 4, ' ', STR_PAD_RIGHT),
                str_pad($idadePaciente, 3, '0', STR_PAD_LEFT),
                str_pad($quantidade, 6, '0', STR_PAD_LEFT),
                str_pad($caracterAtendimento, 2, ' ', STR_PAD_RIGHT),
                str_pad($numeroAutorizacao, 13, '0', STR_PAD_LEFT),
                'BPA',
                str_pad($nomePaciente, 30, ' ', STR_PAD_RIGHT),
                date('Ymd', strtotime($dataNascimento)),
                str_pad($racaCor, 2, '0', STR_PAD_LEFT),
                str_pad($etnia, 4, '0', STR_PAD_LEFT),
                str_pad($nacionalidade, 3, '0', STR_PAD_LEFT),
                str_pad($codigoServico, 3, '0', STR_PAD_LEFT), // ✅ PREENCHIDO
                str_pad($codigoClassificacao, 3, '0', STR_PAD_LEFT), // ✅ PREENCHIDO
                str_pad($codigoSequenciaEquipe, 8, '0', STR_PAD_LEFT),
                str_pad($codigoAreaEquipe, 4, '0', STR_PAD_LEFT),
                str_pad('', 14, ' ', STR_PAD_RIGHT), // ✅ CNPJ - agora vazio
                str_pad($cep, 8, '0', STR_PAD_LEFT),
                str_pad($codigoLogradouro, 3, '0', STR_PAD_LEFT),
                str_pad($endereco, 30, ' ', STR_PAD_RIGHT),
                str_pad($complemento, 10, ' ', STR_PAD_RIGHT),
                str_pad($numero, 5, ' ', STR_PAD_RIGHT),
                str_pad($bairro, 30, ' ', STR_PAD_RIGHT),
                str_pad($telefone, 11, ' ', STR_PAD_RIGHT),
                str_pad($email, 40, ' ', STR_PAD_RIGHT),
                str_pad('', 10, ' ', STR_PAD_RIGHT), // ✅ INE - agora vazio
                str_pad($cpf, 11, '0', STR_PAD_LEFT),
                str_pad($situacaoRua, 1, ' ', STR_PAD_RIGHT),
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

        // Atualiza o campo de controle no header
        $conteudo = substr_replace($conteudo, str_pad($campoControle, 4, '0', STR_PAD_LEFT), strpos($conteudo, '1111'), 4);

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
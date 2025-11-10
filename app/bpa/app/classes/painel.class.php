<?php

class Painel
{
    public static function GetClinica(){

        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM clinica ");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        return ["dados" => $resultado];

    }

    public static function GetProfissionais(){

        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM profissional ");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        return ["dados" => $resultado];
    }
    
    public static function GetPacientes(){

        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM paciente ");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        return ["dados" => $resultado];
    }

    
    public static function GetProcedimentos(){

        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM procedimento ");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        return ["dados" => $resultado];
    }

    public static function getPacienteById($id)
    {
        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM paciente WHERE id = :id");
        $rs->bindParam(':id', $id, PDO::PARAM_INT);
        $rs->execute();
        return $rs->fetch(PDO::FETCH_ASSOC);
    }

    // Em painel.class.php
    public static function getAtendimentos($filtros = [], $pagina = 1, $por_pagina = 10)
    {
        $db = DB::connect();

        $sql = "SELECT a.*, 
               p.nome as paciente_nome, 
               pr.nome as profissional_nome, 
               pc.codigo as procedimento_codigo
        FROM atendimento a
        JOIN paciente p ON a.paciente_id = p.id
        JOIN profissional pr ON a.profissional_id = pr.id
        JOIN procedimento pc ON a.procedimento_id = pc.id
        WHERE 1=1";
        
        $params = [];

        if (!empty($filtros['competencia'])) {
            $sql .= " AND a.competencia = :competencia";
            $params[':competencia'] = $filtros['competencia'];
        }

        if (!empty($filtros['data_inicio'])) {
            $sql .= " AND a.data_atendimento >= :data_inicio";
            $params[':data_inicio'] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $sql .= " AND a.data_atendimento <= :data_fim";
            $params[':data_fim'] = $filtros['data_fim'];
        }

        // Filtro por profissional
        if (!empty($filtros['profissional_id'])) {
            $sql .= " AND a.profissional_id = :profissional_id";
            $params[':profissional_id'] = $filtros['profissional_id'];
        }

        // Filtro por procedimento
        if (!empty($filtros['procedimento_id'])) {
            $sql .= " AND a.procedimento_id = :procedimento_id";
            $params[':procedimento_id'] = $filtros['procedimento_id'];
        }

        // Filtro por nome do paciente (busca geral)
        if (!empty($filtros['paciente_nome']) && empty($filtros['paciente_id'])) {
            $sql .= " AND p.nome LIKE :paciente_nome";
            $params[':paciente_nome'] = '%' . $filtros['paciente_nome'] . '%';
        }

        // Filtro por ID do paciente (busca específica via autocomplete)
        if (!empty($filtros['paciente_id'])) {
            $sql .= " AND p.id = :paciente_id";
            $params[':paciente_id'] = $filtros['paciente_id'];
        }

        // Contar total de registros
        $countSql = "SELECT COUNT(*) FROM ($sql) AS total";
        $stmt = $db->prepare($countSql);
        $stmt->execute($params);
        $totalRegistros = $stmt->fetchColumn();

        // Calcular offset
        $offset = ($pagina - 1) * $por_pagina;

        // Adicionar limit e offset
        $sql .= " LIMIT $por_pagina OFFSET $offset";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total' => $totalRegistros,
            'dados' => $dados,
            'pagina' => $pagina,
            'por_pagina' => $por_pagina
        ];
    }

    public static function excluirAtendimento($id)
{
    try {
        $db = DB::connect();

        // Verifica se o atendimento existe
        $atendimento = self::getAtendimentoById($id);
        if (!$atendimento) {
            return ['tipo' => 'erro', 'texto' => 'Atendimento não encontrado.'];
        }

        // Prepara e executa a exclusão
        $stmt = $db->prepare("DELETE FROM atendimento WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return ['tipo' => 'sucesso', 'texto' => 'Atendimento excluído com sucesso.'];
    } catch (PDOException $e) {
        return ['tipo' => 'erro', 'texto' => 'Erro ao excluir atendimento: ' . $e->getMessage()];
    }
}

    public static function getProfissionalById($id)
    {
        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM profissional WHERE id = :id");
        $rs->bindParam(':id', $id, PDO::PARAM_INT);
        $rs->execute();
        return $rs->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAtendimentoById($id)
{
    $db = DB::connect();
    $sql = "SELECT * FROM atendimento WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public static function getProcedimentoById($id) {
        try {
            $db = DB::connect();
            
            $sql = "SELECT 
                        p.id,
                        p.codigo,
                        p.descricao,
                        p.especialidade,
                        p.ativo,
                        p.servico,
                        p.classificacao
                    FROM procedimento p
                    WHERE p.id = :id
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $procedimento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$procedimento) {
                return false;
            }
            
            return $procedimento;
            
        } catch (PDOException $e) {
            error_log("Erro ao buscar procedimento: " . $e->getMessage());
            return false;
        }
    }

   public static function SetPaciente($dados)
    {
        try {
            $db = DB::connect();

            // SQL para inserir o paciente
            $sql = "INSERT INTO paciente (
                        nome, data_nascimento, sexo, cns, cpf, raca_cor, etnia, 
                        nacionalidade, municipio_ibge, cep, codigo_logradouro, 
                        endereco, numero, complemento, bairro, telefone, email, situacao_rua
                    ) VALUES (
                        :nome, :data_nascimento, :sexo, :cns, :cpf, :raca_cor, :etnia, 
                        :nacionalidade, :municipio_ibge, :cep, :codigo_logradouro, 
                        :endereco, :numero, :complemento, :bairro, :telefone, :email, :situacao_rua
                    )";

            $stmt = $db->prepare($sql);

            // Executa a inserção
            return $stmt->execute($dados);
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar paciente: " . $e->getMessage());
            return false;
        }
        }

        public static function SetProcedimento($dados)
    {
        try {
            $db = DB::connect();

            // SQL para inserir o procedimento
            $sql = "INSERT INTO procedimento (
                        codigo, descricao, especialidade, ativo, servico, classificacao, caracter_atendimento
                    ) VALUES (
                        :codigo, :descricao, :especialidade, :ativo, :servico, :classificacao, :caracter_atendimento
                    )";

            $stmt = $db->prepare($sql);

            // Executa a inserção
            return $stmt->execute($dados);
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar novo procedimento: " . $e->getMessage());
            return false;
        }
        }

    public static function UpdateProfissional($dados) {
        try {
            $db = DB::connect();

            $sql = "UPDATE profissional SET 
                        nome = :nome,
                        cpf = :cpf,
                        cns = :cns,
                        telefone = :telefone,
                        email = :email,
                        especialidade = :especialidade,
                        cbo = :cbo
                    WHERE id = :id LIMIT 1";

            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':nome' => $dados['nome'],
                ':cpf' => $dados['cpf'],
                ':cns' => $dados['cns'],
                ':telefone' => $dados['telefone'],
                ':email' => $dados['email'],
                ':especialidade' => $dados['especialidade'],
                ':cbo' => $dados['cbo'],
                ':id' => $dados['id']
            ]);

        } catch (PDOException $e) {
            error_log("Erro ao atualizar profissional: " . $e->getMessage());
            return false;
        }
    }


   public static function UpdatePaciente($dados) {
        $pdo = Db::connect(); // Garanta que isso retorna uma conexão PDO válida

        $sql = "UPDATE paciente SET 
                    nome = :nome,
                    data_nascimento = :data_nascimento,
                    sexo = :sexo,
                    cns = :cns,
                    cpf = :cpf,
                    raca_cor = :raca_cor,
                    etnia = :etnia,
                    nacionalidade = :nacionalidade,
                    municipio_ibge = :municipio_ibge,
                    cep = :cep,
                    codigo_logradouro = :codigo_logradouro,
                    endereco = :endereco,
                    numero = :numero,
                    complemento = :complemento,
                    bairro = :bairro,
                    telefone = :telefone,
                    email = :email,
                    situacao_rua = :situacao_rua
                WHERE id = :id";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $dados['nome'],
                ':data_nascimento' => $dados['data_nascimento'],
                ':sexo' => $dados['sexo'],
                ':cns' => $dados['cns'] ?? null,
                ':cpf' => $dados['cpf'] ?? null,
                ':raca_cor' => $dados['raca_cor'],
                ':etnia' => $dados['etnia'] ?? null,
                ':nacionalidade' => $dados['nacionalidade'],
                ':municipio_ibge' => $dados['municipio_ibge'],
                ':cep' => preg_replace('/[^0-9]/', '', $dados['cep']),
                ':codigo_logradouro' => $dados['codigo_logradouro'],
                ':endereco' => $dados['endereco'],
                ':numero' => $dados['numero'],
                ':complemento' => $dados['complemento'] ?? null,
                ':bairro' => $dados['bairro'],
                ':telefone' => preg_replace('/[^0-9]/', '', $dados['telefone']),
                ':email' => $dados['email'] ?? null,
                ':situacao_rua' => $dados['situacao_rua'],
                ':id' => $dados['id']
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao editar paciente: " . $e->getMessage());
            var_dump($e->getMessage());
            return false;
        }
    }


    public static function ExcluirPaciente($id) {
    $pdo = Db::connect();

    $sql = "DELETE FROM paciente WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    return $stmt->execute();
}


    public static function ExcluirProfissional($id) {
    $pdo = Db::connect();

    $sql = "DELETE FROM profissional WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    return $stmt->execute();
}

    public static function SetProfissional($dados)
    {
        try {
            $db = DB::connect();

            $sql1 = "INSERT INTO profissional (
                        nome, cpf, cns, telefone, email, especialidade, cbo
                    ) VALUES ( '".$dados['nome']."', '".$dados['cpf']."', '".$dados['cns']."', '".$dados['telefone']."', '".$dados['email']."', '".$dados['especialidade']."', '".$dados['cbo']."')";
            
            echo $sql1;
            $stmt = $db->prepare($sql1);
            return $stmt->execute();
             
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar profissional: " . $e->getMessage());
            return false;
        }
    }

public static function SetAtendimento($dados)
{
    try {
        $db = DB::connect();

        // Cálculo da idade do paciente
        if (empty($dados['paciente_id']) || empty($dados['data_atendimento'])) {
            throw new Exception("Dados obrigatórios não fornecidos.");
        }

        $paciente = self::getPacienteById($dados['paciente_id']);
        if (!$paciente) {
            throw new Exception("Paciente não encontrado.");
        }

        $dataNascimento = new DateTime($paciente['data_nascimento']);
        $dataAtendimento = new DateTime($dados['data_atendimento']);
        $idade = $dataNascimento->diff($dataAtendimento)->y;

        // Monta os dados finais com valores padrão
        $dadosAtendimento = [
            'clinica_id' => self::GetClinica()['dados'][0]['id'],
            'paciente_id' => $dados['paciente_id'],
            'profissional_id' => $dados['profissional_id'],
            'procedimento_id' => $dados['procedimento_id'],
            'competencia' => $dados['competencia'],
            'data_atendimento' => $dados['data_atendimento'],
            'quantidade' => $dados['quantidade'] ?? 1,
            'idade_paciente' => $idade,
            'cid_10' => $dados['cid'] ?? null,
            'caracter_atendimento' => $dados['caracter_atendimento'] ?? null,
            'numero_autorizacao' => null,
            'origem_informacao' => 'BPA',
            'folha_bpa' => null,
            'sequencia_bpa' => null
        ];

        // SQL para inserir o atendimento
        $sql = "INSERT INTO atendimento (
                    clinica_id, paciente_id, profissional_id, procedimento_id,
                    competencia, data_atendimento, quantidade, idade_paciente,
                    cid_10, caracter_atendimento, numero_autorizacao, origem_informacao,
                    folha_bpa, sequencia_bpa
                ) VALUES (
                    :clinica_id, :paciente_id, :profissional_id, :procedimento_id,
                    :competencia, :data_atendimento, :quantidade, :idade_paciente,
                    :cid_10, :caracter_atendimento, :numero_autorizacao, :origem_informacao,
                    :folha_bpa, :sequencia_bpa
                )";

        // REMOVIDO: echo $sql;

        $stmt = $db->prepare($sql);
        $stmt->execute($dadosAtendimento);
        
        // REMOVIDO: Linhas que modificavam $_SESSION['mensagem']
        return true;

    } catch (PDOException $e) {
        error_log("Erro ao inserir atendimento: " . $e->getMessage());
        // REMOVIDO: Linhas que modificavam $_SESSION['mensagem']
        return false;
    } catch (Exception $e) {
        error_log("Erro lógico ao inserir atendimento: " . $e->getMessage());
        // REMOVIDO: Linhas que modificavam $_SESSION['mensagem']
        return false;
    }
}
    

public static function getAtendimentosPorDia()
    {
        $db = DB::connect();
        $sql = "SELECT data_atendimento, COUNT(*) as total FROM atendimento GROUP BY data_atendimento ORDER BY data_atendimento ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAtendimentosPorEspecialidade()
    {
        $db = DB::connect();
        $sql = "SELECT p.especialidade, COUNT(a.id) as total FROM atendimento a JOIN procedimento p ON a.procedimento_id = p.id GROUP BY p.especialidade ORDER BY total DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAtendimentosPorProfissional()
    {
        $db = DB::connect();
        $sql = "SELECT pr.nome as profissional, COUNT(a.id) as total FROM atendimento a JOIN profissional pr ON a.profissional_id = pr.id GROUP BY pr.nome ORDER BY total DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAtendimentosPorPaciente()
    {
        $db = DB::connect();
        $sql = "SELECT p.nome as paciente, COUNT(a.id) as total FROM atendimento a JOIN paciente p ON a.paciente_id = p.id GROUP BY p.nome ORDER BY total DESC LIMIT 10";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAtendimentosPorCompetencia()
    {
        $db = DB::connect();
        $sql = "SELECT competencia, COUNT(*) as total FROM atendimento GROUP BY competencia ORDER BY competencia ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTotalProcedimentos()
    {
        $db = DB::connect();
        $sql = "SELECT SUM(quantidade) as total_procedimentos FROM atendimento";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getMediaIdadePacientes()
    {
        $db = DB::connect();
        $sql = "SELECT AVG(idade_paciente) as media_idade FROM atendimento WHERE idade_paciente IS NOT NULL";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAtendimentosPorCid10()
    {
        $db = DB::connect();
        $sql = "SELECT cid_10, COUNT(*) as total FROM atendimento WHERE cid_10 IS NOT NULL GROUP BY cid_10 ORDER BY total DESC LIMIT 10";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public static function getBigNumbers($filtros = [])
    {
        $db = DB::connect();
        $sql = "SELECT 
                    COUNT(a.id) as total_atendimentos,
                    COUNT(DISTINCT a.paciente_id) as total_pacientes,
                    COUNT(DISTINCT a.profissional_id) as total_profissionais,
                    SUM(a.quantidade) as total_procedimentos
                FROM atendimento a
                WHERE 1=1";
        
        $params = [];

        if (!empty($filtros["competencia"])) {
            $sql .= " AND a.competencia = :competencia";
            $params[":competencia"] = $filtros["competencia"];
        }

        if (!empty($filtros["profissional_id"])) {
            $sql .= " AND a.profissional_id = :profissional_id";
            $params[":profissional_id"] = $filtros["profissional_id"];
        }

        if (!empty($filtros["procedimento_id"])) {
            $sql .= " AND a.procedimento_id = :procedimento_id";
            $params[":procedimento_id"] = $filtros["procedimento_id"];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



}
<?php
include_once "../../../classes/db.class.php";

class Paciente
{
    private $db;
    
    public function __construct()
    {
        try {
            $this->db = DB::connect();
        } catch (Exception $e) {
            throw new Exception("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }
    
    /**
     * Cadastra um novo paciente
     */
    public function cadastrar($dados)
    {
        try {
            // Valida os dados
            $erros = $this->validarDados($dados);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dados
                ];
            }
            
            // Verifica se o CNS já existe
            if (!empty(trim($dados['cns'])) && $this->existeCNS(trim($dados['cns']))) {
                return [
                    'sucesso' => false,
                    'erros' => ['Já existe um paciente cadastrado com este CNS'],
                    'dados' => $dados
                ];
            }
            
            // Prepara os dados para inserção
            $dadosLimpos = $this->limparDados($dados);
            
            // Insere o paciente
            $sql = "INSERT INTO paciente (
                cns, nome, data_nascimento, sexo, raca_cor, etnia, nacionalidade,
                codigo_logradouro, endereco, numero, complemento, bairro, cep,
                telefone, email, situacao_rua
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['cns'],
                $dadosLimpos['nome'],
                $dadosLimpos['data_nascimento'],
                $dadosLimpos['sexo'],
                $dadosLimpos['raca_cor'],
                $dadosLimpos['etnia'],
                $dadosLimpos['nacionalidade'],
                $dadosLimpos['codigo_logradouro'],
                $dadosLimpos['endereco'],
                $dadosLimpos['numero'],
                $dadosLimpos['complemento'],
                $dadosLimpos['bairro'],
                $dadosLimpos['cep'],
                $dadosLimpos['telefone'],
                $dadosLimpos['email'],
                $dadosLimpos['situacao_rua']
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Paciente cadastrado com sucesso!',
                    'id' => $this->db->lastInsertId(),
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao cadastrar paciente. Tente novamente.'],
                    'dados' => $dados
                ];
            }
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erros' => ['Erro no sistema: ' . $e->getMessage()],
                'dados' => $dados
            ];
        }
    }
    
    /**
     * Lista pacientes com paginação
     */
    public function listar($limite = 50, $offset = 0, $busca = '')
    {
        try {
            $where = '';
            $params = [];
            
            if (!empty($busca)) {
                $where = "WHERE nome LIKE ? OR cns LIKE ?";
                $params = ["%$busca%", "%$busca%"];
            }
            
            $sql = "SELECT id, cns, nome, data_nascimento, telefone 
                    FROM paciente 
                    $where
                    ORDER BY nome 
                    LIMIT ? OFFSET ?";
            
            $params[] = $limite;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Busca paciente por ID
     */
    public function buscarPorId($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM paciente WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Atualiza paciente
     */
    public function atualizar($id, $dados)
    {
        try {
            // Valida os dados
            $erros = $this->validarDados($dados, $id);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dados
                ];
            }
            
            // Verifica se o CNS já existe (exceto para o próprio paciente)
            if (!empty(trim($dados['cns'])) && $this->existeCNS(trim($dados['cns']), $id)) {
                return [
                    'sucesso' => false,
                    'erros' => ['Já existe um paciente cadastrado com este CNS'],
                    'dados' => $dados
                ];
            }
            
            // Prepara os dados para atualização
            $dadosLimpos = $this->limparDados($dados);
            
            // Atualiza o paciente
            $sql = "UPDATE paciente SET 
                cns = ?, nome = ?, data_nascimento = ?, sexo = ?, raca_cor = ?, 
                etnia = ?, nacionalidade = ?, codigo_logradouro = ?, endereco = ?, 
                numero = ?, complemento = ?, bairro = ?, cep = ?, telefone = ?, 
                email = ?, situacao_rua = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['cns'],
                $dadosLimpos['nome'],
                $dadosLimpos['data_nascimento'],
                $dadosLimpos['sexo'],
                $dadosLimpos['raca_cor'],
                $dadosLimpos['etnia'],
                $dadosLimpos['nacionalidade'],
                $dadosLimpos['codigo_logradouro'],
                $dadosLimpos['endereco'],
                $dadosLimpos['numero'],
                $dadosLimpos['complemento'],
                $dadosLimpos['bairro'],
                $dadosLimpos['cep'],
                $dadosLimpos['telefone'],
                $dadosLimpos['email'],
                $dadosLimpos['situacao_rua'],
                $id
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Paciente atualizado com sucesso!',
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao atualizar paciente. Tente novamente.'],
                    'dados' => $dados
                ];
            }
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erros' => ['Erro no sistema: ' . $e->getMessage()],
                'dados' => $dados
            ];
        }
    }
    
    /**
     * Exclui paciente
     */
    public function excluir($id)
    {
        try {
            // Verifica se paciente tem atendimentos vinculados
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM atendimento WHERE paciente_id = ?");
            $stmt->execute([$id]);
            $temAtendimentos = $stmt->fetchColumn();
            
            if ($temAtendimentos > 0) {
                return [
                    'sucesso' => false,
                    'erros' => ['Não é possível excluir paciente com atendimentos registrados.']
                ];
            }
            
            $stmt = $this->db->prepare("DELETE FROM paciente WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Paciente excluído com sucesso!'
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao excluir paciente.']
                ];
            }
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erros' => ['Erro no sistema: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Valida os dados do paciente
     */
    private function validarDados($dados, $idExcluir = null)
    {
        $erros = [];
        
        // Validação de campos obrigatórios
        if (empty(trim($dados['nome']))) {
            $erros[] = "Nome completo é obrigatório";
        } elseif (strlen(trim($dados['nome'])) > 100) {
            $erros[] = "Nome completo deve ter no máximo 100 caracteres";
        }
        
        if (empty($dados['data_nascimento'])) {
            $erros[] = "Data de nascimento é obrigatória";
        } elseif (!$this->validarData($dados['data_nascimento'])) {
            $erros[] = "Data de nascimento inválida";
        }
        
        if (empty($dados['sexo']) || !in_array($dados['sexo'], ['M', 'F'])) {
            $erros[] = "Sexo é obrigatório";
        }
        
        if (empty($dados['raca_cor']) || !in_array($dados['raca_cor'], ['01', '02', '03', '04', '05', '99'])) {
            $erros[] = "Raça/Cor é obrigatória";
        }
        
        if (empty($dados['nacionalidade']) || !in_array($dados['nacionalidade'], ['10', '20', '30'])) {
            $erros[] = "Nacionalidade é obrigatória";
        }
        
        if (empty($dados['codigo_logradouro']) || !in_array($dados['codigo_logradouro'], ['81', '8'])) {
            $erros[] = "Tipo de logradouro é obrigatório";
        }
        
        if (empty(trim($dados['endereco']))) {
            $erros[] = "Logradouro é obrigatório";
        }
        
        if (empty(trim($dados['numero']))) {
            $erros[] = "Número é obrigatório";
        }
        
        if (empty(trim($dados['bairro']))) {
            $erros[] = "Bairro é obrigatório";
        }
        
        if (empty(trim($dados['cep']))) {
            $erros[] = "CEP é obrigatório";
        } elseif (!$this->validarCEP(trim($dados['cep']))) {
            $erros[] = "CEP inválido";
        }
        
        if (empty(trim($dados['telefone']))) {
            $erros[] = "Telefone é obrigatório";
        } elseif (!$this->validarTelefone(trim($dados['telefone']))) {
            $erros[] = "Telefone inválido";
        }
        
        if (!empty($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido";
        }
        
        if (empty($dados['situacao_rua']) || !in_array($dados['situacao_rua'], ['S', 'N'])) {
            $erros[] = "Situação de rua é obrigatória";
        }
        
        // Valida CNS se informado
        if (!empty(trim($dados['cns'])) && !$this->validarCNS(trim($dados['cns']))) {
            $erros[] = "CNS inválido";
        }
        
        return $erros;
    }
    
    /**
     * Limpa e formata os dados do paciente
     */
    private function limparDados($dados)
    {
        return [
            'cns' => !empty($dados['cns']) ? preg_replace('/[^0-9]/', '', trim($dados['cns'])) : null,
            'nome' => trim(htmlspecialchars($dados['nome'])),
            'data_nascimento' => $dados['data_nascimento'],
            'sexo' => $dados['sexo'],
            'raca_cor' => $dados['raca_cor'],
            'etnia' => !empty($dados['etnia']) ? trim(htmlspecialchars($dados['etnia'])) : null,
            'nacionalidade' => $dados['nacionalidade'],
            'codigo_logradouro' => $dados['codigo_logradouro'],
            'endereco' => trim(htmlspecialchars($dados['endereco'])),
            'numero' => trim(htmlspecialchars($dados['numero'])),
            'complemento' => !empty($dados['complemento']) ? trim(htmlspecialchars($dados['complemento'])) : null,
            'bairro' => trim(htmlspecialchars($dados['bairro'])),
            'cep' => preg_replace('/[^0-9]/', '', trim($dados['cep'])),
            'telefone' => preg_replace('/[^0-9]/', '', trim($dados['telefone'])),
            'email' => !empty($dados['email']) ? trim(htmlspecialchars($dados['email'])) : null,
            'situacao_rua' => $dados['situacao_rua']
        ];
    }
    
    /**
     * Verifica se CNS já existe
     */
    private function existeCNS($cns, $idExcluir = null)
    {
        $sql = "SELECT id FROM paciente WHERE cns = ?";
        $params = [$cns];
        
        if ($idExcluir) {
            $sql .= " AND id != ?";
            $params[] = $idExcluir;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Valida CNS
     */
    private function validarCNS($cns)
    {
        $cns = preg_replace('/[^0-9]/', '', $cns);
        return strlen($cns) === 15 && is_numeric($cns);
    }
    
    /**
     * Valida data
     */
    private function validarData($data)
    {
        $dataObj = DateTime::createFromFormat('Y-m-d', $data);
        return $dataObj && $dataObj->format('Y-m-d') === $data;
    }
    
    /**
     * Valida CEP
     */
    private function validarCEP($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        return strlen($cep) === 8;
    }
    
    /**
     * Valida telefone
     */
    private function validarTelefone($telefone)
    {
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        return strlen($telefone) >= 10 && strlen($telefone) <= 11;
    }
    
    /**
     * Busca total de pacientes
     */
    public function getTotalPacientes($busca = '')
    {
        try {
            $where = '';
            $params = [];
            
            if (!empty($busca)) {
                $where = "WHERE nome LIKE ? OR cns LIKE ?";
                $params = ["%$busca%", "%$busca%"];
            }
            
            $sql = "SELECT COUNT(*) FROM paciente $where";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }
}
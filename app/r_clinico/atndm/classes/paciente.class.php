<?php
/* Classe de controle dos dados cadastrais dos pacientes*/

Class Paciente_class
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
     * Processa o cadastro de paciente - Fluxo completo
     */
    public function processarCadastroPaciente($dados)
    {
        try {
            // Valida os dados
            $erros = $this->validarDadosPaciente($dados);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dados // Retorna dados para manter no formulário
                ];
            }
            
            // Verifica se o CNS já existe
            if ($this->existeCNS(trim($dados['cns']))) {
                return [
                    'sucesso' => false,
                    'erros' => ['Já existe um paciente cadastrado com este CNS'],
                    'dados' => $dados
                ];
            }
            
            // Prepara os dados para inserção
            $dadosLimpos = $this->limparDadosPaciente($dados);
            
            // Insere o paciente
            $sql = "INSERT INTO pacientes (
                nome, cns, data_nascimento, raca_cor, sexo, etnia, nacionalidade,
                codigo_logradouro, endereco, numero, complemento, bairro, cep,
                telefone, email, situacao_rua, data_cadastro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['nome'],
                $dadosLimpos['cns'],
                $dadosLimpos['data_nascimento'],
                $dadosLimpos['raca_cor'],
                $dadosLimpos['sexo'],
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
                    'dados' => [] // Limpa os dados após sucesso
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
     * Valida os dados do paciente
     */
    private function validarDadosPaciente($dados)
    {
        $erros = [];
        
        // Validação de campos obrigatórios
        if (empty(trim($dados['nome']))) {
            $erros[] = "Nome completo é obrigatório";
        } elseif (strlen(trim($dados['nome'])) > 100) {
            $erros[] = "Nome completo deve ter no máximo 100 caracteres";
        }
        
        if (empty(trim($dados['cns']))) {
            $erros[] = "CNS é obrigatório";
        } elseif (!$this->validarCNS(trim($dados['cns']))) {
            $erros[] = "CNS inválido";
        }
        
        if (empty($dados['data_nascimento'])) {
            $erros[] = "Data de nascimento é obrigatória";
        } elseif (!$this->validarData($dados['data_nascimento'])) {
            $erros[] = "Data de nascimento inválida";
        }
        
        if (empty($dados['raca_cor']) || !in_array($dados['raca_cor'], ['01', '02', '03', '04', '05', '99'])) {
            $erros[] = "Raça/Cor é obrigatória";
        }
        
        if (empty($dados['sexo']) || !in_array($dados['sexo'], ['M', 'F'])) {
            $erros[] = "Sexo é obrigatório";
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
        
        return $erros;
    }
    
    /**
     * Limpa e formata os dados do paciente
     */
    private function limparDadosPaciente($dados)
    {
        return [
            'nome' => trim(htmlspecialchars($dados['nome'])),
            'cns' => preg_replace('/[^0-9]/', '', trim($dados['cns'])),
            'data_nascimento' => $dados['data_nascimento'],
            'raca_cor' => $dados['raca_cor'],
            'sexo' => $dados['sexo'],
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
    private function existeCNS($cns)
    {
        $stmt = $this->db->prepare("SELECT id FROM pacientes WHERE cns = ? LIMIT 1");
        $stmt->execute([$cns]);
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
     * Lista pacientes
     */
    public function listarPacientes($limite = 50, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, cns, data_nascimento, telefone 
                FROM pacientes 
                ORDER BY nome 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limite, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Busca paciente por ID
     */
    public function buscarPaciente($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM pacientes WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }


}

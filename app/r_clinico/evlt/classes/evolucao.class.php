<?php
include_once "../../../classes/db.class.php";

class Evolucao
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
     * Cadastra uma nova evolução
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
            
            // Prepara os dados para inserção
            $dadosLimpos = $this->limparDados($dados);
            
            // Insere a evolução
            $sql = "INSERT INTO evolucao_paciente (
                paciente_id, profissional_id, data_evolucao, descricao, observacao
            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['paciente_id'],
                $dadosLimpos['profissional_id'],
                $dadosLimpos['data_evolucao'],
                $dadosLimpos['descricao'],
                $dadosLimpos['observacao']
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Evolução cadastrada com sucesso!',
                    'id' => $this->db->lastInsertId(),
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao cadastrar evolução. Tente novamente.'],
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
     * Lista evoluções com paginação
     */
    public function listar($limite = 10, $offset = 0, $pacienteId = null)
    {
        try {
            $where = '';
            $params = [];
            
            if ($pacienteId) {
                $where = "WHERE e.paciente_id = ?";
                $params = [$pacienteId];
            }
            
            $sql = "SELECT e.*, p.nome as paciente_nome, prof.nome as profissional_nome 
                    FROM evolucao_paciente e
                    LEFT JOIN paciente p ON e.paciente_id = p.id
                    LEFT JOIN profissional prof ON e.profissional_id = prof.id
                    $where
                    ORDER BY e.data_evolucao DESC
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
     * Busca evoluções por paciente
     */
    public function buscarPorPaciente($pacienteId)
    {
        try {
            $sql = "SELECT e.*, p.nome as paciente_nome, prof.nome as profissional_nome 
                    FROM evolucao_paciente e
                    LEFT JOIN paciente p ON e.paciente_id = p.id
                    LEFT JOIN profissional prof ON e.profissional_id = prof.id
                    WHERE e.paciente_id = ?
                    ORDER BY e.data_evolucao DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$pacienteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Busca evolução por ID
     */
    public function buscarPorId($id)
    {
        try {
            $sql = "SELECT e.*, p.nome as paciente_nome, prof.nome as profissional_nome 
                    FROM evolucao_paciente e
                    LEFT JOIN paciente p ON e.paciente_id = p.id
                    LEFT JOIN profissional prof ON e.profissional_id = prof.id
                    WHERE e.id = ? LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Atualiza evolução
     */
    public function atualizar($id, $dados)
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
            
            // Prepara os dados para atualização
            $dadosLimpos = $this->limparDados($dados);
            
            // Atualiza a evolução
            $sql = "UPDATE evolucao_paciente SET 
                paciente_id = ?, profissional_id = ?, data_evolucao = ?, 
                descricao = ?, observacao = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['paciente_id'],
                $dadosLimpos['profissional_id'],
                $dadosLimpos['data_evolucao'],
                $dadosLimpos['descricao'],
                $dadosLimpos['observacao'],
                $id
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Evolução atualizada com sucesso!',
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao atualizar evolução. Tente novamente.'],
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
     * Exclui evolução
     */
    public function excluir($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM evolucao_paciente WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Evolução excluída com sucesso!'
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao excluir evolução.']
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
     * Valida os dados da evolução
     */
    private function validarDados($dados)
    {
        $erros = [];
        
        // Validação de campos obrigatórios
        if (empty($dados['paciente_id']) || !is_numeric($dados['paciente_id'])) {
            $erros[] = "Paciente é obrigatório";
        }
        
        if (empty($dados['profissional_id']) || !is_numeric($dados['profissional_id'])) {
            $erros[] = "Profissional é obrigatório";
        }
        
        if (empty($dados['data_evolucao'])) {
            $erros[] = "Data da evolução é obrigatória";
        } elseif (!$this->validarData($dados['data_evolucao'])) {
            $erros[] = "Data da evolução inválida";
        }
        
        if (empty(trim($dados['descricao']))) {
            $erros[] = "Descrição da evolução é obrigatória";
        } elseif (strlen(trim($dados['descricao'])) > 1000) {
            $erros[] = "Descrição deve ter no máximo 1000 caracteres";
        }
        
        if (!empty($dados['observacao']) && strlen(trim($dados['observacao'])) > 500) {
            $erros[] = "Observação deve ter no máximo 500 caracteres";
        }
        
        return $erros;
    }
    
    /**
     * Limpa e formata os dados
     */
    private function limparDados($dados)
    {
        return [
            'paciente_id' => (int)$dados['paciente_id'],
            'profissional_id' => (int)$dados['profissional_id'],
            'data_evolucao' => $dados['data_evolucao'],
            'descricao' => trim(htmlspecialchars($dados['descricao'])),
            'observacao' => !empty($dados['observacao']) ? trim(htmlspecialchars($dados['observacao'])) : null
        ];
    }
    
    /**
     * Valida data
     */
    private function validarData($data)
    {
        $dataObj = DateTime::createFromFormat('Y-m-d H:i', $data);
        return $dataObj && $dataObj->format('Y-m-d H:i') === $data;
    }
    
    /**
     * Busca total de evoluções
     */
    public function getTotalEvolucoes($pacienteId = null)
    {
        try {
            $where = '';
            $params = [];
            
            if ($pacienteId) {
                $where = "WHERE paciente_id = ?";
                $params = [$pacienteId];
            }
            
            $sql = "SELECT COUNT(*) FROM evolucao_paciente $where";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Busca pacientes para dropdown
     */
    public function listarPacientes()
    {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, cns FROM paciente ORDER BY nome");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Busca profissionais para dropdown
     */
    public function listarProfissionais()
    {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, especialidade FROM profissional ORDER BY nome");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
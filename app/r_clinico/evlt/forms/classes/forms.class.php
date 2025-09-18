<?php
include_once "../../../classes/db.class.php";

class Formulario
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
     * Cria um novo template de formulário
     */
    public function criarTemplate($dados)
    {
        try {
            $erros = $this->validarTemplate($dados);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dados
                ];
            }
            
            $dadosLimpos = $this->limparDadosTemplate($dados);
            
            $sql = "INSERT INTO formulario_template (
                nome, descricao, area_atendimento, ativo
            ) VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['nome'],
                $dadosLimpos['descricao'],
                $dadosLimpos['area_atendimento'],
                $dadosLimpos['ativo']
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Template criado com sucesso!',
                    'id' => $this->db->lastInsertId(),
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao criar template.'],
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
     * Adiciona campo a um formulário
     */
    public function adicionarCampo($formularioId, $dadosCampo)
    {
        try {
            $erros = $this->validarCampo($dadosCampo);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dadosCampo
                ];
            }
            
            $dadosLimpos = $this->limparDadosCampo($dadosCampo);
            
            $sql = "INSERT INTO formulario_campo (
                formulario_id, ordem, titulo, tipo_campo, opcoes, 
                obrigatorio, tamanho_maximo, placeholder
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $formularioId,
                $dadosLimpos['ordem'],
                $dadosLimpos['titulo'],
                $dadosLimpos['tipo_campo'],
                $dadosLimpos['opcoes'],
                $dadosLimpos['obrigatorio'],
                $dadosLimpos['tamanho_maximo'],
                $dadosLimpos['placeholder']
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Campo adicionado com sucesso!',
                    'id' => $this->db->lastInsertId(),
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao adicionar campo.'],
                    'dados' => $dadosCampo
                ];
            }
            
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erros' => ['Erro no sistema: ' . $e->getMessage()],
                'dados' => $dadosCampo
            ];
        }
    }
    
    /**
     * Lista todos os templates ativos
     */
    public function listarTemplates($ativo = true)
    {
        try {
            $where = $ativo ? "WHERE ativo = 1" : "";
            $sql = "SELECT * FROM formulario_template $where ORDER BY nome";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Busca template por ID com seus campos
     */
    public function buscarTemplateCompleto($id)
    {
        try {
            // Busca template
            $stmt = $this->db->prepare("SELECT * FROM formulario_template WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $template = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$template) {
                return false;
            }
            
            // Busca campos
            $stmt = $this->db->prepare("
                SELECT * FROM formulario_campo 
                WHERE formulario_id = ? 
                ORDER BY ordem
            ");
            $stmt->execute([$id]);
            $template['campos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $template;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Salva resposta de formulário
     */
    public function salvarResposta($dados)
    {
        try {
            $this->db->beginTransaction();
            
            // Insere resposta principal
            $sql = "INSERT INTO formulario_resposta (
                formulario_id, paciente_id, profissional_id
            ) VALUES (?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $dados['formulario_id'],
                $dados['paciente_id'],
                $dados['profissional_id']
            ]);
            
            $respostaId = $this->db->lastInsertId();
            
            // Insere respostas individuais dos campos
            if (isset($dados['respostas_campos']) && is_array($dados['respostas_campos'])) {
                $sql = "INSERT INTO formulario_resposta_campo (
                    resposta_id, campo_id, valor_texto, valor_numerico, valor_data, valor_opcoes
                ) VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->db->prepare($sql);
                
                foreach ($dados['respostas_campos'] as $respostaCampo) {
                    $stmt->execute([
                        $respostaId,
                        $respostaCampo['campo_id'],
                        $respostaCampo['valor_texto'] ?? null,
                        $respostaCampo['valor_numerico'] ?? null,
                        $respostaCampo['valor_data'] ?? null,
                        $respostaCampo['valor_opcoes'] ?? null
                    ]);
                }
            }
            
            $this->db->commit();
            
            return [
                'sucesso' => true,
                'mensagem' => 'Resposta salva com sucesso!',
                'id' => $respostaId
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return [
                'sucesso' => false,
                'erros' => ['Erro ao salvar resposta: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Valida template
     */
    private function validarTemplate($dados)
    {
        $erros = [];
        
        if (empty(trim($dados['nome']))) {
            $erros[] = "Nome do formulário é obrigatório";
        } elseif (strlen(trim($dados['nome'])) > 100) {
            $erros[] = "Nome deve ter no máximo 100 caracteres";
        }
        
        if (empty(trim($dados['area_atendimento']))) {
            $erros[] = "Área de atendimento é obrigatória";
        }
        
        return $erros;
    }
    
    /**
     * Valida campo
     */
    private function validarCampo($dados)
    {
        $erros = [];
        
        if (empty(trim($dados['titulo']))) {
            $erros[] = "Título do campo é obrigatório";
        } elseif (strlen(trim($dados['titulo'])) > 200) {
            $erros[] = "Título deve ter no máximo 200 caracteres";
        }
        
        $tiposValidos = ['texto', 'textarea', 'radio', 'checkbox', 'select', 'numero', 'data'];
        if (empty($dados['tipo_campo']) || !in_array($dados['tipo_campo'], $tiposValidos)) {
            $erros[] = "Tipo de campo inválido";
        }
        
        return $erros;
    }
    
    /**
     * Limpa dados do template
     */
    private function limparDadosTemplate($dados)
    {
        return [
            'nome' => trim(htmlspecialchars($dados['nome'])),
            'descricao' => !empty($dados['descricao']) ? trim(htmlspecialchars($dados['descricao'])) : null,
            'area_atendimento' => trim(htmlspecialchars($dados['area_atendimento'])),
            'ativo' => isset($dados['ativo']) ? 1 : 0
        ];
    }
    
    /**
     * Limpa dados do campo
     */
    private function limparDadosCampo($dados)
    {
        return [
            'ordem' => (int)($dados['ordem'] ?? 0),
            'titulo' => trim(htmlspecialchars($dados['titulo'])),
            'tipo_campo' => $dados['tipo_campo'],
            'opcoes' => !empty($dados['opcoes']) ? json_encode($dados['opcoes']) : null,
            'obrigatorio' => isset($dados['obrigatorio']) ? 1 : 0,
            'tamanho_maximo' => !empty($dados['tamanho_maximo']) ? (int)$dados['tamanho_maximo'] : null,
            'placeholder' => !empty($dados['placeholder']) ? trim(htmlspecialchars($dados['placeholder'])) : null
        ];
    }
}
?>
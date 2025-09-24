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
            // Valida os dados
            $erros = $this->validarTemplate($dados);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dados
                ];
            }
            
            // Verifica se já existe template com mesmo nome e área
            if ($this->existeTemplate($dados['nome'], $dados['area_atendimento'])) {
                return [
                    'sucesso' => false,
                    'erros' => ['Já existe um formulário com este nome nesta área de atendimento'],
                    'dados' => $dados
                ];
            }
            
            // Prepara os dados para inserção
            $dadosLimpos = $this->limparDadosTemplate($dados);
            
            // Inicia transação
            $this->db->beginTransaction();
            
            // Insere o template
            $sql = "INSERT INTO formulario_template (
                nome, descricao, area_atendimento, ativo
            ) VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $dadosLimpos['nome'],
                $dadosLimpos['descricao'],
                $dadosLimpos['area_atendimento'],
                $dadosLimpos['ativo']
            ]);
            
            $templateId = $this->db->lastInsertId();
            
            // Insere os campos se fornecidos
            if (isset($dados['campos']) && is_array($dados['campos'])) {
                foreach ($dados['campos'] as $campo) {
                    $this->adicionarCampo($templateId, $campo);
                }
            }
            
            // Confirma transação
            $this->db->commit();
            
            return [
                'sucesso' => true,
                'mensagem' => 'Formulário criado com sucesso!',
                'id' => $templateId,
                'dados' => []
            ];
            
        } catch (Exception $e) {
            // Reverte transação em caso de erro
            $this->db->rollback();
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
            // Valida os dados do campo
            $erros = $this->validarCampo($dadosCampo);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dadosCampo
                ];
            }
            
            // Prepara os dados para inserção
            $dadosLimpos = $this->limparDadosCampo($dadosCampo);
            
            // Insere o campo
            $sql = "INSERT INTO formulario_campo (
                formulario_id, ordem, nome_unico, titulo, descricao, tipo_input, 
                opcoes, obrigatorio, multipla_escolha, tamanho_maximo, placeholder
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $formularioId,
                $dadosLimpos['ordem'],
                $dadosLimpos['nome_unico'],
                $dadosLimpos['titulo'],
                $dadosLimpos['descricao'],
                $dadosLimpos['tipo_input'],
                $dadosLimpos['opcoes'],
                $dadosLimpos['obrigatorio'],
                $dadosLimpos['multipla_escolha'],
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
     * Atualiza template
     */
    public function atualizarTemplate($id, $dados)
    {
        try {
            // Valida os dados
            $erros = $this->validarTemplate($dados, $id);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dados
                ];
            }
            
            // Verifica se já existe template com mesmo nome e área (exceto o próprio)
            if ($this->existeTemplate($dados['nome'], $dados['area_atendimento'], $id)) {
                return [
                    'sucesso' => false,
                    'erros' => ['Já existe um formulário com este nome nesta área de atendimento'],
                    'dados' => $dados
                ];
            }
            
            // Prepara os dados para atualização
            $dadosLimpos = $this->limparDadosTemplate($dados);
            
            // Atualiza o template
            $sql = "UPDATE formulario_template SET 
                nome = ?, descricao = ?, area_atendimento = ?, ativo = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['nome'],
                $dadosLimpos['descricao'],
                $dadosLimpos['area_atendimento'],
                $dadosLimpos['ativo'],
                $id
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Formulário atualizado com sucesso!',
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao atualizar formulário.'],
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
     * Exclui template
     */
    public function excluirTemplate($id)
    {
        try {
            // Verifica se template tem respostas vinculadas
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM formulario_resposta WHERE formulario_id = ?");
            $stmt->execute([$id]);
            $temRespostas = $stmt->fetchColumn();
            
            if ($temRespostas > 0) {
                return [
                    'sucesso' => false,
                    'erros' => ['Não é possível excluir formulário com respostas registradas.']
                ];
            }
            
            // Exclui campos primeiro (devido à foreign key)
            $stmt = $this->db->prepare("DELETE FROM formulario_campo WHERE formulario_id = ?");
            $stmt->execute([$id]);
            
            // Exclui template
            $stmt = $this->db->prepare("DELETE FROM formulario_template WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Formulário excluído com sucesso!'
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao excluir formulário.']
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
     * Valida template
     */
    private function validarTemplate($dados, $idExcluir = null)
    {
        $erros = [];
        
        // Validação de campos obrigatórios
        if (empty(trim($dados['nome']))) {
            $erros[] = "Nome do formulário é obrigatório";
        } elseif (strlen(trim($dados['nome'])) > 100) {
            $erros[] = "Nome deve ter no máximo 100 caracteres";
        }
        
        if (empty($dados['area_atendimento'])) {
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
        
        if (empty($dados['tipo_input'])) {
            $erros[] = "Tipo de campo é obrigatório";
        }
        
        $tiposValidos = ['texto', 'textarea', 'radio', 'checkbox', 'select', 'numero', 'data', 'hora', 'email', 'telefone'];
        if (!in_array($dados['tipo_input'], $tiposValidos)) {
            $erros[] = "Tipo de campo inválido";
        }
        
        if (empty(trim($dados['nome_unico']))) {
            $erros[] = "Nome único do campo é obrigatório";
        } elseif (strlen(trim($dados['nome_unico'])) > 100) {
            $erros[] = "Nome único deve ter no máximo 100 caracteres";
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
        $opcoesJson = null;
        if (isset($dados['opcoes']) && is_array($dados['opcoes'])) {
            $opcoesJson = json_encode($dados['opcoes']);
        } elseif (isset($dados['opcoes']) && is_string($dados['opcoes'])) {
            $opcoesArray = explode("\n", $dados['opcoes']);
            $opcoesArray = array_map('trim', $opcoesArray);
            $opcoesArray = array_filter($opcoesArray, function($op) { return !empty($op); });
            $opcoesJson = json_encode(array_values($opcoesArray));
        }
        
        return [
            'formulario_id' => (int)($dados['formulario_id'] ?? 0),
            'ordem' => (int)($dados['ordem'] ?? 0),
            'nome_unico' => trim(htmlspecialchars($dados['nome_unico'])),
            'titulo' => trim(htmlspecialchars($dados['titulo'])),
            'descricao' => !empty($dados['descricao']) ? trim(htmlspecialchars($dados['descricao'])) : null,
            'tipo_input' => $dados['tipo_input'],
            'opcoes' => $opcoesJson,
            'obrigatorio' => isset($dados['obrigatorio']) ? 1 : 0,
            'multipla_escolha' => isset($dados['multipla_escolha']) ? 1 : 0,
            'tamanho_maximo' => !empty($dados['tamanho_maximo']) ? (int)$dados['tamanho_maximo'] : null,
            'placeholder' => !empty($dados['placeholder']) ? trim(htmlspecialchars($dados['placeholder'])) : null
        ];
    }
    
    /**
     * Verifica se template já existe
     */
    private function existeTemplate($nome, $areaAtendimento, $idExcluir = null)
    {
        $sql = "SELECT id FROM formulario_template WHERE nome = ? AND area_atendimento = ?";
        $params = [$nome, $areaAtendimento];
        
        if ($idExcluir) {
            $sql .= " AND id != ?";
            $params[] = $idExcluir;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Lista campos de um formulário
     */
    public function listarCampos($formularioId)
    {
        try {
            $sql = "SELECT * FROM formulario_campo WHERE formulario_id = ? ORDER BY ordem";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$formularioId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Busca campo por ID
     */
    public function buscarCampo($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM formulario_campo WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Atualiza campo
     */
    public function atualizarCampo($id, $dados)
    {
        try {
            // Valida os dados
            $erros = $this->validarCampo($dados);
            
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'erros' => $erros,
                    'dados' => $dados
                ];
            }
            
            // Prepara os dados para atualização
            $dadosLimpos = $this->limparDadosCampo($dados);
            
            // Atualiza o campo
            $sql = "UPDATE formulario_campo SET 
                formulario_id = ?, ordem = ?, nome_unico = ?, titulo = ?, descricao = ?, 
                tipo_input = ?, opcoes = ?, obrigatorio = ?, multipla_escolha = ?, 
                tamanho_maximo = ?, placeholder = ?
                WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['formulario_id'],
                $dadosLimpos['ordem'],
                $dadosLimpos['nome_unico'],
                $dadosLimpos['titulo'],
                $dadosLimpos['descricao'],
                $dadosLimpos['tipo_input'],
                $dadosLimpos['opcoes'],
                $dadosLimpos['obrigatorio'],
                $dadosLimpos['multipla_escolha'],
                $dadosLimpos['tamanho_maximo'],
                $dadosLimpos['placeholder'],
                $id
            ]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Campo atualizado com sucesso!',
                    'dados' => []
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao atualizar campo.'],
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
     * Exclui campo
     */
    public function excluirCampo($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM formulario_campo WHERE id = ?");
            $resultado = $stmt->execute([$id]);
            
            if ($resultado) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Campo excluído com sucesso!'
                ];
            } else {
                return [
                    'sucesso' => false,
                    'erros' => ['Erro ao excluir campo.']
                ];
            }
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'erros' => ['Erro no sistema: ' . $e->getMessage()]
            ];
        }
    }
}
?>
<?php

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
     * Lista todos os formulários cadastrados
     */
/**
 * Lista formulários com filtro de status
 */
    public function listarFormularios($ativos = true)
    {
        $status = $ativos ? 1 : 0;
        $stmt = $this->db->prepare("SELECT id, nome, especialidade, descricao, ativo FROM formulario WHERE ativo = ? ORDER BY nome ASC");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Alterna o status de um formulário (ativo/inativo)
     */
    public function alternarStatusFormulario($form_id)
    {
        try {
            // Verifica se existe
            $stmt = $this->db->prepare("SELECT ativo FROM formulario WHERE id = ?");
            $stmt->execute([$form_id]);
            $atual = $stmt->fetchColumn();
            if ($atual === false) {
                return ['sucesso' => false, 'erros' => ['Formulário não encontrado.']];
            }

            $novoStatus = $atual == 1 ? 0 : 1;
            $stmt = $this->db->prepare("UPDATE formulario SET ativo = ? WHERE id = ?");
            $stmt->execute([$novoStatus, $form_id]);

            $msg = $novoStatus == 1 ? 'Formulário reativado com sucesso!' : 'Formulário desativado com sucesso!';
            return ['sucesso' => true, 'mensagem' => $msg];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro ao alterar status: ' . $e->getMessage()]];
        }
    }

    /**
     * Cria um novo formulário
     */
    public function criarFormulario($dados)
    {
        $nome = trim($dados['nome'] ?? '');
        $descricao = trim($dados['descricao'] ?? '');
        $especialidade = trim($dados['especialidade'] ?? '');
        $s_n_anexo = isset($dados['s_n_anexo']) && $dados['s_n_anexo'] === 'S' ? 'S' : 'N';
        $ativo = isset($dados['ativo']) && $dados['ativo'] === '1' ? 1 : 0;

        if (empty($nome) || empty($especialidade)) {
            return ['sucesso' => false, 'erros' => ['Nome e especialidade são obrigatórios.']];
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO formulario (nome, descricao, especialidade, s_n_anexo, ativo, data_criacao)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$nome, $descricao, $especialidade, $s_n_anexo, $ativo]);

            return [
                'sucesso' => true,
                'mensagem' => 'Formulário criado com sucesso!',
                'id' => $this->db->lastInsertId()
            ];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro ao criar formulário: ' . $e->getMessage()]];
        }
    }
}
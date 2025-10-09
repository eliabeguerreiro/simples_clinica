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
     * Lista todos os formulários cadastrados
     */
    public function listarFormularios()
    {
        $stmt = $this->db->prepare("SELECT id, nome, especialidade, descricao, ativo FROM formulario ORDER BY nome ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
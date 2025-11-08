<?php


class GestUser
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
     * Lista todos os usuários
     */
    public function listar()
    {
        $stmt = $this->db->prepare("SELECT id, cpf, login, nm_usuario, tipo FROM usuarios");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca usuário por ID
     */
    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare("SELECT id, cpf, login, nm_usuario, tipo FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cadastra novo usuário
     */
    public function cadastrar($dados)
    {
        // Validação básica
        if (empty(trim($dados['cpf'])) || strlen(trim($dados['cpf'])) != 11) {
            return [
                'sucesso' => false,
                'erros' => ['CPF inválido ou vazio.']
            ];
        }

        if (empty(trim($dados['login']))) {
            return [
                'sucesso' => false,
                'erros' => ['Login é obrigatório.']
            ];
        }

        if (empty(trim($dados['nm_usuario']))) {
            return [
                'sucesso' => false,
                'erros' => ['Nome do usuário é obrigatório.']
            ];
        }

        // Verifica se CPF já existe
        if ($this->existeCpf(trim($dados['cpf']))) {
            return [
                'sucesso' => false,
                'erros' => ['Já existe um usuário com este CPF.']
            ];
        }

        // Verifica se login já existe
        if ($this->existeLogin(trim($dados['login']))) {
            return [
                'sucesso' => false,
                'erros' => ['Já existe um usuário com este login.']
            ];
        }

        // Gera hash da senha
        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (cpf, login, senha, nm_usuario, tipo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([
            trim($dados['cpf']),
            trim($dados['login']),
            $senhaHash,
            trim($dados['nm_usuario']),
            $dados['tipo'] ?? 'user'
        ]);

        if ($resultado) {
            return [
                'sucesso' => true,
                'mensagem' => 'Usuário cadastrado com sucesso!',
                'id' => $this->db->lastInsertId(),
                'dados' => []
            ];
        } else {
            return [
                'sucesso' => false,
                'erros' => ['Erro ao cadastrar usuário. Tente novamente.'],
                'dados' => $dados
            ];
        }
    }

    /**
     * Atualiza usuário
     */
    public function atualizar($id, $dados)
    {
        // Validação
        if (empty(trim($dados['cpf'])) || strlen(trim($dados['cpf'])) != 11) {
            return [
                'sucesso' => false,
                'erros' => ['CPF inválido ou vazio.']
            ];
        }

        if (empty(trim($dados['login']))) {
            return [
                'sucesso' => false,
                'erros' => ['Login é obrigatório.']
            ];
        }

        if (empty(trim($dados['nm_usuario']))) {
            return [
                'sucesso' => false,
                'erros' => ['Nome do usuário é obrigatório.']
            ];
        }

        // Verifica se CPF já existe (exceto para o próprio usuário)
        if ($this->existeCpf(trim($dados['cpf']), $id)) {
            return [
                'sucesso' => false,
                'erros' => ['Já existe outro usuário com este CPF.']
            ];
        }

        // Verifica se login já existe (exceto para o próprio usuário)
        if ($this->existeLogin(trim($dados['login']), $id)) {
            return [
                'sucesso' => false,
                'erros' => ['Já existe outro usuário com este login.']
            ];
        }

        // Se senha for enviada, atualiza
        $campos = "cpf = ?, login = ?, nm_usuario = ?, tipo = ?";
        $valores = [
            trim($dados['cpf']),
            trim($dados['login']),
            trim($dados['nm_usuario']),
            $dados['tipo'] ?? 'user'
        ];

        if (!empty($dados['senha'])) {
            $campos .= ", senha = ?";
            $valores[] = password_hash($dados['senha'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE usuarios SET $campos WHERE id = ?";
        $valores[] = $id;

        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute($valores);

        if ($resultado) {
            return [
                'sucesso' => true,
                'mensagem' => 'Usuário atualizado com sucesso!',
                'dados' => []
            ];
        } else {
            return [
                'sucesso' => false,
                'erros' => ['Erro ao atualizar usuário.'],
                'dados' => $dados
            ];
        }
    }

    /**
     * Exclui usuário
     */
    public function excluir($id)
    {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        $resultado = $stmt->execute([$id]);

        if ($resultado) {
            return [
                'sucesso' => true,
                'mensagem' => 'Usuário excluído com sucesso!'
            ];
        } else {
            return [
                'sucesso' => false,
                'erros' => ['Erro ao excluir usuário.']
            ];
        }
    }

    /**
     * Verifica se CPF já existe
     */
    private function existeCpf($cpf, $idExcluir = null)
    {
        $sql = "SELECT id FROM usuarios WHERE cpf = ?";
        $params = [$cpf];

        if ($idExcluir) {
            $sql .= " AND id != ?";
            $params[] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Verifica se login já existe
     */
    private function existeLogin($login, $idExcluir = null)
    {
        $sql = "SELECT id FROM usuarios WHERE login = ?";
        $params = [$login];

        if ($idExcluir) {
            $sql .= " AND id != ?";
            $params[] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }
}
?>
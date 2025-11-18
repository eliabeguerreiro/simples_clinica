<?php
include_once "../../classes/db.class.php";

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
     * Lista todos os usuários ATIVOS com nome do perfil
     */
    public function listar()
    {
        $sql = "
            SELECT u.id, u.cpf, u.login, u.nm_usuario, u.ativo,
                   p.nome AS perfil_nome
            FROM usuarios u
            LEFT JOIN perfis p ON u.perfil_id = p.id
            WHERE u.ativo = 1
            ORDER BY u.nm_usuario
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca usuário por ID (inclui perfil_id e ativo)
     */
    public function buscarPorId($id)
    {
        $sql = "
            SELECT u.id, u.cpf, u.login, u.nm_usuario, u.perfil_id, u.ativo
            FROM usuarios u
            WHERE u.id = ? LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lista todos os perfis para o select
     */
    public function listarPerfis()
    {
        $stmt = $this->db->prepare("SELECT id, nome, descricao FROM perfis ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cadastra novo usuário com perfil_id
     */
    public function cadastrar($dados)
    {
        // Validações básicas
        if (empty(trim($dados['cpf'])) || strlen(trim($dados['cpf'])) !== 11) {
            return ['sucesso' => false, 'erros' => ['CPF inválido ou vazio.'], 'dados' => $dados];
        }
        if (empty(trim($dados['login']))) {
            return ['sucesso' => false, 'erros' => ['Login é obrigatório.'], 'dados' => $dados];
        }
        if (empty(trim($dados['nm_usuario']))) {
            return ['sucesso' => false, 'erros' => ['Nome do usuário é obrigatório.'], 'dados' => $dados];
        }
        if (empty($dados['perfil_id'])) {
            return ['sucesso' => false, 'erros' => ['Tipo de usuário é obrigatório.'], 'dados' => $dados];
        }
        if (empty($dados['senha'])) {
            return ['sucesso' => false, 'erros' => ['Senha é obrigatória.'], 'dados' => $dados];
        }

        // Verifica duplicidades
        if ($this->existeCpf(trim($dados['cpf']))) {
            return ['sucesso' => false, 'erros' => ['Já existe um usuário com este CPF.'], 'dados' => $dados];
        }
        if ($this->existeLogin(trim($dados['login']))) {
            return ['sucesso' => false, 'erros' => ['Já existe um usuário com este login.'], 'dados' => $dados];
        }

        // Hash da senha
        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

        // Insere com perfil_id e ativo = 1
        $sql = "INSERT INTO usuarios (cpf, login, senha, nm_usuario, perfil_id, ativo) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([
            trim($dados['cpf']),
            trim($dados['login']),
            $senhaHash,
            trim($dados['nm_usuario']),
            (int)$dados['perfil_id']
        ]);

        if ($resultado) {
            return [
                'sucesso' => true,
                'mensagem' => 'Usuário cadastrado com sucesso!',
                'id' => $this->db->lastInsertId()
            ];
        } else {
            return ['sucesso' => false, 'erros' => ['Erro ao cadastrar usuário. Tente novamente.'], 'dados' => $dados];
        }
    }

    /**
     * Atualiza usuário (com perfil_id e senha opcional)
     */
    public function atualizar($id, $dados)
    {
        if (empty(trim($dados['cpf'])) || strlen(trim($dados['cpf'])) !== 11) {
            return ['sucesso' => false, 'erros' => ['CPF inválido ou vazio.'], 'dados' => $dados];
        }
        if (empty(trim($dados['login']))) {
            return ['sucesso' => false, 'erros' => ['Login é obrigatório.'], 'dados' => $dados];
        }
        if (empty(trim($dados['nm_usuario']))) {
            return ['sucesso' => false, 'erros' => ['Nome do usuário é obrigatório.'], 'dados' => $dados];
        }
        if (empty($dados['perfil_id'])) {
            return ['sucesso' => false, 'erros' => ['Tipo de usuário é obrigatório.'], 'dados' => $dados];
        }

        if ($this->existeCpf(trim($dados['cpf']), $id)) {
            return ['sucesso' => false, 'erros' => ['Já existe outro usuário com este CPF.'], 'dados' => $dados];
        }
        if ($this->existeLogin(trim($dados['login']), $id)) {
            return ['sucesso' => false, 'erros' => ['Já existe outro usuário com este login.'], 'dados' => $dados];
        }

        // Monta query dinamicamente (senha opcional)
        $campos = "cpf = ?, login = ?, nm_usuario = ?, perfil_id = ?";
        $valores = [
            trim($dados['cpf']),
            trim($dados['login']),
            trim($dados['nm_usuario']),
            (int)$dados['perfil_id']
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
            return ['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso!'];
        } else {
            return ['sucesso' => false, 'erros' => ['Erro ao atualizar usuário.'], 'dados' => $dados];
        }
    }

    /**
     * Desativa (não exclui) um usuário
     */
    public function desativar($id)
    {
        $stmt = $this->db->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        if ($resultado) {
            return ['sucesso' => true, 'mensagem' => 'Usuário desativado com sucesso!'];
        } else {
            return ['sucesso' => false, 'erros' => ['Erro ao desativar usuário.']];
        }
    }

    /**
     * Verifica se CPF já existe (ignora próprio ID)
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
     * Verifica se login já existe (ignora próprio ID)
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
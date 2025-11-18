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

    // =========== USUÁRIOS ===========
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

    public function buscarPorId($id)
    {
        $sql = "SELECT id, cpf, login, nm_usuario, perfil_id, ativo FROM usuarios WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrar($dados)
    {
        if (empty(trim($dados['cpf'])) || strlen(trim($dados['cpf'])) !== 11) {
            return ['sucesso' => false, 'erros' => ['CPF inválido ou vazio.'], 'dados' => $dados];
        }
        if (empty(trim($dados['login']))) return ['sucesso' => false, 'erros' => ['Login é obrigatório.'], 'dados' => $dados];
        if (empty(trim($dados['nm_usuario']))) return ['sucesso' => false, 'erros' => ['Nome do usuário é obrigatório.'], 'dados' => $dados];
        if (empty($dados['perfil_id'])) return ['sucesso' => false, 'erros' => ['Tipo de usuário é obrigatório.'], 'dados' => $dados];
        if (empty($dados['senha'])) return ['sucesso' => false, 'erros' => ['Senha é obrigatória.'], 'dados' => $dados];

        if ($this->existeCpf(trim($dados['cpf']))) return ['sucesso' => false, 'erros' => ['Já existe um usuário com este CPF.'], 'dados' => $dados];
        if ($this->existeLogin(trim($dados['login']))) return ['sucesso' => false, 'erros' => ['Já existe um usuário com este login.'], 'dados' => $dados];

        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (cpf, login, senha, nm_usuario, perfil_id, ativo) VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([
            trim($dados['cpf']),
            trim($dados['login']),
            $senhaHash,
            trim($dados['nm_usuario']),
            (int)$dados['perfil_id']
        ]);

        return $resultado
            ? ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso!', 'id' => $this->db->lastInsertId()]
            : ['sucesso' => false, 'erros' => ['Erro ao cadastrar usuário.'], 'dados' => $dados];
    }

    public function atualizar($id, $dados)
    {
        if (empty(trim($dados['cpf'])) || strlen(trim($dados['cpf'])) !== 11) {
            return ['sucesso' => false, 'erros' => ['CPF inválido ou vazio.'], 'dados' => $dados];
        }
        if (empty(trim($dados['login']))) return ['sucesso' => false, 'erros' => ['Login é obrigatório.'], 'dados' => $dados];
        if (empty(trim($dados['nm_usuario']))) return ['sucesso' => false, 'erros' => ['Nome do usuário é obrigatório.'], 'dados' => $dados];
        if (empty($dados['perfil_id'])) return ['sucesso' => false, 'erros' => ['Tipo de usuário é obrigatório.'], 'dados' => $dados];

        if ($this->existeCpf(trim($dados['cpf']), $id)) return ['sucesso' => false, 'erros' => ['Já existe outro usuário com este CPF.'], 'dados' => $dados];
        if ($this->existeLogin(trim($dados['login']), $id)) return ['sucesso' => false, 'erros' => ['Já existe outro usuário com este login.'], 'dados' => $dados];

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

        return $resultado
            ? ['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso!']
            : ['sucesso' => false, 'erros' => ['Erro ao atualizar usuário.'], 'dados' => $dados];
    }

    public function desativar($id)
    {
        $stmt = $this->db->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        return $resultado
            ? ['sucesso' => true, 'mensagem' => 'Usuário desativado com sucesso!']
            : ['sucesso' => false, 'erros' => ['Erro ao desativar usuário.']];
    }

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

    // =========== PERFIS ===========
    public function listarPerfis()
    {
        $stmt = $this->db->prepare("SELECT id, nome, especialidade, descricao FROM perfis ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPerfilPorId($id)
    {
        $stmt = $this->db->prepare("SELECT id, nome, especialidade, descricao FROM perfis WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarPerfil($dados)
    {
        $nome = trim($dados['nome'] ?? '');
        $especialidade = !empty($dados['especialidade']) ? trim($dados['especialidade']) : null;
        $descricao = trim($dados['descricao'] ?? '');

        if (empty($nome)) return ['sucesso' => false, 'erros' => ['Nome do perfil é obrigatório.'], 'dados' => $dados];
        if ($this->existeNomePerfil($nome)) return ['sucesso' => false, 'erros' => ['Já existe um perfil com este nome.'], 'dados' => $dados];

        $sql = "INSERT INTO perfis (nome, especialidade, descricao) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([$nome, $especialidade, $descricao]);

        if ($resultado) {
            $perfilId = $this->db->lastInsertId();
            if (!empty($dados['permissoes'])) {
                $this->atualizarPermissoes($perfilId, $dados['permissoes']);
            }
            return ['sucesso' => true, 'mensagem' => 'Perfil criado com sucesso!', 'id' => $perfilId];
        } else {
            return ['sucesso' => false, 'erros' => ['Erro ao salvar o perfil.'], 'dados' => $dados];
        }
    }

    public function atualizarPerfil($id, $dados)
    {
        $nome = trim($dados['nome'] ?? '');
        $especialidade = !empty($dados['especialidade']) ? trim($dados['especialidade']) : null;
        $descricao = trim($dados['descricao'] ?? '');

        if (empty($nome)) return ['sucesso' => false, 'erros' => ['Nome do perfil é obrigatório.'], 'dados' => $dados];
        if ($this->existeNomePerfil($nome, $id)) return ['sucesso' => false, 'erros' => ['Já existe outro perfil com este nome.'], 'dados' => $dados];

        $sql = "UPDATE perfis SET nome = ?, especialidade = ?, descricao = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute([$nome, $especialidade, $descricao, $id]);

        if ($resultado) {
            if (isset($dados['permissoes'])) {
                $this->atualizarPermissoes($id, $dados['permissoes']);
            }
            return ['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso!'];
        } else {
            return ['sucesso' => false, 'erros' => ['Erro ao atualizar o perfil.'], 'dados' => $dados];
        }
    }

    public function excluirPerfil($id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE perfil_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['total'] > 0) {
            return ['sucesso' => false, 'erros' => ['Não é possível excluir: existem usuários vinculados a este perfil.']];
        }

        $stmt = $this->db->prepare("DELETE FROM perfis WHERE id = ?");
        $resultado = $stmt->execute([$id]);
        return $resultado
            ? ['sucesso' => true, 'mensagem' => 'Perfil excluído com sucesso!']
            : ['sucesso' => false, 'erros' => ['Erro ao excluir o perfil.']];
    }

    private function existeNomePerfil($nome, $idExcluir = null)
    {
        $sql = "SELECT id FROM perfis WHERE nome = ?";
        $params = [$nome];
        if ($idExcluir) {
            $sql .= " AND id != ?";
            $params[] = $idExcluir;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    // =========== PERMISSÕES ===========
    public function listarPermissoes()
    {
        $stmt = $this->db->prepare("SELECT id, chave, descricao FROM permissoes ORDER BY chave");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissoesDoPerfil($perfilId)
    {
        $sql = "SELECT permissao_id FROM perfil_permissao WHERE perfil_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perfilId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'permissao_id');
    }

    public function atualizarPermissoes($perfilId, $permissoesIds = [])
    {
        $this->db->prepare("DELETE FROM perfil_permissao WHERE perfil_id = ?")->execute([$perfilId]);
        if (!empty($permissoesIds)) {
            $sql = "INSERT INTO perfil_permissao (perfil_id, permissao_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            foreach ($permissoesIds as $permissaoId) {
                $stmt->execute([$perfilId, (int)$permissaoId]);
            }
        }
        return true;
    }
}
?>
<?php
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

    public function cadastrar($dados)
    {
        try {
            $erros = $this->validarDados($dados);
            if (!empty($erros)) {
                return ['sucesso' => false, 'erros' => $erros, 'dados' => $dados];
            }

            if (!empty(trim($dados['cns'])) && $this->existeCNS(trim($dados['cns']))) {
                return [
                    'sucesso' => false,
                    'erros' => ['Já existe um paciente cadastrado com este CNS'],
                    'dados' => $dados
                ];
            }

            $dadosLimpos = $this->limparDados($dados);

            $sql = "INSERT INTO paciente (
                origem, cns, nome, data_nascimento, sexo, raca_cor, etnia, nacionalidade,
                codigo_logradouro, endereco, numero, complemento, bairro, cep,
                telefone, email, situacao_rua
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['origem'],
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
                    'id' => $this->db->lastInsertId()
                ];
            } else {
                return ['sucesso' => false, 'erros' => ['Erro ao cadastrar paciente.'], 'dados' => $dados];
            }
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro no sistema: ' . $e->getMessage()], 'dados' => $dados];
        }
    }

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
            return (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }

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

    public function buscarPorTermo($termo = '')
    {
        try {
            if (empty(trim($termo))) {
                $stmt = $this->db->prepare("SELECT * FROM paciente ORDER BY nome");
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $termo = '%' . trim($termo) . '%';
            $stmt = $this->db->prepare("SELECT * FROM paciente WHERE nome LIKE ? OR cns LIKE ? ORDER BY nome");
            $stmt->execute([$termo, $termo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function buscarPorTermoPaginado($termo = '', $pagina = 1, $porPagina = 10)
    {
        $offset = ($pagina - 1) * $porPagina;
        try {
            if (empty(trim($termo))) {
                $stmt = $this->db->prepare("SELECT * FROM paciente ORDER BY nome LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            } else {
                $termoLike = '%' . trim($termo) . '%';
                $stmt = $this->db->prepare("SELECT * FROM paciente WHERE nome LIKE :nome OR cns LIKE :cns ORDER BY nome LIMIT :limit OFFSET :offset");
                $stmt->bindValue(':nome', $termoLike, PDO::PARAM_STR);
                $stmt->bindValue(':cns', $termoLike, PDO::PARAM_STR);
                $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function atualizar($id, $dados)
    {
        try {
            $erros = $this->validarDados($dados, $id);
            if (!empty($erros)) {
                return ['sucesso' => false, 'erros' => $erros, 'dados' => $dados];
            }

            if (!empty(trim($dados['cns'])) && $this->existeCNS(trim($dados['cns']), $id)) {
                return [
                    'sucesso' => false,
                    'erros' => ['Já existe um paciente cadastrado com este CNS'],
                    'dados' => $dados
                ];
            }

            $dadosLimpos = $this->limparDados($dados);

            $sql = "UPDATE paciente SET 
                origem = ?, cns = ?, nome = ?, data_nascimento = ?, sexo = ?, raca_cor = ?, 
                etnia = ?, nacionalidade = ?, codigo_logradouro = ?, endereco = ?, 
                numero = ?, complemento = ?, bairro = ?, cep = ?, telefone = ?, 
                email = ?, situacao_rua = ?
                WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                $dadosLimpos['origem'],
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
                return ['sucesso' => true, 'mensagem' => 'Paciente atualizado com sucesso!'];
            } else {
                return ['sucesso' => false, 'erros' => ['Erro ao atualizar paciente.'], 'dados' => $dados];
            }
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro no sistema: ' . $e->getMessage()], 'dados' => $dados];
        }
    }

    public function excluir($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM atendimento WHERE paciente_id = ?");
            $stmt->execute([$id]);
            $temAtendimentos = $stmt->fetchColumn();
            if ($temAtendimentos > 0) {
                return ['sucesso' => false, 'erros' => ['Não é possível excluir paciente com atendimentos registrados.']];
            }

            $stmt = $this->db->prepare("DELETE FROM paciente WHERE id = ?");
            $resultado = $stmt->execute([$id]);

            return $resultado
                ? ['sucesso' => true, 'mensagem' => 'Paciente excluído com sucesso!']
                : ['sucesso' => false, 'erros' => ['Erro ao excluir paciente.']];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erros' => ['Erro no sistema: ' . $e->getMessage()]];
        }
    }

    private function validarDados($dados, $idExcluir = null)
    {
        $erros = [];

        if (empty(trim($dados['nome'] ?? ''))) {
            $erros[] = "Nome completo é obrigatório";
        }
        if (empty($dados['origem'] ?? '') || !in_array($dados['origem'], ['SUS', 'PARTICULAR', 'GEAP'])) {
            $erros[] = "Origem é obrigatória";
        }
        if (empty($dados['data_nascimento'] ?? '')) {
            $erros[] = "Data de nascimento é obrigatória";
        } elseif (!$this->validarData($dados['data_nascimento'])) {
            $erros[] = "Data de nascimento inválida";
        }
        if (empty($dados['sexo'] ?? '') || !in_array($dados['sexo'], ['M', 'F'])) {
            $erros[] = "Sexo é obrigatório";
        }
        if (empty($dados['raca_cor'] ?? '') || !in_array($dados['raca_cor'], ['01', '02', '03', '04', '05', '99'])) {
            $erros[] = "Raça/Cor é obrigatória";
        }
        if (empty($dados['nacionalidade'] ?? '') || !in_array($dados['nacionalidade'], ['10', '20', '30'])) {
            $erros[] = "Nacionalidade é obrigatória";
        }
        if (empty($dados['codigo_logradouro'] ?? '') || !in_array($dados['codigo_logradouro'], ['81', '8'])) {
            $erros[] = "Tipo de logradouro é obrigatório";
        }
        if (empty(trim($dados['endereco'] ?? ''))) {
            $erros[] = "Logradouro é obrigatório";
        }
        if (empty(trim($dados['numero'] ?? ''))) {
            $erros[] = "Número é obrigatório";
        }
        if (empty(trim($dados['bairro'] ?? ''))) {
            $erros[] = "Bairro é obrigatório";
        }
        if (empty(trim($dados['cep'] ?? ''))) {
            $erros[] = "CEP é obrigatório";
        } elseif (!$this->validarCEP($dados['cep'])) {
            $erros[] = "CEP inválido";
        }
        if (empty(trim($dados['telefone'] ?? ''))) {
            $erros[] = "Telefone is obrigatório";
        } elseif (!$this->validarTelefone($dados['telefone'])) {
            $erros[] = "Telefone inválido";
        }
        if (!empty($dados['email'] ?? '') && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email inválido";
        }
        if (empty($dados['situacao_rua'] ?? '') || !in_array($dados['situacao_rua'], ['S', 'N'])) {
            $erros[] = "Situação de rua é obrigatória";
        }

        // CNS: obrigatório apenas se origem for SUS
        $cns = trim($dados['cns'] ?? '');
        if ($dados['origem'] === 'SUS') {
            if (empty($cns)) {
                $erros[] = "CNS é obrigatório para pacientes do SUS";
            } elseif (!$this->validarCNS($cns)) {
                $erros[] = "CNS inválido";
            }
        } else {
            if (!empty($cns) && !$this->validarCNS($cns)) {
                $erros[] = "CNS inválido";
            }
        }

        return $erros;
    }

    private function limparDados($dados)
    {
        return [
            'origem' => $dados['origem'],
            'cns' => !empty($dados['cns']) ? preg_replace('/[^0-9]/', '', trim($dados['cns'])) : null,
            'nome' => trim(htmlspecialchars($dados['nome'] ?? '')),
            'data_nascimento' => $dados['data_nascimento'] ?? '',
            'sexo' => $dados['sexo'] ?? '',
            'raca_cor' => $dados['raca_cor'] ?? '',
            'etnia' => !empty($dados['etnia']) ? trim(htmlspecialchars($dados['etnia'])) : null,
            'nacionalidade' => $dados['nacionalidade'] ?? '',
            'codigo_logradouro' => $dados['codigo_logradouro'] ?? '',
            'endereco' => trim(htmlspecialchars($dados['endereco'] ?? '')),
            'numero' => trim(htmlspecialchars($dados['numero'] ?? '')),
            'complemento' => !empty($dados['complemento']) ? trim(htmlspecialchars($dados['complemento'])) : null,
            'bairro' => trim(htmlspecialchars($dados['bairro'] ?? '')),
            'cep' => preg_replace('/[^0-9]/', '', trim($dados['cep'] ?? '')),
            'telefone' => preg_replace('/[^0-9]/', '', trim($dados['telefone'] ?? '')),
            'email' => !empty($dados['email']) ? trim(htmlspecialchars($dados['email'])) : null,
            'situacao_rua' => $dados['situacao_rua'] ?? ''
        ];
    }

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

    private function validarCNS($cns)
    {
        $cns = preg_replace('/[^0-9]/', '', $cns);
        return strlen($cns) === 15 && is_numeric($cns);
    }

    private function validarData($data)
    {
        $date = DateTime::createFromFormat('Y-m-d', $data);
        return $date && $date->format('Y-m-d') === $data;
    }

    private function validarCEP($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        return strlen($cep) === 8;
    }

    private function validarTelefone($telefone)
    {
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        return strlen($telefone) >= 10 && strlen($telefone) <= 11;
    }

    public function listarEvolucoesDetalhadas($pacienteId)
    {
        try {
            $sql = "
                SELECT 
                    ec.id,
                    ec.formulario_id,
                    ec.paciente_id,
                    ec.atendimento_id,
                    ec.data_referencia,
                    ec.data_hora AS created_at,
                    ec.dados,
                    ec.observacoes,
                    ec.criado_por,
                    f.nome AS nome_formulario,
                    f.especialidade
                FROM evolucao_clinica ec
                LEFT JOIN formulario f ON ec.formulario_id = f.id
                WHERE ec.paciente_id = ?
                ORDER BY ec.data_hora DESC
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$pacienteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

        public function getPermissoesChavesDoPerfil($perfilId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.chave
                FROM perfil_permissao pp
                JOIN permissoes p ON pp.permissao_id = p.id
                WHERE pp.perfil_id = ?
            ");
            $stmt->execute([$perfilId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Erro ao carregar permissões por chave: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Carrega as permissões do perfil na sessão (útil para inicialização)
     * @param int $perfilId
     * @return bool
     */
    public function carregarPermissoesDoPerfilNaSessao($perfilId)
    {
        $chaves = $this->getPermissoesChavesDoPerfil($perfilId);
        if ($chaves === false) {
            return false;
        }
        $_SESSION['data_user']['permissoes'] = $chaves;
        return true;
    }

}
?>
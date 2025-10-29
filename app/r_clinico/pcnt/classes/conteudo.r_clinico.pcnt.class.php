
<?php
include_once "../../../classes/db.class.php";
include_once "Paciente.class.php";

class ConteudoRClinicoPCNT
{
    private $paciente;
    private $paciente_id;

    public function __construct($paciente_id = null)
    {
        $this->paciente = new Paciente();
        $this->paciente_id = $paciente_id;
    }

    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico - Pacientes</title>
                <link rel="stylesheet" href="./src/style.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
            </head>
        HTML;
        $body = $this->renderBody();
        $html .= $body;
        $html .= <<<HTML
            <script src="./src/script.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        </body>
        </html>
        HTML;
        return $html;
    }

    private function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);
        $resultado = null;
        if ($_POST && isset($_POST['acao'])) {
            switch ($_POST['acao']) {
                case 'cadastrar':
                    $resultado = $this->paciente->cadastrar($_POST);
                    break;
                case 'excluir':
                    $resultado = $this->paciente->excluir($_POST['id']);
                    break;
                case 'excluir_multiplos':
                    $resultado = $this->excluirMultiplos($_POST['ids']);
                    break;
                case 'atualizar':
                    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                        $resultado = $this->paciente->atualizar($_POST['id'], $_POST);
                    } else {
                        $resultado = [
                            'sucesso' => false,
                            'erros' => ['ID do paciente inválido.'],
                            'dados' => $_POST
                        ];
                    }
                    break;
            }
        }

        // Verifica se há um paciente sendo visualizado
        $pacienteBuscado = null;
        $buscaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($buscaId > 0) {
            $pacienteBuscado = $this->paciente->buscarPorId($buscaId);
        }

        $html = <<<HTML
            <body>
                <header>
                    <div class="logo">
                        <img src="#" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="./">INICIO</a></li>
                            <li><a href="/atendimentos.php">SUPORTE</a></li>
                            <li><a href="/paciente">SAIR</a></li>
                        </ul>
                    </nav>
                </header>
                <section class="simple-box">
                    <h2>Registro Clínico</h2>
                    <!-- Abas principais -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn active" onclick="redirectToTab('pacientes')">Pacientes</button>
                        <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
                        <button class="tab-btn" onclick="redirectToTab('evolucoes')">Evoluções</button>
                    </div>
                    <!-- Sub-abas -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-pacientes">
                            <button class="tab-btn" data-main="pacientes" data-sub="cadastro" onclick="showSubTab('pacientes', 'cadastro', this)">Cadastro</button>
                            <button class="tab-btn active" data-main="pacientes" data-sub="documentos" onclick="showSubTab('pacientes', 'documentos', this)">Listagem</button>
            HTML;

            // Só mostra a aba "Histórico" se houver paciente_id
            if ($this->paciente_id) {
                $html .= '<button class="tab-btn" data-main="pacientes" data-sub="historico" onclick="showSubTab(\'pacientes\', \'historico\', this)">Histórico</button>';
            }

            $html .= <<<HTML
                            </div>
                        </div>
                        <!-- Conteúdo das abas -->
                        <div id="tab-content">
                            <div id="pacientes-cadastro" class="tab-content" style="display:none;">
                                {$this->getFormularioCadastro($resultado)}
                            </div>
                            <div id="pacientes-documentos" class="tab-content active">
                                {$this->getListagemPacientes($resultado, $pacienteBuscado)}
                            </div>
                            <div id="pacientes-historico" class="tab-content" style="display:none;">
            HTML;

            if ($this->paciente_id) {
                $html .= $this->getHistoricoEvolucoesPorPaciente($this->paciente_id);
            } else {
                $html .= '<div class="form-message error">Paciente não especificado para exibir histórico.</div>';
            }

        $html .= <<<HTML
                        </div>
                        <div id="pacientes-edicao" class="tab-content" style="display:none;">
                            {$this->getFormularioEdicao($resultado)}
                        </div>
                    </div>
                </section>
                <!-- Modal de exclusão -->
                <div id="modal-exclusao" class="modal" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Confirmar Exclusão</h3>
                            <span class="close-modal" onclick="fecharModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <p>Tem certeza que deseja excluir este paciente?</p>
                            <p><strong>Esta ação não pode ser desfeita.</strong></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                            <button class="btn-delete" id="confirmar-exclusao">Excluir</button>
                        </div>
                    </div>
                </div>
                <script src="./src/script.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
            </body>
        HTML;
        return $html;
    }

    private function getFormularioCadastro($resultado = null)
    {
        $dadosForm = [];
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        } elseif (isset($_POST) && (!isset($_POST['acao']) || $_POST['acao'] == 'cadastrar')) {
            $dadosForm = $_POST;
        }
        $mensagens = '';
        if ($resultado && (!isset($_POST['acao']) || $_POST['acao'] == 'cadastrar')) {
            if (isset($resultado['sucesso']) && $resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . $resultado['mensagem'] . '</div>';
            } elseif (isset($resultado['erros'])) {
                $mensagens = '<div class="form-message error">';
                foreach ($resultado['erros'] as $erro) {
                    $mensagens .= '<p>' . htmlspecialchars($erro) . '</p>';
                }
                $mensagens .= '</div>';
            }
        }
        return '
        <div class="form-container">
            ' . $mensagens . '
            <form action="" method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome Completo*</label>
                        <input required type="text" id="nome" name="nome" required maxlength="100" placeholder="Digite o nome completo"
                               value="' . (isset($dadosForm['nome']) ? htmlspecialchars($dadosForm['nome']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="cns">CNS*</label>
                        <input required type="text" id="cns" name="cns" required maxlength="100" placeholder="Digite o CNS"
                               value="' . (isset($dadosForm['cns']) ? htmlspecialchars($dadosForm['cns']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="data_nascimento">Data*</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required placeholder="dd/mm/aaaa"
                               value="' . (isset($dadosForm['data_nascimento']) ? htmlspecialchars($dadosForm['data_nascimento']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="raca_cor">Raça/Cor*</label>
                        <select required id="raca_cor" name="raca_cor" required>
                            <option value="">Selecionar</option>
                            <option value="01" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '01' ? 'selected' : '') . '>Branca</option>
                            <option value="02" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '02' ? 'selected' : '') . '>Preta</option>
                            <option value="03" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '03' ? 'selected' : '') . '>Parda</option>
                            <option value="04" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '04' ? 'selected' : '') . '>Amarela</option>
                            <option value="05" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '05' ? 'selected' : '') . '>Indígena</option>
                            <option value="99" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '99' ? 'selected' : '') . '>Sem informação</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="sexo">Sexo*</label>
                        <select required id="sexo" name="sexo" required>
                            <option value="">Selecionar</option>
                            <option value="M" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'M' ? 'selected' : '') . '>Masculino</option>
                            <option value="F" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'F' ? 'selected' : '') . '>Feminino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="etnia">Etnia</label>
                        <input type="text" id="etnia" name="etnia" maxlength="4" placeholder="Selecionar"
                               value="' . (isset($dadosForm['etnia']) ? htmlspecialchars($dadosForm['etnia']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="nacionalidade">Nacionalidade*</label>
                        <select required id="nacionalidade" name="nacionalidade" required>
                            <option value="">Selecionar</option>
                            <option value="10" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '10' ? 'selected' : '') . '>Brasileira</option>
                            <option value="20" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '20' ? 'selected' : '') . '>Naturalizado</option>
                            <option value="30" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '30' ? 'selected' : '') . '>Estrangeiro</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="codigo_logradouro">Tipo do Logradouro*</label>
                        <select required id="codigo_logradouro" name="codigo_logradouro" required>
                            <option value="">Selecionar</option>
                            <option value="81" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '81' ? 'selected' : '') . '>Rua</option>
                            <option value="8" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '8' ? 'selected' : '') . '>Avenida</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="endereco">Logradouro*</label>
                        <input required type="text" id="endereco" name="endereco" required maxlength="100" placeholder="Digite o logradouro"
                               value="' . (isset($dadosForm['endereco']) ? htmlspecialchars($dadosForm['endereco']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="numero">Número*</label>
                        <input required type="text" id="numero" name="numero" required maxlength="10" placeholder="Digite o número"
                               value="' . (isset($dadosForm['numero']) ? htmlspecialchars($dadosForm['numero']) : '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" name="complemento" maxlength="30" placeholder="Ex: Apt 101"
                               value="' . (isset($dadosForm['complemento']) ? htmlspecialchars($dadosForm['complemento']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="bairro">Bairro*</label>
                        <input required type="text" id="bairro" name="bairro" required maxlength="60" placeholder="Informe o bairro"
                               value="' . (isset($dadosForm['bairro']) ? htmlspecialchars($dadosForm['bairro']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="cep">CEP*</label>
                        <input required type="text" id="cep" name="cep" required maxlength="9" placeholder="00000-000"
                               value="' . (isset($dadosForm['cep']) ? htmlspecialchars($dadosForm['cep']) : '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone">Telefone*</label>
                        <input required type="text" id="telefone" name="telefone" required maxlength="15" placeholder="(00) 00000-0000"
                               value="' . (isset($dadosForm['telefone']) ? htmlspecialchars($dadosForm['telefone']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com"
                               value="' . (isset($dadosForm['email']) ? htmlspecialchars($dadosForm['email']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="situacao_rua">Situação de Rua?</label>
                        <select required id="situacao_rua" name="situacao_rua" required>
                            <option value="N" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'N' ? 'selected' : '') . '>Não</option>
                            <option value="S" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'S' ? 'selected' : '') . '>Sim</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-add">
                    <i class="fas fa-save"></i> Salvar Paciente
                </button>
            </form>
        </div>';
    }

    private function getListagemPacientes($resultado = null, $pacienteBuscado = null)
    {
        $mensagens = '';
        if ($resultado && isset($_POST['acao']) && ($_POST['acao'] == 'excluir' || $_POST['acao'] == 'excluir_multiplos')) {
            if (isset($resultado['sucesso']) && $resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . $resultado['mensagem'] . '</div>';
            } elseif (isset($resultado['erros'])) {
                $mensagens = '<div class="form-message error">';
                foreach ($resultado['erros'] as $erro) {
                    $mensagens .= '<p>' . htmlspecialchars($erro) . '</p>';
                }
                $mensagens .= '</div>';
            }
        }

        // Buscar todos os pacientes (só se não estiver visualizando um)
        $pacientes = [];
        if (!$pacienteBuscado) {
            $pacientes = $this->paciente->listar();
        }

        $total = count($pacientes);
        $tabelaPacientes = '';

        if ($pacienteBuscado) {
            $dataNasc = date('d/m/Y', strtotime($pacienteBuscado['data_nascimento']));
            $tabelaPacientes = '
            <div class="table-container">
                <div class="paciente-detalhe">
                    <h3>Dados do Paciente Encontrado</h3>
                    <div class="paciente-info">
                        <p><strong>ID:</strong> ' . htmlspecialchars($pacienteBuscado['id']) . '</p>
                        <p><strong>Nome:</strong> ' . htmlspecialchars($pacienteBuscado['nome']) . '</p>
                        <p><strong>CNS:</strong> ' . (!empty($pacienteBuscado['cns']) ? htmlspecialchars($pacienteBuscado['cns']) : '-') . '</p>
                        <p><strong>Data de Nascimento:</strong> ' . $dataNasc . '</p>
                        <p><strong>Sexo:</strong> ' . ($pacienteBuscado['sexo'] == 'M' ? 'Masculino' : 'Feminino') . '</p>
                        <p><strong>Telefone:</strong> ' . (!empty($pacienteBuscado['telefone']) ? htmlspecialchars($pacienteBuscado['telefone']) : '-') . '</p>
                        <p><strong>Email:</strong> ' . (!empty($pacienteBuscado['email']) ? htmlspecialchars($pacienteBuscado['email']) : '-') . '</p>
                        <p><strong>Endereço:</strong> ' . htmlspecialchars($pacienteBuscado['endereco'] . ', ' . $pacienteBuscado['numero'] . (!empty($pacienteBuscado['complemento']) ? ' - ' . $pacienteBuscado['complemento'] : '')) . '</p>
                        <p><strong>Bairro:</strong> ' . htmlspecialchars($pacienteBuscado['bairro']) . '</p>
                        <p><strong>CEP:</strong> ' . (!empty($pacienteBuscado['cep']) ? htmlspecialchars($pacienteBuscado['cep']) : '-') . '</p>
                        <p><strong>Raça/Cor:</strong> ' . $this->getDescricaoRacaCor($pacienteBuscado['raca_cor']) . '</p>
                        <p><strong>Nacionalidade:</strong> ' . $this->getDescricaoNacionalidade($pacienteBuscado['nacionalidade']) . '</p>
                        <p><strong>Situação de Rua:</strong> ' . ($pacienteBuscado['situacao_rua'] == 'S' ? 'Sim' : 'Não') . '</p>
                    </div>
                    <div class="paciente-actions">
                        <button class="btn-edit" onclick="editarPaciente(' . $pacienteBuscado['id'] . ')" title="Editar">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-delete" onclick="confirmarExclusao(' . $pacienteBuscado['id'] . ')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                        <button class="btn-evolucao" onclick="abrirEvolucao(' . $pacienteBuscado['id'] . ')">
                            <i class="fas fa-file-medical"></i> Evolução
                        </button>
                        <button class="btn-historico" onclick="showSubTab(\'pacientes\', \'historico\', this)">
                            <i class="fas fa-history"></i> Histórico
                        </button>
                        <a href="?sub=documentos" class="btn-clear">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>';
        } else {
            $formularioBusca = '
            <div class="search-bar">
                <form method="GET" class="search-form">
                    <input type="hidden" name="sub" value="documentos">
                    <input type="number" name="id" placeholder="Digite o ID do paciente" min="1" required>
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i> Buscar Paciente
                    </button>
                    <a href="?sub=documentos" class="btn-clear">Limpar Busca</a>
                </form>
            </div>';

            if (!empty($pacientes)) {
                $tabelaPacientes = '
                <div class="table-container">
                    <table class="pacientes-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all" onclick="selecionarTodos(this)"></th>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CNS</th>                            
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>';
                foreach ($pacientes as $paciente) {
                    $tabelaPacientes .= '
                        <tr>
                            <td><input type="checkbox" name="paciente_ids[]" value="' . $paciente['id'] . '" class="checkbox-paciente"></td>
                            <td>' . htmlspecialchars($paciente['id']) . '</td>
                            <td>' . htmlspecialchars($paciente['nome']) . '</td>
                            <td>' . (!empty($paciente['cns']) ? htmlspecialchars($paciente['cns']) : '-') . '</td>   
                            <td>' . (!empty($paciente['telefone']) ? htmlspecialchars($paciente['telefone']) : '-') . '</td>
                            <td>
                                <a href="?id=' . $paciente['id'] . '&sub=documentos" class="btn-view" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn-edit" onclick="abrirEvolucao(' . $paciente['id'] . ')" title="Evolucao">
                                    <i class="fas fa-file-medical"></i>
                                </button>
                            </td>
                        </tr>';
                }
                $tabelaPacientes .= '
                        </tbody>
                    </table>
                </div>
                <div class="table-actions">
                    <button class="btn-delete-multiple" onclick="excluirSelecionados()" id="btn-excluir-selecionados" style="display:none;">
                        <i class="fas fa-trash"></i> Excluir Selecionados
                    </button>
                </div>';
            } else {
                $tabelaPacientes = '<div class="no-data">Nenhum paciente encontrado.</div>';
            }
        }

        return '
        <div class="listagem-container">
            ' . $mensagens . '
            ' . (isset($formularioBusca) ? $formularioBusca : '') . '
            <div class="table-header">
                <h3>' . ($pacienteBuscado ? 'Detalhes do Paciente' : 'Listagem de Pacientes (' . $total . ' cadastrado' . ($total != 1 ? 's' : '') . ')') . '</h3>
            </div>
            ' . $tabelaPacientes . '
        </div>';
    }

    private function getDescricaoRacaCor($codigo)
    {
        $racas = [
            '01' => 'Branca',
            '02' => 'Preta',
            '03' => 'Parda',
            '04' => 'Amarela',
            '05' => 'Indígena',
            '99' => 'Sem informação'
        ];
        return isset($racas[$codigo]) ? $racas[$codigo] : $codigo;
    }

    private function getDescricaoNacionalidade($codigo)
    {
        $nacionalidades = [
            '10' => 'Brasileira',
            '20' => 'Naturalizado',
            '30' => 'Estrangeiro'
        ];
        return isset($nacionalidades[$codigo]) ? $nacionalidades[$codigo] : $codigo;
    }

    private function excluirMultiplos($ids)
    {
        if (empty($ids) || !is_array($ids)) {
            return [
                'sucesso' => false,
                'erros' => ['Nenhum paciente selecionado para exclusão.']
            ];
        }
        $sucessos = 0;
        $erros = [];
        foreach ($ids as $id) {
            $resultado = $this->paciente->excluir($id);
            if ($resultado['sucesso']) {
                $sucessos++;
            } else {
                $erros[] = "Erro ao excluir paciente ID $id: " . implode(', ', $resultado['erros']);
            }
        }
        if (empty($erros)) {
            return [
                'sucesso' => true,
                'mensagem' => "$sucessos paciente(s) excluído(s) com sucesso!"
            ];
        } else {
            return [
                'sucesso' => $sucessos > 0,
                'mensagem' => "$sucessos paciente(s) excluído(s) com sucesso.",
                'erros' => $erros
            ];
        }
    }

    private function getFormularioEdicao($resultado = null)
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            return '<div class="form-message error">ID do paciente não informado.</div>';
        }
        $paciente = $this->paciente->buscarPorId($id);
        if (!$paciente) {
            return '<div class="form-message error">Paciente não encontrado.</div>';
        }
        $dadosForm = [];
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        } else {
            $dadosForm = $paciente;
        }
        $mensagens = '';
        if ($resultado && (!isset($_POST['acao']) || $_POST['acao'] == 'atualizar')) {
            if (isset($resultado['sucesso']) && $resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . $resultado['mensagem'] . '</div>';
            } elseif (isset($resultado['erros'])) {
                $mensagens = '<div class="form-message error">';
                foreach ($resultado['erros'] as $erro) {
                    $mensagens .= '<p>' . htmlspecialchars($erro) . '</p>';
                }
                $mensagens .= '</div>';
            }
        }
        return '
        <div class="form-container">
            ' . $mensagens . '
            <form action="" method="POST">
                <input type="hidden" name="acao" value="atualizar">
                <input type="hidden" name="id" value="' . $id . '">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome Completo*</label>
                        <input required type="text" id="nome" name="nome" required maxlength="100" placeholder="Digite o nome completo"
                            value="' . (isset($dadosForm['nome']) ? htmlspecialchars($dadosForm['nome']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="cns">CNS*</label>
                        <input required type="text" id="cns" name="cns" required maxlength="100" placeholder="Digite o CNS"
                            value="' . (isset($dadosForm['cns']) ? htmlspecialchars($dadosForm['cns']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="data_nascimento">Data*</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required placeholder="dd/mm/aaaa"
                            value="' . (isset($dadosForm['data_nascimento']) ? htmlspecialchars($dadosForm['data_nascimento']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="raca_cor">Raça/Cor*</label>
                        <select required id="raca_cor" name="raca_cor" required>
                            <option value="">Selecionar</option>
                            <option value="01" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '01' ? 'selected' : '') . '>Branca</option>
                            <option value="02" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '02' ? 'selected' : '') . '>Preta</option>
                            <option value="03" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '03' ? 'selected' : '') . '>Parda</option>
                            <option value="04" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '04' ? 'selected' : '') . '>Amarela</option>
                            <option value="05" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '05' ? 'selected' : '') . '>Indígena</option>
                            <option value="99" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '99' ? 'selected' : '') . '>Sem informação</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="sexo">Sexo*</label>
                        <select required id="sexo" name="sexo" required>
                            <option value="">Selecionar</option>
                            <option value="M" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'M' ? 'selected' : '') . '>Masculino</option>
                            <option value="F" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'F' ? 'selected' : '') . '>Feminino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="etnia">Etnia</label>
                        <input type="text" id="etnia" name="etnia" maxlength="4" placeholder="Selecionar"
                            value="' . (isset($dadosForm['etnia']) ? htmlspecialchars($dadosForm['etnia']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="nacionalidade">Nacionalidade*</label>
                        <select required id="nacionalidade" name="nacionalidade" required>
                            <option value="">Selecionar</option>
                            <option value="10" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '10' ? 'selected' : '') . '>Brasileira</option>
                            <option value="20" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '20' ? 'selected' : '') . '>Naturalizado</option>
                            <option value="30" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '30' ? 'selected' : '') . '>Estrangeiro</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="codigo_logradouro">Tipo do Logradouro*</label>
                        <select required id="codigo_logradouro" name="codigo_logradouro" required>
                            <option value="">Selecionar</option>
                            <option value="81" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '81' ? 'selected' : '') . '>Rua</option>
                            <option value="8" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '8' ? 'selected' : '') . '>Avenida</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="endereco">Logradouro*</label>
                        <input required type="text" id="endereco" name="endereco" required maxlength="100" placeholder="Digite o logradouro"
                            value="' . (isset($dadosForm['endereco']) ? htmlspecialchars($dadosForm['endereco']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="numero">Número*</label>
                        <input required type="text" id="numero" name="numero" required maxlength="10" placeholder="Digite o número"
                            value="' . (isset($dadosForm['numero']) ? htmlspecialchars($dadosForm['numero']) : '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" name="complemento" maxlength="30" placeholder="Ex: Apt 101"
                            value="' . (isset($dadosForm['complemento']) ? htmlspecialchars($dadosForm['complemento']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="bairro">Bairro*</label>
                        <input required type="text" id="bairro" name="bairro" required maxlength="60" placeholder="Informe o bairro"
                            value="' . (isset($dadosForm['bairro']) ? htmlspecialchars($dadosForm['bairro']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="cep">CEP*</label>
                        <input required type="text" id="cep" name="cep" required maxlength="9" placeholder="00000-000"
                            value="' . (isset($dadosForm['cep']) ? htmlspecialchars($dadosForm['cep']) : '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone">Telefone*</label>
                        <input required type="text" id="telefone" name="telefone" required maxlength="15" placeholder="(00) 00000-0000"
                            value="' . (isset($dadosForm['telefone']) ? htmlspecialchars($dadosForm['telefone']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com"
                            value="' . (isset($dadosForm['email']) ? htmlspecialchars($dadosForm['email']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="situacao_rua">Situação de Rua?</label>
                        <select required id="situacao_rua" name="situacao_rua" required>
                            <option value="N" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'N' ? 'selected' : '') . '>Não</option>
                            <option value="S" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'S' ? 'selected' : '') . '>Sim</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-add">
                    <i class="fas fa-save"></i> Atualizar Paciente
                </button>
                <a href="?id=' . $id . '&sub=documentos" class="btn-clear" style="display:inline-block; margin-left:10px;">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </form>
        </div>';
    }

    // =============== HISTÓRICO DE EVOLUÇÕES ===============
    private function getHistoricoEvolucoesPorPaciente($pacienteId)
    {
        if (!$pacienteId || $pacienteId <= 0) {
            return '<div class="form-message error">Paciente não especificado para exibir histórico.</div>';
        }

        // Busca evoluções diretamente do banco
        try {
            $db = DB::connect();
            $stmt = $db->prepare("
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
            ");
            $stmt->execute([$pacienteId]);
            $evolucoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return '<div class="form-message error">Erro ao carregar histórico: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }

        if (empty($evolucoes)) {
            return '<div class="no-data">Nenhuma evolução registrada para este paciente.</div>';
        }

        $html = '<div class="table-container">
            <table class="pacientes-table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Formulário</th>
                        <th>Especialidade</th>
                        <th>Registrado por</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($evolucoes as $ev) {
            $dataFormatada = date('d/m/Y H:i', strtotime($ev['created_at']));
            $html .= '
            <tr>
                <td>' . htmlspecialchars($dataFormatada) . '</td>
                <td>' . htmlspecialchars($ev['nome_formulario']) . '</td>
                <td>' . htmlspecialchars($ev['especialidade']) . '</td>
                <td>' . (!empty($ev['criado_por']) ? htmlspecialchars($ev['criado_por']) : '-') . '</td>
                <td>
                    <a href="visualizar_evolucao.php?id=' . $ev['id'] . '" class="btn-view" title="Visualizar">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                </td>
            </tr>';
        }
        $html .= '</tbody></table></div>';
        $html .= '
        <div style="margin-top: 15px;">
            <a href="?id=' . $pacienteId . '&sub=documentos" class="btn-clear">
                <i class="fas fa-arrow-left"></i> Voltar aos Dados do Paciente
            </a>
        </div>';
        return $html;
    }
}
?>
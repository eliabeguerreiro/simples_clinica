<?php
include_once "../../../classes/db.class.php";
include_once "Evolucao.class.php";

class ConteudoRClinicoEvlt
{
    private $paciente;
    
    public function __construct()
    {
        $this->paciente = new Paciente();
    }
    
    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico - Evolução</title>
                <link rel="stylesheet" href="./src/style.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
            </head>
        HTML;

        // Renderiza o corpo da página
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

         $modules = [
            [
                'title' => 'Ver Ficha de Evolução',
                'description' => 'Visualize os formulários já criados',
                'icon' => '🏥',
                'link' => 'forms/'
            ],
            [
                'title' => 'Acessa o histórico de evoluções de um paciente',
                'description' => 'Visualize o histórico de evoluções de um paciente específico',
                'icon' => '📜',
                'link' => 'historico/'
            ]
        ];
        
        // Processa formulário se foi enviado
        $resultado = null;
        if ($_POST && isset($_POST['acao'])) {
            switch ($_POST['acao']) {
                case 'FormEditar':
                    $resultado = $this->paciente->editarForms($_POST);
                    break;
                case 'abrirEvolucao':
                    $resultado = $this->paciente->abrirEvolucao($_POST['id'], $_POST);
                    break;
            }
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
                    <h2>Registro Clínico - Evolução</h2>
                    
                    <!-- Abas principais de navegação entre módulos -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn" onclick="redirectToTab('pacientes')">Pacientes</button>
                        <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
                        <button class="tab-btn active" onclick="redirectToTab('evolucoes')">Evoluções</button>
                    </div>
                    
                    <!-- Sub-abas do módulo atual -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-pacientes">
                            <button class="tab-btn active" data-main="pacientes" data-sub="cadastro" onclick="showSubTab('pacientes', 'cadastro', this)">Cadastro de Formulário</button>
                            <button class="tab-btn" data-main="pacientes" data-sub="documentos" onclick="showSubTab('pacientes', 'documentos', this)">Listagem</button>
                            <button class="tab-btn" data-main="pacientes" data-sub="historico" onclick="showSubTab('pacientes', 'historico', this)">Histórico</button>
                        </div>
                    </div>
                    
                    <!-- Conteúdo das abas -->
                    <div id="tab-content">
                        <div id="pacientes-cadastro" class="tab-content active">
                            {$this->getFormularioCadastro($resultado)}
                        </div>
                        <div id="pacientes-documentos" class="tab-content" style="display:none;">
                            {$this->getListagemPacientes($resultado)}
                        </div>
                        <div id="pacientes-historico" class="tab-content" style="display:none;">
                            <p>Conteúdo Histórico de Pacientes.</p>
                        </div>
                    </div>
                </section>

                <!-- Modal de confirmação de exclusão -->
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
        // Mantém os dados no formulário em caso de erro
        $dadosForm = [];
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        } elseif (isset($_POST) && (!isset($_POST['acao']) || $_POST['acao'] == 'cadastrar')) {
            $dadosForm = $_POST;
        }
        
        // Exibe mensagens de sucesso/erro
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
            <form action="construtor_forms.php" method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome da Fomulário padrão de evolução</label>
                        <input required type="text" id="nome" name="nome" required maxlength="100" placeholder="Digite o nome sem citar a especialidade"
                               value="' . (isset($dadosForm['nome']) ? htmlspecialchars($dadosForm['nome']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="s_n_anexo">Formulário recebe anexos de arquivos*</label>
                        <select required id="s_n_anexo" name="s_n_anexo" required>
                            <option value="N" ' . (isset($dadosForm['s_n_anexo']) && $dadosForm['s_n_anexo'] == 'N' ? 'selected' : '') . '>Não</option>
                            <option value="S" ' . (isset($dadosForm['s_n_anexo']) && $dadosForm['s_n_anexo'] == 'S' ? 'selected' : '') . '>Sim</option>
                        </select>
                    </div>
                   
                    <div class="form-group">
                        <label for="especialidade">Especialidade*</label>
                        <select required id="especialidade" name="especialidade" required>
                            <option value="">Selecionar</option>
                            <option value="FISIO" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'FISIO' ? 'selected' : '') . '>Fisio</option>
                            <option value="FONO" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'FONO' ? 'selected' : '') . '>T. Ocupacional</option>
                            <option value="TEOC" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'TEOC' ? 'selected' : '') . '>Fono</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status*</label>
                        <select required id="status" name="status">
                            <option value="1" ' . (isset($dadosForm['status']) && $dadosForm['status'] == '1' ? 'selected' : '') . '>Ativo</option>
                            <option value="0" ' . (isset($dadosForm['status']) && $dadosForm['status'] == '0' ? 'selected' : '') . '>Inativo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <input required type="text" id="descricao" name="descricao" required maxlength="100" placeholder="Digite a descrição"
                            value="' . (isset($dadosForm['descricao']) ? htmlspecialchars($dadosForm['descricao']) : '') . '">
                    </div>
                </div>

           

                <button type="submit" class="btn-add">
                    <i class="fas fa-edit"></i> Iniciar</button>
            </form>
        </div>';
    }
    
    private function getListagemPacientes($resultado = null)
    {
        // Exibe mensagens de sucesso/erro
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
        
        // Verificar se é uma busca por ID
        $pacienteBuscado = null;
        $buscaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($buscaId > 0) {
            $pacienteBuscado = $this->paciente->buscarPorId($buscaId);
        }
        
        // Buscar todos os pacientes
        $pacientes = $this->paciente->listar();
        $total = count($pacientes);
        
        $tabelaPacientes = '';
        
        // Se foi feita uma busca por ID e encontrou o paciente
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
                        <button class="btn-edit" onclick="editarPaciente(' . $pacienteBuscado['id'] . ')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-delete" onclick="confirmarExclusao(' . $pacienteBuscado['id'] . ')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                        <button class="btn-evolucao" onclick="abrirEvolucao(' . $pacienteBuscado['id'] . ')">
                            <i class="fas fa-file-medical"></i> Evolução
                        </button>
                        <a href="?sub=documentos" class="btn-clear">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>';
        } else {
            // Formulário de busca por ID
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
            
            // Mostrar lista de todos os pacientes
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
                                <button class="btn-edit" onclick="editarPaciente(' . $paciente['id'] . ')" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="confirmarExclusao(' . $paciente['id'] . ')" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
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
}
?>
<?php
include_once "../../../classes/db.class.php";
include_once "evolucao.class.php";

class ConteudoRClinicoEvlt
{
    private $evolucao;
    private $paciente_id;

    public function __construct($paciente_id = null)
    {
        $this->evolucao = new Evolucao();
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
                <title>Registro Clínico - Evolução</title>
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
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario'] ?? 'Usuário');

        // Processa formulário de criação de formulário
        $resultado = null;
        if ($_POST && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
            $resultado = $this->evolucao->criarFormulario($_POST);
            if ($resultado['sucesso']) {
                header("Location: construtor_forms.php?form_id=" . $resultado['id']);
                exit;
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
                    <!-- Abas principais -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn" onclick="redirectToTab('pacientes')">Pacientes</button>
                        <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
                        <button class="tab-btn active" onclick="redirectToTab('evolucoes')">Evoluções</button>
                    </div>
                    <!-- Sub-abas -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-pacientes">
                            <button class="tab-btn active" data-main="pacientes" data-sub="cadastro" onclick="showSubTab('pacientes', 'cadastro', this)">Cadastro de Formulário</button>
                            <button class="tab-btn" data-main="pacientes" data-sub="documentos" onclick="showSubTab('pacientes', 'documentos', this)">Listagem de Formulários</button>
                        </div>
                    </div>
                    <!-- Conteúdo -->
                    <div id="tab-content">
                        <div id="pacientes-cadastro" class="tab-content active">
                            {$this->getFormularioCadastro($resultado)}
                        </div>
                        <div id="pacientes-documentos" class="tab-content" style="display:none;">
                            {$this->getListagemFormularios($resultado)}
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
                            <p>Tem certeza que deseja excluir este item?</p>
                            <p><strong>Esta ação não pode ser desfeita.</strong></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                            <button class="btn-delete" id="confirmar-exclusao">Excluir</button>
                        </div>
                    </div>
                </div>
            </body>
        HTML;
        return $html;
    }

    private function getFormularioCadastro($resultado = null)
    {
        $dadosForm = [];
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        } elseif (isset($_POST) && $_POST['acao'] == 'cadastrar') {
            $dadosForm = $_POST;
        }

        $mensagens = '';
        if ($resultado && $_POST['acao'] == 'cadastrar') {
            if ($resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . htmlspecialchars($resultado['mensagem']) . '</div>';
            } else {
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
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome">Nome do Formulário padrão de evolução*</label>
                        <input required type="text" id="nome" name="nome" maxlength="100" placeholder="Ex: Evolução Diária"
                               value="' . (isset($dadosForm['nome']) ? htmlspecialchars($dadosForm['nome']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="s_n_anexo">Formulário recebe anexos de arquivos*</label>
                        <select required id="s_n_anexo" name="s_n_anexo">
                            <option value="N" ' . (isset($dadosForm['s_n_anexo']) && $dadosForm['s_n_anexo'] == 'N' ? 'selected' : '') . '>Não</option>
                            <option value="S" ' . (isset($dadosForm['s_n_anexo']) && $dadosForm['s_n_anexo'] == 'S' ? 'selected' : '') . '>Sim</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="especialidade">Especialidade*</label>
                        <select required id="especialidade" name="especialidade">
                            <option value="">Selecionar</option>
                            <option value="FISIO" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'FISIO' ? 'selected' : '') . '>Fisio</option>
                            <option value="FONO" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'FONO' ? 'selected' : '') . '>T. Ocupacional</option>
                            <option value="TEOC" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'TEOC' ? 'selected' : '') . '>Fono</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ativo">Ativo*</label>
                        <select required id="ativo" name="ativo">
                            <option value="1" ' . (isset($dadosForm['ativo']) && $dadosForm['ativo'] == '1' ? 'selected' : '') . '>Ativo</option>
                            <option value="0" ' . (isset($dadosForm['ativo']) && $dadosForm['ativo'] == '0' ? 'selected' : '') . '>Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <input type="text" id="descricao" name="descricao" maxlength="255" placeholder="Ex: Formulário para evolução diária de fisioterapia"
                            value="' . (isset($dadosForm['descricao']) ? htmlspecialchars($dadosForm['descricao']) : '') . '">
                    </div>
                </div>
                <button type="submit" class="btn-add">
                    <i class="fas fa-edit"></i> Iniciar Construção
                </button>
            </form>
        </div>';
    }

    private function getListagemFormularios($resultado = null)
    {
        $mensagens = '';
        if ($resultado && isset($resultado['sucesso'])) {
            if ($resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . htmlspecialchars($resultado['mensagem']) . '</div>';
            } else {
                $mensagens = '<div class="form-message error">';
                foreach ($resultado['erros'] ?? [] as $erro) {
                    $mensagens .= '<p>' . htmlspecialchars($erro) . '</p>';
                }
                $mensagens .= '</div>';
            }
        }

        try {
            $formularios = $this->evolucao->listarFormularios();
        } catch (Exception $e) {
            $formularios = [];
            $mensagens = '<div class="form-message error">Erro ao carregar formulários: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }

        $total = count($formularios);
        if (!empty($formularios)) {
            $tabelaFormularios = '
            <div class="table-container">
                <table class="pacientes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Formulário</th>
                            <th>Especialidade</th>
                            <th>Descrição</th>
                            <th>Ativo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($formularios as $form) {
                $ativo = $form['ativo'] == 1 ? 'Ativo' : 'Inativo';
                $descricao = !empty($form['descricao']) ? htmlspecialchars($form['descricao']) : '-';
                $linkRender = "render_forms.php?form_id=" . (int)$form['id'];
                if ($this->paciente_id) {
                    $linkRender .= "&paciente_id=" . (int)$this->paciente_id;
                }
                $tabelaFormularios .= '
                    <tr>
                        <td>' . (int)$form['id'] . '</td>
                        <td>' . htmlspecialchars($form['nome']) . '</td>
                        <td>' . htmlspecialchars($form['especialidade']) . '</td>
                        <td>' . $descricao . '</td>
                        <td>' . $ativo . '</td>
                        <td>
                            <a href="construtor_forms.php?form_id=' . (int)$form['id'] . '" class="btn-view" title="Gerenciar Perguntas">
                                <i class="fas fa-edit"></i> Gerenciar
                            </a>
                            <a href="' . $linkRender . '" class="btn-view" title="Preencher Evolução" style="margin-left:8px;">
                                <i class="fas fa-file-medical"></i> Preencher
                            </a>
                        </td>
                    </tr>';
            }
            $tabelaFormularios .= '
                    </tbody>
                </table>
            </div>';
        } else {
            $tabelaFormularios = '<div class="no-data">Nenhum formulário encontrado.</div>';
        }

        return '
        <div class="listagem-container">
            ' . $mensagens . '
            <div class="table-header">
                <h3>Listagem de Formulários (' . $total . ' cadastrado' . ($total != 1 ? 's' : '') . ')</h3>
                ' . ($this->paciente_id ? '<p><strong>Paciente vinculado:</strong> ID ' . (int)$this->paciente_id . '</p>' : '') . '
            </div>
            ' . $tabelaFormularios . '
        </div>';
    }
}
?>
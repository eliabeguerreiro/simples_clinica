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

    private function usuarioTemPermissao($permissao)
    {
        return isset($_SESSION['data_user']['permissoes']) && in_array($permissao, $_SESSION['data_user']['permissoes']);
    }

    private function temQualquerPermissaoDeEvolucao()
    {
        $permissoes = $_SESSION['data_user']['permissoes'] ?? [];
        foreach ($permissoes as $p) {
            if (
                strpos($p, 'evolucoes.') === 0 ||
                strpos($p, 'formularios.fisio.') === 0 ||
                strpos($p, 'formularios.fono.') === 0 ||
                strpos($p, 'formularios.teoc.') === 0
            ) {
                return true;
            }
        }
        return false;
    }

    private function carregarPermissoesDoPerfil($perfilId)
    {
        try {
            include "../../../classes/db.class.php";
            $db = DB::connect();
            $stmt = $db->prepare("
                SELECT p.chave
                FROM perfil_permissao pp
                JOIN permissoes p ON pp.permissao_id = p.id
                WHERE pp.perfil_id = ?
            ");
            $stmt->execute([$perfilId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Erro ao carregar permissões: " . $e->getMessage());
            return [];
        }
    }

    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico - Formulários de Evolução</title>
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
        // 1. Valida login
        if (!isset($_SESSION['data_user']) || !isset($_SESSION['login_time'])) {
            $_SESSION['msg'] = 'Realize o login para acessar o Registro Clínico.';
            header('Location: ../../');
            exit;
        }

        // 2. Verifica se perfil foi carregado
        if (!isset($_SESSION['data_user']['perfil_id'])) {
            $_SESSION['msg'] = 'Seu usuário não possui perfil definido.';
            header('Location: ../../');
            exit;
        }

        // 3. Carrega permissões na sessão (se necessário)
        if (!isset($_SESSION['data_user']['permissoes'])) {
            $permissoes = $this->carregarPermissoesDoPerfil($_SESSION['data_user']['perfil_id']);
            $_SESSION['data_user']['permissoes'] = $permissoes;
        }

        // 4. Verifica acesso mínimo a evoluções
        if (!$this->temQualquerPermissaoDeEvolucao()) {
            return '<div class="form-message error">Você não tem permissão para acessar o módulo de Evoluções.</div>';
        }

        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);
        $perfil = htmlspecialchars($_SESSION['data_user']['perfil_nome'] ?? 'Usuário');

        // Processa formulário de criação (se submetido)
        $resultado = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
            switch ($_POST['acao']) {
                case 'cadastrar':
                    $resultado = $this->evolucao->criarFormulario($_POST);
                    if ($resultado['sucesso']) {
                        header("Location: construtor_forms.php?form_id=" . $resultado['id']);
                        exit;
                    }
                    break;
                case 'alternar_status':
                    $form_id = (int)($_POST['form_id'] ?? 0);
                    if ($form_id > 0) {
                        $resultado = $this->evolucao->alternarStatusFormulario($form_id);
                    } else {
                        $resultado = ['sucesso' => false, 'erros' => ['ID de formulário inválido.']];
                    }
                    break;
            }
        }

        $html = <<<HTML
            <body>
                <header>
                    <div class="logo">
                        <img src="src/vivenciar_logov2.png" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="../../">INICIO</a></li>
                            <li><a href="#">SUPORTE</a></li>
                            <li class="user-info">
                                <span class="user-icon"><i class="fas fa-user"></i></span>
                                <div class="user-details">
                                    <span class="user-name">{$nome}</span>
                                    <span class="user-role">{$perfil}</span>
                                </div>
                                <a href="?sair" class="btn-logout" title="Sair">
                                    <i class="fas fa-sign-out-alt"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </header>
                <section class="simple-box">
                    <h2>Registro Clínico - Formulários de Evolução</h2>
                    <!-- Abas principais -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn" onclick="redirectToTab('pacientes')">Pacientes</button>
                        <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
                        <button class="tab-btn active" onclick="redirectToTab('evolucoes')">Formulários</button>
                    </div>
                    <!-- Sub-abas -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-pacientes">
                            <button class="tab-btn" data-main="pacientes" data-sub="cadastro" onclick="showSubTab('pacientes', 'cadastro', this)">Cadastro de Formulário</button>
                            <button class="tab-btn active" data-main="pacientes" data-sub="ativos" onclick="showSubTab('pacientes', 'ativos', this)">Formulários Ativos</button>
                            <button class="tab-btn" data-main="pacientes" data-sub="inativos" onclick="showSubTab('pacientes', 'inativos', this)">Formulários Inativos</button>
                        </div>
                    </div>
                    <!-- Conteúdo -->
                    <div id="tab-content">
                        <div id="pacientes-cadastro" class="tab-content" style="display:none;">
                            {$this->getFormularioCadastro($resultado)}
                        </div>
                        <div id="pacientes-ativos" class="tab-content active">
                            {$this->getListagemFormularios($resultado, true)}
                        </div>
                        <div id="pacientes-inativos" class="tab-content" style="display:none;">
                            {$this->getListagemFormularios($resultado, false)}
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
        // Verifica permissão para criar formulários
        if (!$this->usuarioTemPermissao('evolucoes.fisio.criar') &&
            !$this->usuarioTemPermissao('evolucoes.fono.criar') &&
            !$this->usuarioTemPermissao('evolucoes.teoc.criar')) {
            return '<div class="form-message error">Você não tem permissão para criar formulários de evolução.</div>';
        }

        $dadosForm = [];
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {
            $dadosForm = $_POST;
        }

        $mensagens = '';
        if ($resultado && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {
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
                        <label for="nome">Nome do Formulário*</label>
                        <input required type="text" id="nome" name="nome" maxlength="100" placeholder="Ex: Avaliação Fisioterapêutica"
                               value="' . (isset($dadosForm['nome']) ? htmlspecialchars($dadosForm['nome']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="s_n_anexo">Recebe anexos?*</label>
                        <select required id="s_n_anexo" name="s_n_anexo">
                            <option value="N" ' . (isset($dadosForm['s_n_anexo']) && $dadosForm['s_n_anexo'] == 'N' ? 'selected' : '') . '>Não</option>
                            <option value="S" ' . (isset($dadosForm['s_n_anexo']) && $dadosForm['s_n_anexo'] == 'S' ? 'selected' : '') . '>Sim</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="especialidade">Especialidade*</label>
                        <select required id="especialidade" name="especialidade">
                            <option value="">Selecionar</option>
                            <option value="FISIO" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'FISIO' ? 'selected' : '') . '>Fisioterapia</option>
                            <option value="FONO" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'FONO' ? 'selected' : '') . '>Fonoaudiologia</option>
                            <option value="TEOC" ' . (isset($dadosForm['especialidade']) && $dadosForm['especialidade'] == 'TEOC' ? 'selected' : '') . '>Terapia Ocupacional</option>
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
                        <input type="text" id="descricao" name="descricao" maxlength="255" placeholder="Ex: Formulário para avaliação inicial"
                            value="' . (isset($dadosForm['descricao']) ? htmlspecialchars($dadosForm['descricao']) : '') . '">
                    </div>
                </div>
                <button type="submit" class="btn-add">
                    <i class="fas fa-edit"></i> Iniciar Construção
                </button>
            </form>
        </div>';
    }

    private function getListagemFormularios($resultado = null, $ativos = true)
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
            $formularios = $this->evolucao->listarFormularios($ativos);
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
                            <th>Codigo</th>
                            <th>Nome do Formulário</th>
                            <th>Especialidade</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($formularios as $form) {
                $descricao = !empty($form['descricao']) ? htmlspecialchars($form['descricao']) : '-';
                $tabelaFormularios .= '
                    <tr>
                        <td>' . (int)$form['id'] . '</td>
                        <td>' . htmlspecialchars($form['nome']) . '</td>
                        <td>' . htmlspecialchars($form['especialidade']) . '</td>
                        <td>' . $descricao . '</td>
                        <td>
                            <a href="construtor_forms.php?form_id=' . (int)$form['id'] . '" class="btn-view" title="Gerenciar Perguntas">
                                <i class="fas fa-list"></i> Perguntas
                            </a>
                            <a href="editar_forms.php?form_id=' . (int)$form['id'] . '" class="btn-view" title="Editar Dados do Formulário" style="margin-left:8px;">
                                <i class="fas fa-edit"></i> Editar Dados
                            </a>
                            <a href="render_forms.php?form_id=' . (int)$form['id'] . '" class="btn-view" title="Visualizar Formulário" style="margin-left:8px;">
                                <i class="fas fa-eye"></i> Visualizar
                            </a>';

                // Botão de alternar status só aparece se tiver permissão
                if ($this->usuarioTemPermissao('evolucoes.fisio.criar') ||
                    $this->usuarioTemPermissao('evolucoes.fono.criar') ||
                    $this->usuarioTemPermissao('evolucoes.teoc.criar')) {
                    $tabelaFormularios .= '
                            <form method="POST" style="display:inline; margin-left:8px;">
                                <input type="hidden" name="acao" value="alternar_status">
                                <input type="hidden" name="form_id" value="' . (int)$form['id'] . '">
                                <button type="submit" class="btn-toggle" title="' . ($ativos ? 'Desativar' : 'Reativar') . '">
                                    <i class="fas fa-' . ($ativos ? 'toggle-off' : 'toggle-on') . '"></i> ' . ($ativos ? 'Desativar' : 'Reativar') . '
                                </button>
                            </form>';
                }

                $tabelaFormularios .= '
                        </td>
                    </tr>';
            }
            $tabelaFormularios .= '
                    </tbody>
                </table>
            </div>';
        } else {
            $msg = $ativos ? 'Nenhum formulário ativo encontrado.' : 'Nenhum formulário inativo encontrado.';
            $tabelaFormularios = '<div class="no-data">' . $msg . '</div>';
        }

        return '
        <div class="listagem-container">
            ' . $mensagens . '
            <div class="table-header">
                <h3>Listagem de Formulários ' . ($ativos ? 'Ativos' : 'Inativos') . ' (' . $total . ')</h3>
            </div>
            ' . $tabelaFormularios . '
        </div>';
    }
}
?>
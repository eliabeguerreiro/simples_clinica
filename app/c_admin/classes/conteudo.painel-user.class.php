<?php
include_once "gest-user.class.php";

class ConteudoPainelUser
{
    private $usuario;

    public function __construct()
    {
        $this->usuario = new GestUser();
    }

    private function usuarioTemPermissao($permissao)
    {
        return isset($_SESSION['data_user']['permissoes']) && in_array($permissao, $_SESSION['data_user']['permissoes']);
    }

    public function render()
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestão de Usuários e Perfis</title>
            <link rel="stylesheet" href="src/style.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        </head>
HTML;

        $body = $this->renderBody();
        $html .= $body;

        $html .= <<<HTML
        <script src="src/script.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        </body>
        </html>
HTML;

        return $html;
    }

    private function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario'] ?? '');

        // Mensagem de sucesso/erro via GET (para reativação)
        $resultado = null;
        if (isset($_GET['msg']) && $_GET['msg'] == 'reativado') {
            $resultado = [
                'sucesso' => true,
                'mensagem' => 'Usuário reativado com sucesso!'
            ];
        }

        if ($_POST && isset($_POST['acao'])) {
            switch ($_POST['acao']) {
                case 'cadastrar':
                    $resultado = $this->usuario->cadastrar($_POST);
                    break;
                case 'desativar':
                    $resultado = $this->usuario->desativar($_POST['id']);
                    break;
                case 'reativar':
                    $resultado = $this->usuario->reativar($_POST['id']);
                    break;
                case 'atualizar':
                    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                        $resultado = $this->usuario->atualizar($_POST['id'], $_POST);
                    } else {
                        $resultado = [
                            'sucesso' => false,
                            'erros' => ['ID do usuário inválido.'],
                            'dados' => $_POST
                        ];
                    }
                    break;
                case 'cadastrar_perfil':
                    $resultado = $this->usuario->cadastrarPerfil($_POST);
                    break;
                case 'atualizar_perfil':
                    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                        $resultado = $this->usuario->atualizarPerfil($_POST['id'], $_POST);
                    } else {
                        $resultado = [
                            'sucesso' => false,
                            'erros' => ['ID de perfil inválido.'],
                            'dados' => $_POST
                        ];
                    }
                    break;
                case 'excluir_perfil':
                    $resultado = $this->usuario->excluirPerfil($_POST['id']);
                    break;
            }
        }

        $tabAtiva = 'usuarios';
        $subTabAtiva = 'documentos';
        $id = $_GET['id'] ?? 0;
        $id_perfil = $_GET['id_perfil'] ?? 0;

        if (isset($_GET['tab']) && $_GET['tab'] === 'perfis') {
            $tabAtiva = 'perfis';
            $subTabAtiva = $_GET['sub'] ?? 'listagem';
        } elseif ($id) {
            $tabAtiva = 'usuarios';
            $subTabAtiva = 'edicao';
        } elseif ($id_perfil) {
            $tabAtiva = 'perfis';
            $subTabAtiva = 'edicao';
        } elseif (isset($_GET['sub']) && $_GET['sub'] === 'inativos') {
            $tabAtiva = 'usuarios';
            $subTabAtiva = 'inativos';
        }

        $usuariosClass = $tabAtiva === 'usuarios' ? 'tab-btn active' : 'tab-btn';
        $perfisClass = $tabAtiva === 'perfis' ? 'tab-btn active' : 'tab-btn';

        $usuariosSubStyle = $tabAtiva === 'usuarios' ? 'display:flex;' : 'display:none;';
        $perfisSubStyle = $tabAtiva === 'perfis' ? 'display:flex;' : 'display:none;';

        $usuariosCadastroStyle = ($tabAtiva === 'usuarios' && $subTabAtiva === 'cadastro') ? 'display:block;' : 'display:none;';
        $usuariosListagemStyle = ($tabAtiva === 'usuarios' && $subTabAtiva === 'documentos') ? 'display:block;' : 'display:none;';
        $usuariosInativosStyle = ($tabAtiva === 'usuarios' && $subTabAtiva === 'inativos') ? 'display:block;' : 'display:none;';
        $usuariosEdicaoStyle = ($tabAtiva === 'usuarios' && $subTabAtiva === 'edicao') ? 'display:block;' : 'display:none;';
        $perfisListagemStyle = ($tabAtiva === 'perfis' && $subTabAtiva === 'listagem') ? 'display:block;' : 'display:none;';
        $perfisCadastroStyle = ($tabAtiva === 'perfis' && $subTabAtiva === 'cadastro') ? 'display:block;' : 'display:none;';
        $perfisEdicaoStyle = ($tabAtiva === 'perfis' && $subTabAtiva === 'edicao') ? 'display:block;' : 'display:none;';

        $initScript = '';
        if ($subTabAtiva === 'edicao') {
            $initScript = "<script>document.addEventListener('DOMContentLoaded', function() { showSubTab('{$tabAtiva}', 'edicao', null); });</script>";
        }

        return <<<HTML
        <body>
            <header>
                <div class="logo"><img src="#" alt="Logo"></div>
                <nav>
                    <ul>
                        <li><a href="../">INICIO</a></li>
                        <li><a href="#">SUPORTE</a></li>
                        <li><a href="?sair">SAIR</a></li>
                    </ul>
                </nav>
            </header>
            <section class="simple-box">
                <h2>Gestão de Usuários e Perfis</h2>
                <!-- Abas principais -->
                <div class="tabs">
                    <button class="{$usuariosClass}" onclick="switchMainTab('usuarios', this)">Usuários</button>
                    <button class="{$perfisClass}" onclick="switchMainTab('perfis', this)">Perfis</button>
                </div>
                <!-- Sub-abas -->
                <div id="sub-tabs">
                    <div class="sub-tabs" id="sub-usuarios" style="{$usuariosSubStyle}">
                        <button class="tab-btn" onclick="showSubTab('usuarios', 'cadastro', this)">Cadastro</button>
                        <button class="tab-btn" onclick="showSubTab('usuarios', 'documentos', this)">Ativos</button>
                        <button class="tab-btn" onclick="showSubTab('usuarios', 'inativos', this)">Inativos</button>
                    </div>
                    <div class="sub-tabs" id="sub-perfis" style="{$perfisSubStyle}">
                        <button class="tab-btn" onclick="showSubTab('perfis', 'listagem', this)">Listagem</button>
                        <button class="tab-btn" onclick="showSubTab('perfis', 'cadastro', this)">Novo Perfil</button>
                    </div>
                </div>
                <!-- Conteúdo -->
                <div id="tab-content">
                    <div id="usuarios-cadastro" class="tab-content" style="{$usuariosCadastroStyle}">
                        {$this->getFormularioCadastro($resultado)}
                    </div>
                    <div id="usuarios-documentos" class="tab-content" style="{$usuariosListagemStyle}">
                        {$this->getListagemUsuarios($resultado)}
                    </div>
                    <div id="usuarios-inativos" class="tab-content" style="{$usuariosInativosStyle}">
                        {$this->getListagemUsuariosInativos($resultado)}
                    </div>
                    <div id="usuarios-edicao" class="tab-content" style="{$usuariosEdicaoStyle}">
                        {$this->getFormularioEdicao($resultado)}
                    </div>
                    <div id="perfis-listagem" class="tab-content" style="{$perfisListagemStyle}">
                        {$this->getListagemPerfis($resultado)}
                    </div>
                    <div id="perfis-cadastro" class="tab-content" style="{$perfisCadastroStyle}">
                        {$this->getFormularioCadastroPerfil($resultado)}
                    </div>
                    <div id="perfis-edicao" class="tab-content" style="{$perfisEdicaoStyle}">
                        {$this->getFormularioEdicaoPerfil($resultado)}
                    </div>
                </div>
            </section>
            <!-- Modal de desativação -->
            <div id="modal-exclusao" class="modal" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Confirmar Desativação</h3>
                        <span class="close-modal" onclick="fecharModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja desativar este usuário?</p>
                        <p><strong>O usuário perderá acesso ao sistema.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                        <button class="btn-delete" id="confirmar-exclusao">Desativar</button>
                    </div>
                </div>
            </div>
            {$initScript}
        </body>
HTML;
    }

    // =========== USUÁRIOS ===========
    private function getFormularioCadastro($resultado = null)
    {
        if (!$this->usuarioTemPermissao('cadmin.usuarios.criar')) {
            return '<div class="form-message error">Você não tem permissão para criar usuários.</div>';
        }

        $dadosForm = [];
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        } elseif (isset($_POST) && ($_POST['acao'] ?? '') === 'cadastrar') {
            $dadosForm = $_POST;
        }

        $mensagens = '';
        if ($resultado && (!isset($_POST['acao']) || ($_POST['acao'] ?? '') === 'cadastrar')) {
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

        $perfis = $this->usuario->listarPerfis();
        $opcoesPerfis = '<option value="">Selecionar</option>';
        foreach ($perfis as $perfil) {
            $nomeExibicao = ucfirst(str_replace('_', ' ', $perfil['nome']));
            $selected = (isset($dadosForm['perfil_id']) && $dadosForm['perfil_id'] == $perfil['id']) ? 'selected' : '';
            $opcoesPerfis .= '<option value="' . $perfil['id'] . '" ' . $selected . '>' . htmlspecialchars($nomeExibicao) . '</option>';
        }

        return '
        <div class="form-container">' . $mensagens . '
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-row">
                    <div class="form-group">
                        <label>CPF*</label>
                        <input required name="cpf" maxlength="11" value="' . htmlspecialchars($dadosForm['cpf'] ?? '') . '">
                    </div>
                    <div class="form-group">
                        <label>Login*</label>
                        <input required name="login" maxlength="60" value="' . htmlspecialchars($dadosForm['login'] ?? '') . '">
                    </div>
                    <div class="form-group">
                        <label>Nome*</label>
                        <input required name="nm_usuario" maxlength="45" value="' . htmlspecialchars($dadosForm['nm_usuario'] ?? '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Senha*</label>
                        <input required type="password" name="senha" maxlength="30">
                    </div>
                    <div class="form-group">
                        <label>Tipo de Usuário*</label>
                        <select required name="perfil_id">' . $opcoesPerfis . '</select>
                    </div>
                </div>
                <button type="submit" class="btn-add">Salvar Usuário</button>
            </form>
        </div>';
    }

    private function getListagemUsuarios($resultado = null)
    {
        $mensagens = '';
        if ($resultado && isset($_POST['acao']) && $_POST['acao'] == 'desativar') {
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

        $usuarios = $this->usuario->listar();
        $total = count($usuarios);
        $rows = '';
        foreach ($usuarios as $u) {
            $rows .= '<tr>
                <td>' . htmlspecialchars($u['id']) . '</td>
                <td>' . htmlspecialchars($u['cpf']) . '</td>
                <td>' . htmlspecialchars($u['login']) . '</td>
                <td>' . htmlspecialchars($u['nm_usuario']) . '</td>
                <td>' . htmlspecialchars($u['perfil_nome'] ?? '—') . '</td>
                <td>
                    <div class="table-actions">';
            if ($this->usuarioTemPermissao('cadmin.usuarios.editar')) {
                $rows .= '<a href="?tab=usuarios&sub=edicao&id=' . $u['id'] . '" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Editar
                          </a>
                          <button type="button" class="btn-action btn-delete" onclick="confirmarDesativacao(' . $u['id'] . ')">
                            <i class="fas fa-trash"></i> Desativar
                          </button>';
            }
            $rows .= '</div>
                </td>
            </tr>';
        }

        $tabela = $rows ? '<table class="pacientes-table">
            <thead><tr><th>ID</th><th>CPF</th><th>Login</th><th>Nome</th><th>Perfil</th><th>Ações</th></tr></thead>
            <tbody>' . $rows . '</tbody>
        </table>' : '<div class="no-data">Nenhum usuário ativo.</div>';

        return '<div class="listagem-container">' . $mensagens . '
            <div class="table-header"><h3>Usuários Ativos (' . $total . ')</h3></div>
            <div class="table-container">' . $tabela . '</div>
        </div>';
    }

    private function getListagemUsuariosInativos($resultado = null)
    {
        $mensagens = '';
        if ($resultado && isset($_POST['acao']) && $_POST['acao'] == 'reativar') {
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

        $usuarios = $this->usuario->listarInativos();
        $total = count($usuarios);
        $rows = '';
        foreach ($usuarios as $u) {
            $rows .= '<tr>
                <td>' . htmlspecialchars($u['id']) . '</td>
                <td>' . htmlspecialchars($u['cpf']) . '</td>
                <td>' . htmlspecialchars($u['login']) . '</td>
                <td>' . htmlspecialchars($u['nm_usuario']) . '</td>
                <td>' . htmlspecialchars($u['perfil_nome'] ?? '—') . '</td>
                <td>
                    <div class="table-actions">';
            if ($this->usuarioTemPermissao('cadmin.usuarios.editar')) {
                $rows .= '<button type="button" class="btn-action btn-add" onclick="confirmarReativacao(' . $u['id'] . ')">
                            <i class="fas fa-redo"></i> Reativar
                          </button>';
            }
            $rows .= '</div>
                </td>
            </tr>';
        }

        $tabela = $rows ? '<table class="pacientes-table">
            <thead><tr><th>ID</th><th>CPF</th><th>Login</th><th>Nome</th><th>Perfil</th><th>Ações</th></tr></thead>
            <tbody>' . $rows . '</tbody>
        </table>' : '<div class="no-data">Nenhum usuário inativo.</div>';

        return '<div class="listagem-container">' . $mensagens . '
            <div class="table-header"><h3>Usuários Inativos (' . $total . ')</h3></div>
            <div class="table-container">' . $tabela . '</div>
        </div>';
    }

    private function getFormularioEdicao($resultado = null)
    {
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            return '<div class="form-message error">ID do usuário não informado.</div>';
        }

        if (!$this->usuarioTemPermissao('cadmin.usuarios.editar')) {
            return '<div class="form-message error">Você não tem permissão para editar usuários.</div>';
        }

        $usuario = $this->usuario->buscarPorId($id);
        if (!$usuario) {
            return '<div class="form-message error">Usuário não encontrado.</div>';
        }

        $dadosForm = $usuario;
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
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

        $perfis = $this->usuario->listarPerfis();
        $opcoesPerfis = '<option value="">Selecionar</option>';
        foreach ($perfis as $perfil) {
            $selected = ($dadosForm['perfil_id'] == $perfil['id']) ? 'selected' : '';
            $nomeExibicao = ucfirst(str_replace('_', ' ', $perfil['nome']));
            $opcoesPerfis .= '<option value="' . $perfil['id'] . '" ' . $selected . '>' . htmlspecialchars($nomeExibicao) . '</option>';
        }

        return '
        <div class="form-container">' . $mensagens . '
            <form method="POST">
                <input type="hidden" name="acao" value="atualizar">
                <input type="hidden" name="id" value="' . $id . '">
                <div class="form-row">
                    <div class="form-group">
                        <label>CPF*</label>
                        <input required name="cpf" maxlength="11" value="' . htmlspecialchars($dadosForm['cpf'] ?? '') . '">
                    </div>
                    <div class="form-group">
                        <label>Login*</label>
                        <input required name="login" maxlength="60" value="' . htmlspecialchars($dadosForm['login'] ?? '') . '">
                    </div>
                    <div class="form-group">
                        <label>Nome*</label>
                        <input required name="nm_usuario" maxlength="45" value="' . htmlspecialchars($dadosForm['nm_usuario'] ?? '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nova Senha (opcional)</label>
                        <input type="password" name="senha" maxlength="30" placeholder="Deixe em branco para manter a mesma">
                    </div>
                    <div class="form-group">
                        <label>Tipo de Usuário*</label>
                        <select required name="perfil_id">' . $opcoesPerfis . '</select>
                    </div>
                </div>
                <button type="submit" class="btn-add">Atualizar Usuário</button>
                <a href="?tab=usuarios&sub=documentos" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar à Listagem
                </a>
            </form>
        </div>';
    }

    // =========== PERFIS ===========
    private function getListagemPerfis($resultado = null)
    {
        $mensagens = '';
        if ($resultado && isset($_POST['acao']) && $_POST['acao'] == 'excluir_perfil') {
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

        $perfis = $this->usuario->listarPerfis();
        $total = count($perfis);
        $rows = '';
        foreach ($perfis as $p) {
            $rows .= '<tr>
                <td>' . htmlspecialchars($p['id']) . '</td>
                <td>' . htmlspecialchars($p['nome']) . '</td>
                <td>' . htmlspecialchars($p['descricao'] ?? '') . '</td>
                <td>
                    <div class="table-actions">';
            if ($this->usuarioTemPermissao('cadmin.perfis.editar')) {
                $rows .= '<a href="?tab=perfis&sub=edicao&id_perfil=' . $p['id'] . '" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Editar
                          </a>
                          <form method="POST" style="display:inline;" onsubmit="return confirm(\'Excluir perfil? Usuários vinculados perderão acesso.\');">
                            <input type="hidden" name="acao" value="excluir_perfil">
                            <input type="hidden" name="id" value="' . $p['id'] . '">
                            <button type="submit" class="btn-action btn-delete">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                          </form>';
            }
            $rows .= '</div>
                </td>
            </tr>';
        }

        $tabela = $rows ? '<table class="pacientes-table">
            <thead><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Ações</th></tr></thead>
            <tbody>' . $rows . '</tbody>
        </table>' : '<div class="no-data">Nenhum perfil cadastrado.</div>';

        return '<div class="listagem-container">' . $mensagens . '
            <div class="table-header"><h3>Perfis (' . $total . ')</h3></div>
            <div class="table-container">' . $tabela . '</div>
        </div>';
    }

    private function getFormularioCadastroPerfil($resultado = null)
    {
        if (!$this->usuarioTemPermissao('cadmin.perfis.criar')) {
            return '<div class="form-message error">Você não tem permissão para criar perfis.</div>';
        }

        $dadosForm = [];
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        } elseif (isset($_POST) && ($_POST['acao'] ?? '') === 'cadastrar_perfil') {
            $dadosForm = $_POST;
        }

        $mensagens = '';
        if ($resultado && (!isset($_POST['acao']) || ($_POST['acao'] ?? '') === 'cadastrar_perfil')) {
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

        $permissoes = $this->usuario->listarTodasPermissoes();
        $checkboxes = $this->gerarHtmlPermissoes($permissoes, $dadosForm['permissoes'] ?? []);

        return '
        <div class="form-container">' . $mensagens . '
            <form method="POST">
                <input type="hidden" name="acao" value="cadastrar_perfil">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome*</label>
                        <input required name="nome" maxlength="50" value="' . htmlspecialchars($dadosForm['nome'] ?? '') . '">
                    </div>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <input name="descricao" maxlength="150" value="' . htmlspecialchars($dadosForm['descricao'] ?? '') . '">
                </div>
                <div class="form-group">
                    <label>Permissões</label>
                    <div class="accordion-container">
                        ' . $checkboxes . '
                    </div>
                </div>
                <button type="submit" class="btn-add">Salvar Perfil</button>
            </form>
        </div>';
    }

    private function getFormularioEdicaoPerfil($resultado = null)
    {
        $id = $_GET['id_perfil'] ?? 0;
        if (!$id) return '<div class="form-message error">ID do perfil não informado.</div>';

        if (!$this->usuarioTemPermissao('cadmin.perfis.editar')) {
            return '<div class="form-message error">Você não tem permissão para editar perfis.</div>';
        }

        $perfil = $this->usuario->buscarPerfilPorId($id);
        if (!$perfil) return '<div class="form-message error">Perfil não encontrado.</div>';

        $dadosForm = $perfil;
        if ($resultado && isset($resultado['dados'])) {
            $dadosForm = $resultado['dados'];
        }

        $mensagens = '';
        if ($resultado && (!isset($_POST['acao']) || $_POST['acao'] == 'atualizar_perfil')) {
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

        $permissoes = $this->usuario->listarTodasPermissoes();
        $permissoesAtuais = $this->usuario->getPermissoesDoPerfil($id);
        $checkboxes = $this->gerarHtmlPermissoes($permissoes, $permissoesAtuais);

        return '
        <div class="form-container">' . $mensagens . '
            <form method="POST">
                <input type="hidden" name="acao" value="atualizar_perfil">
                <input type="hidden" name="id" value="' . $id . '">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome*</label>
                        <input required name="nome" maxlength="50" value="' . htmlspecialchars($dadosForm['nome'] ?? '') . '">
                    </div>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <input name="descricao" maxlength="150" value="' . htmlspecialchars($dadosForm['descricao'] ?? '') . '">
                </div>
                <div class="form-group">
                    <label>Permissões</label>
                    <div class="accordion-container">
                        ' . $checkboxes . '
                    </div>
                </div>
                <button type="submit" class="btn-add">Atualizar Perfil</button>
                <a href="?tab=perfis&sub=listagem" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar à Listagem
                </a>
            </form>
        </div>';
    }

    private function gerarHtmlPermissoes($todasPermissoes, $idsSelecionados)
    {
        $grupos = [
            'cadmin' => ['nome' => 'Painel Administrativo', 'icon' => 'fa-cogs'],
            'atendimento' => ['nome' => 'Atendimentos', 'icon' => 'fa-stethoscope'],
            'formularios' => ['nome' => 'Evoluções', 'icon' => 'fa-file-medical']
        ];

        foreach ($todasPermissoes as $p) {
            if (strpos($p['chave'], 'cadmin.') === 0) {
                $grupos['cadmin']['permissoes'][] = $p;
            } elseif (strpos($p['chave'], 'atendimento.') === 0) {
                $grupos['atendimento']['permissoes'][] = $p;
            } elseif (strpos($p['chave'], 'formularios.') === 0) {
                $grupos['formularios']['permissoes'][] = $p;
            }
        }

        $html = '';
        foreach ($grupos as $prefixo => $info) {
            if (!isset($info['permissoes']) || empty($info['permissoes'])) continue;

            $temPermissaoMarcada = false;
            foreach ($info['permissoes'] as $p) {
                if (in_array($p['id'], $idsSelecionados)) {
                    $temPermissaoMarcada = true;
                    break;
                }
            }

            $checkedMaster = $temPermissaoMarcada;
            $html .= '<div class="accordion-item">
                <div class="accordion-header">
                    <i class="fas ' . $info['icon'] . '"></i>
                    <strong>' . htmlspecialchars($info['nome']) . '</strong>
                    <label class="switch">
                        <input type="checkbox" class="master-toggle" data-prefix="' . $prefixo . '" ' . ($checkedMaster ? 'checked' : '') . '>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="accordion-content" ' . ($checkedMaster ? '' : 'style="display:none;"') . '>';

            foreach ($info['permissoes'] as $p) {
                $checked = in_array($p['id'], $idsSelecionados) ? 'checked' : '';
                $html .= '<label class="permission-item">
                    <input type="checkbox" name="permissoes[]" value="' . $p['id'] . '" ' . $checked . '>
                    ' . htmlspecialchars($p['chave']) . ' — ' . htmlspecialchars($p['descricao']) . '
                </label>';
            }

            $html .= '</div></div>';
        }

        return $html;
    }
}
?>
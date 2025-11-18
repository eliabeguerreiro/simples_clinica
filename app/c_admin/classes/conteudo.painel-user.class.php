<?php
include_once "gest-user.class.php";

class ConteudoPainelUser
{
    private $usuario;

    public function __construct()
    {
        $this->usuario = new GestUser();
    }

    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Gestão de Usuários</title>
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

        $resultado = null;
        if ($_POST && isset($_POST['acao'])) {
            switch ($_POST['acao']) {
                case 'cadastrar':
                    $resultado = $this->usuario->cadastrar($_POST);
                    break;
                case 'desativar':
                    $resultado = $this->usuario->desativar($_POST['id']);
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
                            <li><a href="../">INICIO</a></li>
                            <li><a href="#">SUPORTE</a></li>
                            <li><a href="?sair">SAIR</a></li>
                        </ul>
                    </nav>
                </header>
                <section class="simple-box">
                    <h2>Gestão de Usuários</h2>
                    <!-- Abas principais -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn active" onclick="redirectToTab('usuarios')">Usuários</button>
                        <button class="tab-btn disabled" title="Módulo em desenvolvimento" style="cursor:not-allowed; opacity:0.6;" onclick="event.preventDefault();">Perfis</button>
                    </div>
                    <!-- Sub-abas -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-usuarios">
                            <button class="tab-btn" data-main="usuarios" data-sub="cadastro" onclick="showSubTab('usuarios', 'cadastro', this)">Cadastro</button>
                            <button class="tab-btn active" data-main="usuarios" data-sub="documentos" onclick="showSubTab('usuarios', 'documentos', this)">Listagem</button>
                        </div>
                    </div>
                    <!-- Conteúdo das abas -->
                    <div id="tab-content">
                        <div id="usuarios-cadastro" class="tab-content" style="display:none;">
                            {$this->getFormularioCadastro($resultado)}
                        </div>
                        <div id="usuarios-documentos" class="tab-content active">
                            {$this->getListagemUsuarios($resultado)}
                        </div>
                        <div id="usuarios-edicao" class="tab-content" style="display:none;">
                            {$this->getFormularioEdicao($resultado)}
                        </div>
                    </div>
                </section>
                <!-- Modal de confirmação de desativação -->
                <div id="modal-exclusao" class="modal" style="display:none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Confirmar Desativação</h3>
                            <span class="close-modal" onclick="fecharModal()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <p>Tem certeza que deseja desativar este usuário?</p>
                            <p><strong>O usuário não terá mais acesso ao sistema.</strong></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                            <button class="btn-delete" id="confirmar-exclusao">Desativar</button>
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

        // Carrega perfis do banco
        $perfis = $this->usuario->listarPerfis();
        $opcoesPerfis = '<option value="">Selecionar</option>';
        foreach ($perfis as $perfil) {
            $selected = (isset($dadosForm['perfil_id']) && $dadosForm['perfil_id'] == $perfil['id']) ? 'selected' : '';
            $nomeExibicao = ucfirst(str_replace('_', ' ', $perfil['nome'])); // ex: fisioterapeuta -> Fisioterapeuta
            $opcoesPerfis .= '<option value="' . $perfil['id'] . '" ' . $selected . '>' . htmlspecialchars($nomeExibicao) . '</option>';
        }

        return '
        <div class="form-container">
            ' . $mensagens . '
            <form action="" method="POST">
                <input type="hidden" name="acao" value="cadastrar">
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF*</label>
                        <input required type="text" id="cpf" name="cpf" required maxlength="11" placeholder="Digite o CPF"
                               value="' . (isset($dadosForm['cpf']) ? htmlspecialchars($dadosForm['cpf']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="login">Login*</label>
                        <input required type="text" id="login" name="login" required maxlength="60" placeholder="Digite o login"
                               value="' . (isset($dadosForm['login']) ? htmlspecialchars($dadosForm['login']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="nm_usuario">Nome Completo*</label>
                        <input required type="text" id="nm_usuario" name="nm_usuario" required maxlength="45" placeholder="Digite o nome"
                               value="' . (isset($dadosForm['nm_usuario']) ? htmlspecialchars($dadosForm['nm_usuario']) : '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Senha*</label>
                        <input required type="password" id="senha" name="senha" required maxlength="30" placeholder="Digite a senha"
                               value="">
                    </div>
                    <div class="form-group">
                        <label for="perfil_id">Tipo de Usuário*</label>
                        <select required name="perfil_id">
                            ' . $opcoesPerfis . '
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-add">
                    <i class="fas fa-save"></i> Salvar Usuário
                </button>
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
        $tabelaUsuarios = '';

        if (!empty($usuarios)) {
            $tabelaUsuarios = '
            <div class="table-container">
                <table class="pacientes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>CPF</th>
                            <th>Login</th>
                            <th>Nome</th>
                            <th>Perfil</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($usuarios as $usuario) {
                $tabelaUsuarios .= '
                    <tr>
                        <td>' . htmlspecialchars($usuario['id']) . '</td>
                        <td>' . htmlspecialchars($usuario['cpf']) . '</td>
                        <td>' . htmlspecialchars($usuario['login']) . '</td>
                        <td>' . htmlspecialchars($usuario['nm_usuario']) . '</td>
                        <td>' . htmlspecialchars($usuario['perfil_nome'] ?? '—') . '</td>
                        <td>
                            <a href="atualizar_usuario.php?id=' . $usuario['id'] . '" class="btn-edit" title="Editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button type="button" class="btn-delete" onclick="confirmarDesativacao(' . $usuario['id'] . ')" title="Desativar">
                                <i class="fas fa-trash"></i> Desativar
                            </button>
                        </td>
                    </tr>';
            }

            $tabelaUsuarios .= '
                </tbody>
            </table>
        </div>';
        } else {
            $tabelaUsuarios = '<div class="no-data">Nenhum usuário ativo encontrado.</div>';
        }

        return '
        <div class="listagem-container">
            ' . $mensagens . '
            <div class="table-header">
                <h3>Listagem de Usuários (' . $total . ' ativo' . ($total != 1 ? 's' : '') . ')</h3>
            </div>
            ' . $tabelaUsuarios . '
        </div>';
    }

    private function getFormularioEdicao($resultado = null)
    {
        // Verifica se há ID na URL
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            return '<div class="form-message error">ID do usuário não informado.</div>';
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

        // Carrega perfis
        $perfis = $this->usuario->listarPerfis();
        $opcoesPerfis = '<option value="">Selecionar</option>';
        foreach ($perfis as $perfil) {
            $selected = (isset($dadosForm['perfil_id']) && $dadosForm['perfil_id'] == $perfil['id']) ? 'selected' : '';
            $nomeExibicao = ucfirst(str_replace('_', ' ', $perfil['nome']));
            $opcoesPerfis .= '<option value="' . $perfil['id'] . '" ' . $selected . '>' . htmlspecialchars($nomeExibicao) . '</option>';
        }

        return '
        <div class="form-container">
            ' . $mensagens . '
            <form action="" method="POST">
                <input type="hidden" name="acao" value="atualizar">
                <input type="hidden" name="id" value="' . $id . '">
                <div class="form-row">
                    <div class="form-group">
                        <label for="cpf">CPF*</label>
                        <input required type="text" id="cpf" name="cpf" required maxlength="11" placeholder="Digite o CPF"
                               value="' . (isset($dadosForm['cpf']) ? htmlspecialchars($dadosForm['cpf']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="login">Login*</label>
                        <input required type="text" id="login" name="login" required maxlength="60" placeholder="Digite o login"
                               value="' . (isset($dadosForm['login']) ? htmlspecialchars($dadosForm['login']) : '') . '">
                    </div>
                    <div class="form-group">
                        <label for="nm_usuario">Nome Completo*</label>
                        <input required type="text" id="nm_usuario" name="nm_usuario" required maxlength="45" placeholder="Digite o nome"
                               value="' . (isset($dadosForm['nm_usuario']) ? htmlspecialchars($dadosForm['nm_usuario']) : '') . '">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Nova Senha (opcional)</label>
                        <input type="password" id="senha" name="senha" maxlength="30" placeholder="Deixe em branco para manter a mesma">
                    </div>
                    <div class="form-group">
                        <label for="perfil_id">Tipo de Usuário*</label>
                        <select required name="perfil_id">
                            ' . $opcoesPerfis . '
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-add">
                    <i class="fas fa-save"></i> Atualizar Usuário
                </button>
            </form>
        </div>';
    }
}
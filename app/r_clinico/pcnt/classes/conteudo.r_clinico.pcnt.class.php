<?php
include_once "paciente.class.php";

class ConteudoRClinicoPCNT
{
    private $paciente;
    private $paciente_id;

    public function __construct($paciente_id = null)
    {
        $this->paciente = new Paciente();
        $this->paciente_id = $paciente_id;
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
        $temPermissaoPacientes = (
            $this->usuarioTemPermissao('pacientes.visualizar') ||
            $this->usuarioTemPermissao('pacientes.criar') ||
            $this->usuarioTemPermissao('pacientes.editar') ||
            $this->usuarioTemPermissao('pacientes.excluir')
        );

        if (!$temPermissaoPacientes) {
            return '<div class="form-message error">Você não tem permissão para acessar o módulo de Pacientes.</div>';
        }

        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);
        $perfil = htmlspecialchars($_SESSION['data_user']['perfil_nome'] ?? 'Usuário');
        $resultado = null;
        $abaAtiva = 'documentos';

        if ($_POST && isset($_POST['acao'])) {
            switch ($_POST['acao']) {
                case 'cadastrar':
                    $resultado = $this->paciente->cadastrar($_POST);
                    if ($resultado['sucesso']) {
                        $_SESSION['mensagem'] = ['texto' => $resultado['mensagem'], 'tipo' => 'sucesso'];
                        header("Location: ?sub=documentos");
                        exit;
                    } else {
                        $abaAtiva = 'cadastro';
                    }
                    break;
                case 'atualizar':
                    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                        $resultado = $this->paciente->atualizar($_POST['id'], $_POST);
                        if ($resultado['sucesso']) {
                            $_SESSION['mensagem'] = ['texto' => $resultado['mensagem'], 'tipo' => 'sucesso'];
                            header("Location: ?sub=documentos");
                            exit;
                        } else {
                            $abaAtiva = 'edicao';
                        }
                    }
                    break;
                case 'excluir':
                    $resultado = $this->paciente->excluir($_POST['id']);
                    if ($resultado['sucesso']) {
                        $_SESSION['mensagem'] = ['texto' => $resultado['mensagem'], 'tipo' => 'sucesso'];
                        header("Location: ?sub=documentos");
                        exit;
                    }
                    break;
                case 'excluir_multiplos':
                    $resultado = $this->excluirMultiplos($_POST['ids']);
                    if ($resultado['sucesso']) {
                        $_SESSION['mensagem'] = ['texto' => $resultado['mensagem'], 'tipo' => 'sucesso'];
                        header("Location: ?sub=documentos");
                        exit;
                    }
                    break;
            }
        }

        $pacienteBuscado = null;
        if (isset($_GET['id']) && is_numeric($_GET['id']) && (int)$_GET['id'] > 0) {
            $pacienteBuscado = $this->paciente->buscarPorId((int)$_GET['id']);
        }

        $html = <<<HTML
    <body>
    <header>
        <div class="logo"><img src="src/vivenciar_logov2.png" alt="Logo"></div>
        <nav>
            <ul>
                <li><a href="../../" title="Voltar ao Menu Principal"><i class="fas fa-home"></i></a></li>
                <li class="user-info">
                    <span class="user-icon"><i class="fas fa-user"></i></span>
                    <div class="user-details">
                        <span class="user-name">{$nome}</span>
                        <span class="user-role">{$perfil}</span>
                    </div>
                    <a href="?sair" class="btn-logout" title="Sair"><i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </nav>
    </header>
    <section class="simple-box">
        <h2>Registro Clínico</h2>
        <div class="tabs" id="main-tabs">
            <button class="tab-btn active" onclick="redirectToTab('pacientes')">Pacientes</button>
            <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
            <button class="tab-btn" onclick="redirectToTab('evolucoes')">Formulários</button>
        </div>
        <div id="sub-tabs">
            <div class="sub-tabs" id="sub-pacientes">
                <button class="tab-btn {$this->getActiveClass($abaAtiva, 'cadastro')}" 
                        data-main="pacientes" data-sub="cadastro" 
                        onclick="showSubTab('pacientes', 'cadastro', this)">Cadastrar Paciente</button>
                <button class="tab-btn {$this->getActiveClass($abaAtiva, 'documentos')}" 
                        data-main="pacientes" data-sub="documentos" 
                        onclick="showSubTab('pacientes', 'documentos', this)">Listagem</button>
HTML;

        if ($this->paciente_id) {
            $html .= '<button class="tab-btn" data-main="pacientes" data-sub="historico" onclick="showSubTab(\'pacientes\', \'historico\', this)">Histórico de Evoluções</button>';
        }

        $html .= <<<HTML
            </div>
        </div>
        <div id="tab-content">
            <div id="pacientes-cadastro" class="tab-content" style="{$this->getDisplayStyle($abaAtiva, 'cadastro')}">
                {$this->getFormularioCadastro($resultado)}
            </div>
            <div id="pacientes-documentos" class="tab-content" style="{$this->getDisplayStyle($abaAtiva, 'documentos')}">
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
            <div id="pacientes-edicao" class="tab-content" style="{$this->getDisplayStyle($abaAtiva, 'edicao')}">
                {$this->getFormularioEdicao($resultado)}
            </div>
        </div>
    </section>
    <div id="modal-exclusao" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header"><h3>Confirmar Exclusão</h3><span class="close-modal" onclick="fecharModal()">&times;</span></div>
            <div class="modal-body"><p>Tem certeza que deseja excluir este paciente?</p><p><strong>Esta ação não pode ser desfeita.</strong></p></div>
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

    private function getActiveClass($current, $target) { return $current === $target ? 'active' : ''; }
    private function getDisplayStyle($current, $target) { return $current === $target ? 'display:block;' : 'display:none;'; }

    // ========================================================================
    // FORMULÁRIO DE CADASTRO - ATUALIZADO COM SISREG E CONVÊNIO
    // ========================================================================
    private function getFormularioCadastro($resultado = null)
    {
        if (!$this->usuarioTemPermissao('pacientes.criar')) {
            return '<div class="form-message error">Você não tem permissão para cadastrar pacientes.</div>';
        }

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
                foreach ($resultado['erros'] as $erro) { $mensagens .= '<p>' . htmlspecialchars($erro) . '</p>'; }
                $mensagens .= '</div>';
            }
        }

        return '
        <div class="form-container">' . $mensagens . '
        <form action="" method="POST">
            <input type="hidden" name="acao" value="cadastrar">
            
            <!-- Dados Pessoais -->
            <div class="form-row">
                <div class="form-group">
                    <label for="nome" class="required">Nome Completo</label>
                    <input required type="text" id="nome" name="nome" maxlength="100" placeholder="Digite o nome completo"
                           value="' . (isset($dadosForm['nome']) ? htmlspecialchars($dadosForm['nome']) : '') . '"
                           oninput="this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, \'\');">
                </div>
                <div class="form-group">
                    <label for="convenio" class="required">Convênio*</label>
                    <select required id="convenio" name="convenio" onchange="toggleCNSField()">
                        <option value="">Selecionar</option>
                        <option value="SUS" ' . (isset($dadosForm['convenio']) && $dadosForm['convenio'] == 'SUS' ? 'selected' : '') . '>SUS</option>
                        <option value="CONVENIO" ' . (isset($dadosForm['convenio']) && $dadosForm['convenio'] == 'CONVENIO' ? 'selected' : '') . '>CONVÊNIO</option>
                        <option value="UNIMED" ' . (isset($dadosForm['convenio']) && $dadosForm['convenio'] == 'UNIMED' ? 'selected' : '') . '>UNIMED</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cns">CNS <small>(obrigatório se SUS)</small></label>
                    <input type="text" id="cns" name="cns" maxlength="15" placeholder="Digite o CNS"
                           value="' . (isset($dadosForm['cns']) ? htmlspecialchars($dadosForm['cns']) : '') . '"
                           inputmode="numeric" pattern="[0-9]*"
                           oninput="this.value = this.value.replace(/[^0-9]/g, \'\');">
                </div>
                <div class="form-group">
                    <label for="num_autorizacao_sisreg">Nº Autorização SISREG</label>
                    <input type="text" id="num_autorizacao_sisreg" name="num_autorizacao_sisreg" maxlength="20" 
                           placeholder="Ex: SISREG2026041234567890"
                           value="' . (isset($dadosForm['num_autorizacao_sisreg']) ? htmlspecialchars($dadosForm['num_autorizacao_sisreg']) : '') . '"
                           oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, \'\').substring(0,20);">
                    <small style="color:#666;">Apenas letras e números, máximo 20 caracteres</small>
                </div>
                <div class="form-group">
                    <label for="data_nascimento" class="required">Data Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" required
                           value="' . (isset($dadosForm['data_nascimento']) ? htmlspecialchars($dadosForm['data_nascimento']) : '') . '">
                </div>
            </div>
            
            <!-- Demográficos -->
            <div class="form-row">
                <div class="form-group">
                    <label for="raca_cor" class="required">Raça/Cor</label>
                    <select required id="raca_cor" name="raca_cor">
                        <option value="">Selecionar</option>
                        <option value="01" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '01' ? 'selected' : '') . '>Branca</option>
                        <option value="02" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '02' ? 'selected' : '') . '>Preta</option>
                        <option value="03" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '03' ? 'selected' : '') . '>Parda</option>
                        <option value="04" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '04' ? 'selected' : '') . '>Amarela</option>
                        <option value="05" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '05' ? 'selected' : '') . '>Indígena</option>
                        <option value="99" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '99' ? 'selected' : '') . '>Sem informação</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sexo" class="required">Sexo</label>
                    <select required id="sexo" name="sexo">
                        <option value="">Selecionar</option>
                        <option value="M" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'M' ? 'selected' : '') . '>Masculino</option>
                        <option value="F" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'F' ? 'selected' : '') . '>Feminino</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="etnia">Etnia</label>
                    <input type="text" id="etnia" name="etnia" maxlength="4" placeholder="Ex: Tupi"
                           value="' . (isset($dadosForm['etnia']) ? htmlspecialchars($dadosForm['etnia']) : '') . '">
                </div>
                <div class="form-group">
                    <label for="nacionalidade" class="required">Nacionalidade</label>
                    <select required id="nacionalidade" name="nacionalidade">
                        <option value="">Selecionar</option>
                        <option value="10" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '10' ? 'selected' : '') . '>Brasileira</option>
                        <option value="20" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '20' ? 'selected' : '') . '>Naturalizado</option>
                        <option value="30" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '30' ? 'selected' : '') . '>Estrangeiro</option>
                    </select>
                </div>
            </div>
            
            <!-- Endereço -->
            <div class="form-row">
                <div class="form-group">
                    <label for="codigo_logradouro" class="required">Tipo Logradouro</label>
                    <select required id="codigo_logradouro" name="codigo_logradouro">
                        <option value="">Selecionar</option>
                        <option value="81" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '81' ? 'selected' : '') . '>Rua</option>
                        <option value="8" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '8' ? 'selected' : '') . '>Avenida</option>
                    </select>
                </div>
                <div class="form-group" style="flex:2;">
                    <label for="endereco" class="required">Logradouro</label>
                    <input required type="text" id="endereco" name="endereco" maxlength="100" placeholder="Digite o logradouro"
                           value="' . (isset($dadosForm['endereco']) ? htmlspecialchars($dadosForm['endereco']) : '') . '">
                </div>
                <div class="form-group">
                    <label for="numero" class="required">Número</label>
                    <input required type="text" id="numero" name="numero" maxlength="10" placeholder="Nº"
                           value="' . (isset($dadosForm['numero']) ? htmlspecialchars($dadosForm['numero']) : '') . '" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="complemento">Complemento</label>
                    <input type="text" id="complemento" name="complemento" maxlength="30" placeholder="Apto, Bloco"
                           value="' . (isset($dadosForm['complemento']) ? htmlspecialchars($dadosForm['complemento']) : '') . '">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="bairro" class="required">Bairro</label>
                    <input required type="text" id="bairro" name="bairro" maxlength="60" placeholder="Informe o bairro"
                           value="' . (isset($dadosForm['bairro']) ? htmlspecialchars($dadosForm['bairro']) : '') . '">
                </div>
                <div class="form-group">
                    <label for="cep" class="required">CEP</label>
                    <input required type="text" id="cep" name="cep" maxlength="9" placeholder="00000-000"
                           value="' . (isset($dadosForm['cep']) ? htmlspecialchars($dadosForm['cep']) : '') . '" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="telefone" class="required">Telefone</label>
                    <input required type="text" id="telefone" name="telefone" maxlength="15" placeholder="(00) 00000-0000"
                           value="' . (isset($dadosForm['telefone']) ? htmlspecialchars($dadosForm['telefone']) : '') . '" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com"
                           value="' . (isset($dadosForm['email']) ? htmlspecialchars($dadosForm['email']) : '') . '">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="situacao_rua" class="required">Situação de Rua?</label>
                    <select required id="situacao_rua" name="situacao_rua">
                        <option value="N" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'N' ? 'selected' : '') . '>Não</option>
                        <option value="S" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'S' ? 'selected' : '') . '>Sim</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn-add"><i class="fas fa-save"></i> Salvar Paciente</button>
        </form>
        </div>
        
        <script>
        // Mostra/oculta campo CNS conforme convênio selecionado
        function toggleCNSField() {
            const convenio = document.getElementById("convenio").value;
            const cnsField = document.getElementById("cns");
            if (convenio === "SUS") {
                cnsField.required = true;
                cnsField.placeholder = "Digite o CNS (obrigatório)";
                cnsField.style.borderColor = "#007bff";
            } else {
                cnsField.required = false;
                cnsField.placeholder = "Digite o CNS (opcional)";
                cnsField.style.borderColor = "#ccc";
            }
        }
        // Inicializa ao carregar
        document.addEventListener("DOMContentLoaded", toggleCNSField);
        </script>';
    }

    // ========================================================================
    // LISTAGEM DE PACIENTES - ATUALIZADA COM NOVAS COLUNAS
    // ========================================================================
    private function getListagemPacientes($resultado = null, $pacienteBuscado = null)
    {
        $mensagens = '';
        if (isset($_SESSION['mensagem'])) {
            $mensagens = '<div class="form-message ' . ($_SESSION['mensagem']['tipo'] === 'erro' ? 'error' : 'success') . '">' .
                         htmlspecialchars($_SESSION['mensagem']['texto']) . '</div>';
            unset($_SESSION['mensagem']);
        }
        if ($resultado && isset($_POST['acao']) && ($_POST['acao'] == 'excluir' || $_POST['acao'] == 'excluir_multiplos')) {
            if (isset($resultado['sucesso']) && $resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . $resultado['mensagem'] . '</div>';
            } elseif (isset($resultado['erros'])) {
                $mensagens = '<div class="form-message error">';
                foreach ($resultado['erros'] as $erro) { $mensagens .= '<p>' . htmlspecialchars($erro) . '</p>'; }
                $mensagens .= '</div>';
            }
        }

        $termoBusca = $_GET['busca'] ?? '';
        $formularioBusca = '';
        $tabelaPacientes = '';
        $controls = '';

        if ($pacienteBuscado) {
            $dataNasc = date('d/m/Y', strtotime($pacienteBuscado['data_nascimento']));
            $tabelaPacientes = '
            <div class="table-container">
                <div class="paciente-detalhe">
                    <h3 id="titulo-paciente">Detalhes do Paciente</h3>
                    <div class="paciente-info">
                        <p><strong>ID:</strong> ' . htmlspecialchars($pacienteBuscado['id']) . '</p>
                        <p><strong>Nome:</strong> ' . htmlspecialchars($pacienteBuscado['nome']) . '</p>
                        <p><strong>Convênio:</strong> ' . htmlspecialchars($pacienteBuscado['convenio'] ?? '-') . '</p>
                        <p><strong>CNS:</strong> ' . (!empty($pacienteBuscado['cns']) ? htmlspecialchars($pacienteBuscado['cns']) : '-') . '</p>
                        <p><strong>SISREG:</strong> ' . (!empty($pacienteBuscado['num_autorizacao_sisreg']) ? '<span style="color:#28a745;font-weight:500;">' . htmlspecialchars($pacienteBuscado['num_autorizacao_sisreg']) . '</span>' : '<span style="color:#dc3545;font-style:italic;">Não cadastrado</span>') . '</p>
                        <p><strong>Data Nascimento:</strong> ' . $dataNasc . '</p>
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
                    <div class="paciente-actions">';
            if ($this->usuarioTemPermissao('pacientes.editar')) {
                $tabelaPacientes .= '<button class="btn-edit" onclick="editarPaciente(' . $pacienteBuscado['id'] . ')"><i class="fas fa-edit"></i> Editar</button>';
            }
            if ($this->usuarioTemPermissao('pacientes.excluir')) {
                $tabelaPacientes .= '<button class="btn-delete" onclick="confirmarExclusao(' . $pacienteBuscado['id'] . ')"><i class="fas fa-trash"></i> Excluir</button>';
            }
            if ($this->usuarioTemPermissao('evolucoes.fisio.criar') || $this->usuarioTemPermissao('evolucoes.fono.criar') || $this->usuarioTemPermissao('evolucoes.teoc.criar')) {
                $tabelaPacientes .= '<button class="btn-evolucao" onclick="abrirEvolucao(' . $pacienteBuscado['id'] . ')"><i class="fas fa-file-medical"></i> Evolução</button>';
            }
            $tabelaPacientes .= '<a href="?sub=documentos" class="btn-clear"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div></div></div>';
        } else {
            $pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            if ($pagina < 1) $pagina = 1;
            $porPagina = 10;
            $ordenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'nome';
            $direcao = isset($_GET['direcao']) ? $_GET['direcao'] : 'ASC';
            
            $pacientes = $this->paciente->buscarPorTermoPaginado($termoBusca, $pagina, $porPagina, $ordenar, $direcao);
            $totalPacientes = $this->paciente->getTotalPacientes($termoBusca);
            $totalPaginas = ceil($totalPacientes / $porPagina);

            $formularioBusca = '
            <div class="search-bar">
                <form method="GET" class="search-form">
                    <input type="hidden" name="sub" value="documentos">
                    <input type="text" name="busca" placeholder="Buscar por nome, CNS ou SISREG"
                           value="' . htmlspecialchars($termoBusca) . '" maxlength="100">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i> Buscar</button>
                    <a href="?sub=documentos" class="btn-clear">Limpar Busca</a>
                </form>
            </div>
            <div class="sort-controls">
                <form method="GET" class="sort-form">
                    <input type="hidden" name="sub" value="documentos">
                    ' . (!empty($termoBusca) ? '<input type="hidden" name="busca" value="' . htmlspecialchars($termoBusca) . '">' : '') . '
                    <label for="ordenar">Ordenar por:</label>
                    <select name="ordenar" id="ordenar" onchange="this.form.submit()">
                        <option value="nome" ' . ($ordenar === 'nome' ? 'selected' : '') . '>Nome (A-Z)</option>
                        <option value="data_cadastro" ' . ($ordenar === 'data_cadastro' ? 'selected' : '') . '>Data Cadastro</option>
                        <option value="cns" ' . ($ordenar === 'cns' ? 'selected' : '') . '>CNS</option>
                    </select>
                    <label for="direcao">Ordem:</label>
                    <select name="direcao" id="direcao" onchange="this.form.submit()">
                        <option value="ASC" ' . ($direcao === 'ASC' ? 'selected' : '') . '>Crescente ↑</option>
                        <option value="DESC" ' . ($direcao === 'DESC' ? 'selected' : '') . '>Decrescente ↓</option>
                    </select>
                </form>
            </div>';

            if (!empty($pacientes)) {
                $tabelaPacientes = '
                <div class="table-container">
                    <table class="pacientes-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Convênio</th>
                                <th>CNS</th>
                                <th>SISREG</th>
                                <th>Telefone</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>';
                foreach ($pacientes as $paciente) {
                    $sisregDisplay = !empty($paciente['num_autorizacao_sisreg']) 
                        ? '<span style="color:#28a745;font-weight:500;" title="' . htmlspecialchars($paciente['num_autorizacao_sisreg']) . '">✅ Registrado</span>' 
                        : '<span style="color:#dc3545;font-style:italic;" title="Autorização SISREG não cadastrada">⚠ Pendente</span>';
                    
                    $tabelaPacientes .= '
                    <tr>
                        <td>' . htmlspecialchars($paciente['nome']) . '</td>
                        <td>' . htmlspecialchars($paciente['convenio'] ?? '-') . '</td>
                        <td>' . (!empty($paciente['cns']) ? htmlspecialchars($paciente['cns']) : '-') . '</td>
                        <td>' . $sisregDisplay . '</td>
                        <td>' . (!empty($paciente['telefone']) ? htmlspecialchars($paciente['telefone']) : '-') . '</td>
                        <td>
                            <div class="table-actions">
                                <a href="?id=' . $paciente['id'] . '&sub=documentos" class="btn-action btn-view"><i class="fas fa-eye"></i> Detalhes</a>
                            </div>
                        </td>
                    </tr>';
                }
                $tabelaPacientes .= '</tbody></table></div>';

                if ($totalPaginas > 1) {
                    $controls = '<div class="pagination-controls">';
                    $paramsUrl = (!empty($termoBusca) ? '&busca=' . urlencode($termoBusca) : '') . '&ordenar=' . urlencode($ordenar) . '&direcao=' . urlencode($direcao);
                    if ($pagina > 1) {
                        $controls .= '<a href="?sub=documentos' . $paramsUrl . '&pagina=1" class="btn-pagination">&laquo;&laquo; Primeiro</a>';
                        $controls .= '<a href="?sub=documentos' . $paramsUrl . '&pagina=' . ($pagina - 1) . '" class="btn-pagination">&laquo; Anterior</a>';
                    }
                    for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++) {
                        $controls .= ($i == $pagina) 
                            ? '<span class="pagination-current">' . $i . '</span>' 
                            : '<a href="?sub=documentos' . $paramsUrl . '&pagina=' . $i . '" class="btn-pagination">' . $i . '</a>';
                    }
                    if ($pagina < $totalPaginas) {
                        $controls .= '<a href="?sub=documentos' . $paramsUrl . '&pagina=' . ($pagina + 1) . '" class="btn-pagination">Próximo &raquo;</a>';
                        $controls .= '<a href="?sub=documentos' . $paramsUrl . '&pagina=' . $totalPaginas . '" class="btn-pagination">Último &raquo;&raquo;</a>';
                    }
                    $controls .= '</div>';
                }
            } else {
                $tabelaPacientes = '<div class="no-data">Nenhum paciente encontrado.</div>';
            }
        }

        $total = $pacienteBuscado ? 1 : $totalPacientes ?? 0;
        return '
        <div class="listagem-container">' . $mensagens . $formularioBusca . '
        <div class="table-header">
            <h3>' . ($pacienteBuscado ? 'Detalhes do Paciente' : 'Listagem de Pacientes (' . $total . ' cadastrado' . ($total != 1 ? 's' : '') . ')') . '</h3>
        </div>
        ' . $tabelaPacientes . $controls . '
        </div>';
    }

    // ========================================================================
    // FORMULÁRIO DE EDIÇÃO - ATUALIZADO COM SISREG E CONVÊNIO
    // ========================================================================
    private function getFormularioEdicao($resultado = null)
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) return '<div class="form-message error">ID do paciente não informado.</div>';
        if (!$this->usuarioTemPermissao('pacientes.editar')) return '<div class="form-message error">Você não tem permissão para editar pacientes.</div>';
        
        $paciente = $this->paciente->buscarPorId($id);
        if (!$paciente) return '<div class="form-message error">Paciente não encontrado.</div>';

        $dadosForm = $resultado && isset($resultado['dados']) ? $resultado['dados'] : $paciente;
        $mensagens = '';
        if ($resultado && (!isset($_POST['acao']) || $_POST['acao'] == 'atualizar')) {
            if (isset($resultado['sucesso']) && $resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . $resultado['mensagem'] . '</div>';
            } elseif (isset($resultado['erros'])) {
                $mensagens = '<div class="form-message error">';
                foreach ($resultado['erros'] as $erro) { $mensagens .= '<p>' . htmlspecialchars($erro) . '</p>'; }
                $mensagens .= '</div>';
            }
        }

        return '
        <div class="form-container">' . $mensagens . '
        <form action="" method="POST">
            <input type="hidden" name="acao" value="atualizar">
            <input type="hidden" name="id" value="' . $id . '">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nome" class="required">Nome Completo</label>
                    <input required type="text" id="nome" name="nome" maxlength="100" placeholder="Digite o nome completo"
                           value="' . (isset($dadosForm['nome']) ? htmlspecialchars($dadosForm['nome']) : '') . '">
                </div>
                <div class="form-group">
                    <label for="convenio" class="required">Convênio*</label>
                    <select required id="convenio" name="convenio" onchange="toggleCNSField()">
                        <option value="">Selecionar</option>
                        <option value="SUS" ' . (isset($dadosForm['convenio']) && $dadosForm['convenio'] == 'SUS' ? 'selected' : '') . '>SUS</option>
                        <option value="CONVENIO" ' . (isset($dadosForm['convenio']) && $dadosForm['convenio'] == 'CONVENIO' ? 'selected' : '') . '>CONVÊNIO</option>
                        <option value="UNIMED" ' . (isset($dadosForm['convenio']) && $dadosForm['convenio'] == 'UNIMED' ? 'selected' : '') . '>UNIMED</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cns">CNS <small>(obrigatório se SUS)</small></label>
                    <input type="text" id="cns" name="cns" maxlength="15" placeholder="Digite o CNS"
                           value="' . (isset($dadosForm['cns']) ? htmlspecialchars($dadosForm['cns']) : '') . '" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="num_autorizacao_sisreg">Nº Autorização SISREG</label>
                    <input type="text" id="num_autorizacao_sisreg" name="num_autorizacao_sisreg" maxlength="20" 
                           placeholder="Ex: SISREG2026041234567890"
                           value="' . (isset($dadosForm['num_autorizacao_sisreg']) ? htmlspecialchars($dadosForm['num_autorizacao_sisreg']) : '') . '"
                           oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, \'\').substring(0,20);">
                    <small style="color:#666;">Apenas letras e números, máximo 20 caracteres</small>
                </div>
                <div class="form-group">
                    <label for="data_nascimento" class="required">Data Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" required
                           value="' . (isset($dadosForm['data_nascimento']) ? htmlspecialchars($dadosForm['data_nascimento']) : '') . '">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="raca_cor" class="required">Raça/Cor</label>
                    <select required id="raca_cor" name="raca_cor">
                        <option value="">Selecionar</option>
                        <option value="01" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '01' ? 'selected' : '') . '>Branca</option>
                        <option value="02" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '02' ? 'selected' : '') . '>Preta</option>
                        <option value="03" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '03' ? 'selected' : '') . '>Parda</option>
                        <option value="04" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '04' ? 'selected' : '') . '>Amarela</option>
                        <option value="05" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '05' ? 'selected' : '') . '>Indígena</option>
                        <option value="99" ' . (isset($dadosForm['raca_cor']) && $dadosForm['raca_cor'] == '99' ? 'selected' : '') . '>Sem informação</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sexo" class="required">Sexo</label>
                    <select required id="sexo" name="sexo">
                        <option value="">Selecionar</option>
                        <option value="M" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'M' ? 'selected' : '') . '>Masculino</option>
                        <option value="F" ' . (isset($dadosForm['sexo']) && $dadosForm['sexo'] == 'F' ? 'selected' : '') . '>Feminino</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="etnia">Etnia</label>
                    <input type="text" id="etnia" name="etnia" maxlength="4" placeholder="Ex: Tupi"
                           value="' . (isset($dadosForm['etnia']) ? htmlspecialchars($dadosForm['etnia']) : '') . '">
                </div>
                <div class="form-group">
                    <label for="nacionalidade" class="required">Nacionalidade</label>
                    <select required id="nacionalidade" name="nacionalidade">
                        <option value="">Selecionar</option>
                        <option value="10" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '10' ? 'selected' : '') . '>Brasileira</option>
                        <option value="20" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '20' ? 'selected' : '') . '>Naturalizado</option>
                        <option value="30" ' . (isset($dadosForm['nacionalidade']) && $dadosForm['nacionalidade'] == '30' ? 'selected' : '') . '>Estrangeiro</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="codigo_logradouro" class="required">Tipo Logradouro</label>
                    <select required id="codigo_logradouro" name="codigo_logradouro">
                        <option value="">Selecionar</option>
                        <option value="81" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '81' ? 'selected' : '') . '>Rua</option>
                        <option value="8" ' . (isset($dadosForm['codigo_logradouro']) && $dadosForm['codigo_logradouro'] == '8' ? 'selected' : '') . '>Avenida</option>
                    </select>
                </div>
                <div class="form-group" style="flex:2;">
                    <label for="endereco" class="required">Logradouro</label>
                    <input required type="text" id="endereco" name="endereco" maxlength="100" placeholder="Digite o logradouro"
                           value="' . (isset($dadosForm['endereco']) ? htmlspecialchars($dadosForm['endereco']) : '') . '">
                </div>
                <div class="form-group">
                    <label for="numero" class="required">Número</label>
                    <input required type="text" id="numero" name="numero" maxlength="10" placeholder="Nº"
                           value="' . (isset($dadosForm['numero']) ? htmlspecialchars($dadosForm['numero']) : '') . '" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="complemento">Complemento</label>
                    <input type="text" id="complemento" name="complemento" maxlength="30" placeholder="Apto, Bloco"
                           value="' . (isset($dadosForm['complemento']) ? htmlspecialchars($dadosForm['complemento']) : '') . '">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="bairro" class="required">Bairro</label>
                    <input required type="text" id="bairro" name="bairro" maxlength="60" placeholder="Informe o bairro"
                           value="' . (isset($dadosForm['bairro']) ? htmlspecialchars($dadosForm['bairro']) : '') . '">
                </div>
                <div class="form-group">
                    <label for="cep" class="required">CEP</label>
                    <input required type="text" id="cep" name="cep" maxlength="9" placeholder="00000-000"
                           value="' . (isset($dadosForm['cep']) ? htmlspecialchars($dadosForm['cep']) : '') . '" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="telefone" class="required">Telefone</label>
                    <input required type="text" id="telefone" name="telefone" maxlength="15" placeholder="(00) 00000-0000"
                           value="' . (isset($dadosForm['telefone']) ? htmlspecialchars($dadosForm['telefone']) : '') . '" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com"
                           value="' . (isset($dadosForm['email']) ? htmlspecialchars($dadosForm['email']) : '') . '">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="situacao_rua" class="required">Situação de Rua?</label>
                    <select required id="situacao_rua" name="situacao_rua">
                        <option value="N" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'N' ? 'selected' : '') . '>Não</option>
                        <option value="S" ' . (isset($dadosForm['situacao_rua']) && $dadosForm['situacao_rua'] == 'S' ? 'selected' : '') . '>Sim</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn-add"><i class="fas fa-save"></i> Atualizar Paciente</button>
            <a href="?sub=documentos" class="btn-clear" style="display:inline-block; margin-left:10px;"><i class="fas fa-arrow-left"></i> Voltar para Listagem</a>
        </form>
        </div>
        
        <script>
        function toggleCNSField() {
            const convenio = document.getElementById("convenio").value;
            const cnsField = document.getElementById("cns");
            if (convenio === "SUS") {
                cnsField.required = true;
                cnsField.placeholder = "Digite o CNS (obrigatório)";
                cnsField.style.borderColor = "#007bff";
            } else {
                cnsField.required = false;
                cnsField.placeholder = "Digite o CNS (opcional)";
                cnsField.style.borderColor = "#ccc";
            }
        }
        document.addEventListener("DOMContentLoaded", toggleCNSField);
        </script>';
    }

    // ========================================================================
    // MÉTODOS AUXILIARES
    // ========================================================================
    private function getDescricaoRacaCor($codigo) {
        $racas = ['01'=>'Branca','02'=>'Preta','03'=>'Parda','04'=>'Amarela','05'=>'Indígena','99'=>'Sem informação'];
        return isset($racas[$codigo]) ? $racas[$codigo] : $codigo;
    }

    private function getDescricaoNacionalidade($codigo) {
        $nacionalidades = ['10'=>'Brasileira','20'=>'Naturalizado','30'=>'Estrangeiro'];
        return isset($nacionalidades[$codigo]) ? $nacionalidades[$codigo] : $codigo;
    }

    private function excluirMultiplos($ids) {
        if (empty($ids) || !is_array($ids)) return ['sucesso'=>false,'erros'=>['Nenhum paciente selecionado.']];
        $sucessos = 0; $erros = [];
        foreach ($ids as $id) {
            $resultado = $this->paciente->excluir($id);
            if ($resultado['sucesso']) { $sucessos++; } else { $erros[] = "Erro ao excluir ID $id: " . implode(', ', $resultado['erros']); }
        }
        return empty($erros) 
            ? ['sucesso'=>true,'mensagem'=>"$sucessos paciente(s) excluído(s) com sucesso!"] 
            : ['sucesso'=>$sucessos>0,'mensagem'=>"$sucessos paciente(s) excluído(s).",'erros'=>$erros];
    }

    private function getHistoricoEvolucoesPorPaciente($pacienteId) {
        if (!$this->usuarioTemPermissao('evolucoes.visualizar')) return '<div class="form-message error">Você não tem permissão para visualizar evoluções.</div>';
        if (!$pacienteId || $pacienteId <= 0) return '<div class="form-message error">Paciente não especificado.</div>';
        
        $evolucoes = $this->paciente->listarEvolucoesDetalhadas($pacienteId);
        if (empty($evolucoes)) return '<div class="no-data">Nenhuma evolução registrada para este paciente.</div>';
        
        $html = '<div class="table-container"><table class="pacientes-table"><thead><tr><th>Data/Hora</th><th>Formulário</th><th>Especialidade</th><th>Registrado por</th><th>Ações</th></tr></thead><tbody>';
        foreach ($evolucoes as $ev) {
            $dataFormatada = date('d/m/Y H:i', strtotime($ev['created_at']));
            $html .= '<tr><td>'.htmlspecialchars($dataFormatada).'</td><td>'.htmlspecialchars($ev['nome_formulario']).'</td><td>'.htmlspecialchars($ev['especialidade']).'</td><td>'.(!empty($ev['criado_por']) ? htmlspecialchars($ev['criado_por']) : '-').'</td><td><a href="visualizar_evolucao.php?id='.$ev['id'].'" class="btn-view" title="Visualizar"><i class="fas fa-eye"></i> Ver</a></td></tr>';
        }
        $html .= '</tbody></table></div><div style="margin-top:15px;"><a href="?id='.$pacienteId.'&sub=documentos" class="btn-clear"><i class="fas fa-arrow-left"></i> Voltar aos Dados do Paciente</a></div>';
        return $html;
    }
}
?>
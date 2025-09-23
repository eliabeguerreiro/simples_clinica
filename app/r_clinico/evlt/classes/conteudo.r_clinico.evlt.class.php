<?php
include_once "../../../classes/db.class.php";
include_once "Evolucao.class.php";

class ConteudoEvolucoesEVLT
{
    private $evolucao;
    
    public function __construct()
    {
        $this->evolucao = new Evolucao();
    }
    
    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Evoluções - Registro Clínico</title>
                <link rel="stylesheet" href="./src/style.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css  ">
            </head>
        HTML;

        // Renderiza o corpo da página
        $body = $this->renderBody();

        $html .= $body;

        $html .= <<<HTML
            <script src="./src/script.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js  "></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js  "></script>
        </body>
        </html>
        HTML;

        return $html;
    }
    
private function renderBody()
{
    $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);
    
    // Processa formulário se foi enviado
    $resultado = null;
    if ($_POST && isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'cadastrar':
                $resultado = $this->evolucao->cadastrar($_POST);
                break;
            case 'atualizar':
                $resultado = $this->evolucao->atualizar($_POST['id'], $_POST);
                break;
            case 'excluir':
                $resultado = $this->evolucao->excluir($_POST['id']);
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
                <h2>Registro Clínico - Evoluções</h2>
                
                <!-- Abas principais de navegação entre módulos -->
                <div class="tabs" id="main-tabs">
                    <button class="tab-btn" onclick="redirectToTab('pacientes')">Pacientes</button>
                    <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
                    <button class="tab-btn active" onclick="redirectToTab('evolucoes')">Evoluções</button>
                </div>
                
                <!-- Sub-abas do módulo atual -->
                <div id="sub-tabs">
                    <div class="sub-tabs" id="sub-evolucoes">
                        <button class="tab-btn active" data-main="evolucoes" data-sub="nova" onclick="showSubTab('evolucoes', 'nova', this)">Nova</button>
                        <button class="tab-btn" data-main="evolucoes" data-sub="listar" onclick="showSubTab('evolucoes', 'listar', this)">Formulários</button>
                        <button class="tab-btn" data-main="evolucoes" data-sub="graficos" onclick="showSubTab('evolucoes', 'graficos', this)">Gráficos</button>
                    </div>
                </div>
                
                <!-- Conteúdo das abas -->
                <div id="tab-content">
                    <div id="evolucoes-nova" class="tab-content active">
                        {$this->getFormularioCadastro($resultado)}
                    </div>
                    <div id="evolucoes-listar" class="tab-content" style="display:none;">
                        {$this->getFormulariosEvolucoes($resultado)}
                    </div>
                    <div id="evolucoes-graficos" class="tab-content" style="display:none;">
                        <p>Conteúdo Gráficos de Evoluções.</p>
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
                        <p>Tem certeza que deseja excluir esta evolução?</p>
                        <p><strong>Esta ação não pode ser desfeita.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                        <button class="btn-delete" id="confirmar-exclusao">Excluir</button>
                    </div>
                </div>
            </div>

            <script src="./src/script.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js  "></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js  "></script>
        </body>
    HTML;

    return $html;
}

private function getFormulariosEvolucoes($resultado = null)
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
    
    return '
    <div class="listagem-container">
        ' . $mensagens . '
        
        <div class="table-header">
            <h3>Formulários de Evolução</h3>
            <p>Gerencie e utilize formulários padronizados para evoluções</p>
        </div>
        
        <div class="formularios-grid">
            <div class="formulario-card">
                <div class="card-header">
                    <i class="fas fa-plus-circle"></i>
                    <h4>Criar Novo Formulário</h4>
                </div>
                <div class="card-body">
                    <p>Crie formulários personalizados para diferentes áreas de atendimento</p>
                </div>
                <div class="card-footer">
                    <button class="btn-add" onclick="acessarFormularios()">
                        <i class="fas fa-plus"></i> Criar Formulário
                    </button>
                </div>
            </div>
            
            <div class="formulario-card">
                <div class="card-header">
                    <i class="fas fa-list"></i>
                    <h4>Formulários Existentes</h4>
                </div>
                <div class="card-body">
                    <p>Gerencie formulários já criados e seus campos</p>
                </div>
               <div class="card-footer">
                    <button class="btn-edit" onclick="acessarGerenciamentoFormularios()">
                        <i class="fas fa-edit"></i> Gerenciar
                    </button>
                </div>
            </div>
            
            <div class="formulario-card">
                <div class="card-header">
                    <i class="fas fa-file-medical"></i>
                    <h4>Aplicar Formulário</h4>
                </div>
                <div class="card-body">
                    <p>Utilize formulários padronizados para registrar evoluções</p>
                </div>
                <div class="card-footer">
                    <button class="btn-evolucao" onclick="acessarAplicacaoFormulario()">
                        <i class="fas fa-play"></i> Aplicar
                    </button>
                </div>
            </div>
        </div>

    </div>';
}
    
private function getFormularioCadastro($resultado = null)
{
    // Verifica se já temos paciente e procedimento selecionados
    $pacienteId = isset($_GET['paciente']) ? (int)$_GET['paciente'] : null;
    $procedimentoId = isset($_GET['procedimento']) ? (int)$_GET['procedimento'] : null;
    $procedimentoGenerico = isset($_GET['generico']) ? true : false;
    
    // Se já temos paciente selecionado, mostra a Fase 2
    if ($pacienteId && ($procedimentoId || $procedimentoGenerico)) {
        return $this->getFormularioEvolucao($pacienteId, $procedimentoId, $procedimentoGenerico, $resultado);
    }
    
    // Caso contrário, mostra a Fase 1 (seleção)
    return $this->getFormularioSelecao($resultado);
}


private function getFormularioSelecao($resultado = null)
{
    // Inicializa $dadosForm como array vazio
    $dadosForm = [];
    
    // Mantém os dados no formulário em caso de erro
    if ($resultado && isset($resultado['dados'])) {
        $dadosForm = $resultado['dados'];
    } elseif (isset($_POST) && (!isset($_POST['acao']) || $_POST['acao'] == 'selecionar')) {
        $dadosForm = $_POST;
    }
    
    // Busca dados para dropdowns
    $pacientes = $this->evolucao->listarPacientes();
    
    // Exibe mensagens de sucesso/erro
    $mensagens = '';
    if ($resultado && (!isset($_POST['acao']) || $_POST['acao'] == 'selecionar')) {
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

    // Preenche dropdowns de pacientes
    $optionsPacientes = '<option value="">Selecione um paciente</option>';
    foreach ($pacientes as $paciente) {
        $selected = (isset($dadosForm['paciente_id']) && $dadosForm['paciente_id'] == $paciente['id']) ? 'selected' : '';
        $optionsPacientes .= '<option value="' . $paciente['id'] . '" ' . $selected . '>' . htmlspecialchars($paciente['nome']) . ' - CNS: ' . htmlspecialchars($paciente['cns']) . '</option>';
    }

    return '
    <div class="form-container">
        ' . $mensagens . '
        
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Seleção de Paciente</h3>
            <form action="" method="GET" id="form-selecao-paciente">
                <input type="hidden" name="sub" value="nova">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="paciente_id">Paciente*</label>
                        <select required id="paciente_id" name="paciente_id" onchange="buscarHistoricoPaciente(this.value)">
                            ' . $optionsPacientes . '
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-add" id="btn-continuar-paciente" disabled>
                        <i class="fas fa-arrow-right"></i> Continuar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Histórico do Paciente -->
        <div class="form-section" id="historico-paciente" style="display: none;">
            <h3><i class="fas fa-history"></i> Histórico do Paciente</h3>
            <div id="conteudo-historico">
                <p>Selecione um paciente para ver o histórico...</p>
            </div>
        </div>
        
        <!-- Seleção de Procedimento -->
        <div class="form-section" id="selecao-procedimento" style="display: none;">
            <h3><i class="fas fa-procedures"></i> Seleção de Procedimento</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de Evolução:</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="tipo_evolucao" value="procedimento" onchange="mostrarOpcoesProcedimento()">
                            <span class="radio-custom"></span>
                            Procedimento Clínico
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tipo_evolucao" value="generico" onchange="mostrarOpcoesGenerico()">
                            <span class="radio-custom"></span>
                            Conversa com Responsáveis
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Opções de Procedimento -->
            <div id="opcoes-procedimento" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="procedimento_id">Procedimento*</label>
                        <select id="procedimento_id" name="procedimento_id">
                            <option value="">Selecione um procedimento...</option>
                            <!-- Procedimentos serão carregados via AJAX -->
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Opções Genéricas -->
            <div id="opcoes-generico" style="display: none;">
                <div class="form-row">
                    <div class="form-group">
                        <label for="motivo_generico">Motivo da Conversa*</label>
                        <input type="text" id="motivo_generico" name="motivo_generico" placeholder="Descreva o motivo da conversa...">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="observacao_generica">Observações</label>
                        <textarea id="observacao_generica" name="observacao_generica" rows="3" placeholder="Observações adicionais..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-add" onclick="continuarParaEvolucao()" id="btn-continuar-evolucao" disabled>
                    <i class="fas fa-file-medical"></i> Criar Evolução
                </button>
                <button type="button" class="btn-clear" onclick="voltarSelecaoPaciente()">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
            </div>
        </div>
    </div>';
}

private function getFormularioEvolucao($pacienteId, $procedimentoId = null, $procedimentoGenerico = false, $resultado = null)
{
    // Busca dados do paciente
    $paciente = $this->evolucao->buscarPacientePorId($pacienteId);
    
    // Busca profissionais
    $profissionais = $this->evolucao->listarProfissionais();
    
    // Inicializa $dadosForm como array vazio
    $dadosForm = [];
    
    // Exibe mensagens de sucesso/erro
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

    // Preenche dropdowns de profissionais
    $optionsProfissionais = '<option value="">Selecione um profissional</option>';
    foreach ($profissionais as $profissional) {
        $selected = (isset($dadosForm['profissional_id']) && $dadosForm['profissional_id'] == $profissional['id']) ? 'selected' : '';
        $optionsProfissionais .= '<option value="' . $profissional['id'] . '" ' . $selected . '>' . htmlspecialchars($profissional['nome']) . ' - ' . htmlspecialchars($profissional['especialidade']) . '</option>';
    }

    // Informações do paciente
    $infoPaciente = '
    <div class="paciente-info-resumo">
        <h4><i class="fas fa-user-injured"></i> ' . htmlspecialchars($paciente['nome']) . '</h4>
        <p><strong>CNS:</strong> ' . htmlspecialchars($paciente['cns']) . '</p>
        <p><strong>Data Nasc.:</strong> ' . date('d/m/Y', strtotime($paciente['data_nascimento'])) . '</p>
    </div>';

    return '
    <div class="form-container">
        ' . $mensagens . '
        
        <!-- Informações do Paciente -->
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Informações do Paciente</h3>
            ' . $infoPaciente . '
            
            <div class="form-actions" style="justify-content: flex-start; margin-top: 15px;">
                <button type="button" class="btn-clear" onclick="voltarParaSelecao()">
                    <i class="fas fa-arrow-left"></i> Voltar para Seleção
                </button>
            </div>
        </div>
        
        <!-- Formulário de Evolução -->
        <div class="form-section">
            <h3><i class="fas fa-file-medical"></i> Nova Evolução</h3>
            <form action="" method="POST" id="form-evolucao">
                <input type="hidden" name="acao" value="cadastrar">
                <input type="hidden" name="paciente_id" value="' . $pacienteId . '">
                <input type="hidden" name="procedimento_id" value="' . ($procedimentoId ?: '') . '">
                <input type="hidden" name="procedimento_generico" value="' . ($procedimentoGenerico ? '1' : '0') . '">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="profissional_id">Profissional*</label>
                        <select required id="profissional_id" name="profissional_id">
                            ' . $optionsProfissionais . '
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="descricao">Descrição da Evolução*</label>
                        <textarea required id="descricao" name="descricao" rows="8" maxlength="1000" placeholder="Descreva detalhadamente a evolução do paciente...">' . (isset($dadosForm['descricao']) ? htmlspecialchars($dadosForm['descricao']) : '') . '</textarea>
                        <small class="form-text">Máximo 1000 caracteres</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="observacao">Observações</label>
                        <textarea id="observacao" name="observacao" rows="4" maxlength="500" placeholder="Observações adicionais...">' . (isset($dadosForm['observacao']) ? htmlspecialchars($dadosForm['observacao']) : '') . '</textarea>
                        <small class="form-text">Máximo 500 caracteres</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="assinatura_digital">Assinatura Digital</label>
                        <input type="text" id="assinatura_digital" name="assinatura_digital" maxlength="255" placeholder="Digite a assinatura digital"
                            value="' . (isset($dadosForm['assinatura_digital']) ? htmlspecialchars($dadosForm['assinatura_digital']) : '') . '">
                        <small class="form-text">Máximo 255 caracteres</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-add">
                        <i class="fas fa-save"></i> Salvar Evolução
                    </button>
                    <button type="button" class="btn-clear" onclick="voltarParaSelecao()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>';
}
    
    private function getListagemEvolucoes($resultado = null)
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
        
        // Parâmetros de paginação
        $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $limite = 10;
        $offset = ($pagina - 1) * $limite;
        
        // Busca evoluções
        $pacienteId = isset($_GET['paciente']) ? (int)$_GET['paciente'] : null;
        $evolucoes = $this->evolucao->listar($limite, $offset, $pacienteId);
        $total = $this->evolucao->getTotalEvolucoes($pacienteId);
        $totalPaginas = max(1, ceil($total / $limite));
        
        $tabelaEvolucoes = '';
        if (!empty($evolucoes)) {
            $tabelaEvolucoes = '
            <div class="table-container">
                <table class="pacientes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Profissional</th>
                            <th>Data/Hora</th>
                            <th>Resumo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($evolucoes as $evolucao) {
                $dataHora = date('d/m/Y H:i', strtotime($evolucao['data_registro']));
                $resumo = strlen($evolucao['descricao']) > 50 ? substr($evolucao['descricao'], 0, 50) . '...' : $evolucao['descricao'];
                
                $tabelaEvolucoes .= '
                    <tr>
                        <td>' . htmlspecialchars($evolucao['id']) . '</td>
                        <td>' . htmlspecialchars($evolucao['paciente_nome']) . '</td>
                        <td>' . htmlspecialchars($evolucao['profissional_nome']) . '</td>
                        <td>' . $dataHora . '</td>
                        <td>' . htmlspecialchars($resumo) . '</td>
                        <td>
                            <button class="btn-view" onclick="visualizarEvolucao(' . $evolucao['id'] . ')" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-edit" onclick="editarEvolucao(' . $evolucao['id'] . ')" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-delete" onclick="confirmarExclusao(' . $evolucao['id'] . ')" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>';
            }
            
            $tabelaEvolucoes .= '
                    </tbody>
                </table>
            </div>
            
            ' . $this->gerarPaginacao($pagina, $totalPaginas, $limite, $pacienteId) . '';
        } else {
            $tabelaEvolucoes = '<div class="no-data">Nenhuma evolução encontrada.</div>';
        }

        return '
        <div class="listagem-container">
            ' . $mensagens . '
            
            <div class="table-header">
                <h3>Listagem de Evoluções (' . $total . ' encontrada' . ($total != 1 ? 's' : '') . ')</h3>
            </div>
            
            ' . $tabelaEvolucoes . '
        </div>';
    }
    
    private function gerarPaginacao($paginaAtual, $totalPaginas, $limite, $pacienteId = null)
    {
        if ($totalPaginas <= 1) {
            return '';
        }
        
        $paginacao = '<div class="pagination">';
        
        // Botão Anterior
        if ($paginaAtual > 1) {
            $urlAnterior = '?sub=listar&pagina=' . ($paginaAtual - 1);
            if ($pacienteId) {
                $urlAnterior .= '&paciente=' . $pacienteId;
            }
            $paginacao .= '<a href="' . $urlAnterior . '" class="pagination-btn">&laquo; Anterior</a>';
        }
        
        // Páginas
        $inicio = max(1, $paginaAtual - 2);
        $fim = min($totalPaginas, $paginaAtual + 2);
        
        if ($inicio > 1) {
            $urlPrimeira = '?sub=listar&pagina=1';
            if ($pacienteId) {
                $urlPrimeira .= '&paciente=' . $pacienteId;
            }
            $paginacao .= '<a href="' . $urlPrimeira . '" class="pagination-btn">1</a>';
            if ($inicio > 2) {
                $paginacao .= '<span class="pagination-ellipsis">...</span>';
            }
        }
        
        for ($i = $inicio; $i <= $fim; $i++) {
            if ($i == $paginaAtual) {
                $paginacao .= '<span class="pagination-btn active">' . $i . '</span>';
            } else {
                $urlPagina = '?sub=listar&pagina=' . $i;
                if ($pacienteId) {
                    $urlPagina .= '&paciente=' . $pacienteId;
                }
                $paginacao .= '<a href="' . $urlPagina . '" class="pagination-btn">' . $i . '</a>';
            }
        }
        
        if ($fim < $totalPaginas) {
            if ($fim < $totalPaginas - 1) {
                $paginacao .= '<span class="pagination-ellipsis">...</span>';
            }
            $urlUltima = '?sub=listar&pagina=' . $totalPaginas;
            if ($pacienteId) {
                $urlUltima .= '&paciente=' . $pacienteId;
            }
            $paginacao .= '<a href="' . $urlUltima . '" class="pagination-btn">' . $totalPaginas . '</a>';
        }
        
        // Botão Próximo
        if ($paginaAtual < $totalPaginas) {
            $urlProximo = '?sub=listar&pagina=' . ($paginaAtual + 1);
            if ($pacienteId) {
                $urlProximo .= '&paciente=' . $pacienteId;
            }
            $paginacao .= '<a href="' . $urlProximo . '" class="pagination-btn">Próximo &raquo;</a>';
        }
        
        $paginacao .= '</div>';
        
        return $paginacao;
    }
}
?>
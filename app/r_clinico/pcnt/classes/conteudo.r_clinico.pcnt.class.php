<?php
include_once "../../../classes/db.class.php";
include_once "paciente.class.php";

class ContentRClinicoPctn
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
                <title>Registro Clínico - Pacientes</title>
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
        
        // Processa formulário se foi enviado
        $resultado = null;
        if ($_POST) {
            $resultado = $this->paciente->cadastrar($_POST);
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
                    
                    <!-- Abas principais de navegação entre módulos -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn active" onclick="redirectToTab('pacientes')">Pacientes</button>
                        <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
                        <button class="tab-btn" onclick="redirectToTab('evolucoes')">Evoluções</button>
                    </div>
                    
                    <!-- Sub-abas do módulo atual -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-pacientes">
                            <button class="tab-btn active" data-main="pacientes" data-sub="cadastro" onclick="showSubTab('pacientes', 'cadastro', this)">Cadastro</button>
                            <button class="tab-btn" data-main="pacientes" data-sub="documentos" onclick="showSubTab('pacientes', 'documentos', this)">Documentos</button>
                            <button class="tab-btn" data-main="pacientes" data-sub="historico" onclick="showSubTab('pacientes', 'historico', this)">Histórico</button>
                        </div>
                    </div>
                    
                    <!-- Conteúdo das abas -->
                    <div id="tab-content">
                        <div id="pacientes-cadastro" class="tab-content active">
                            {$this->getFormularioCadastro($resultado)}
                        </div>
                        <div id="pacientes-documentos" class="tab-content" style="display:none;">
                            <p>Conteúdo Documentos de Pacientes.</p>
                        </div>
                        <div id="pacientes-historico" class="tab-content" style="display:none;">
                            <p>Conteúdo Histórico de Pacientes.</p>
                        </div>
                    </div>
                </section>

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
        } elseif (isset($_POST)) {
            $dadosForm = $_POST;
        }
        
        // Exibe mensagens de sucesso/erro
        $mensagens = '';
        if ($resultado) {
            if ($resultado['sucesso']) {
                $mensagens = '<div class="form-message success">' . $resultado['mensagem'] . '</div>';
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
            <form action="" method="POST">
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
}

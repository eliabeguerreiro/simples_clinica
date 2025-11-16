<?php
class ContentRClinicoAtdnm
{
    public function render()
    {
$html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico - Atendimentos</title>
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

        // Dados simulados (em produção viriam do banco)
        $pacientes = [
            ['id' => 1, 'nome' => 'João Silva', 'cns' => '123456789012345'],
            ['id' => 2, 'nome' => 'Maria Santos', 'cns' => '987654321098765'],
            ['id' => 3, 'nome' => 'Pedro Oliveira', 'cns' => '456789123456789']
        ];

        $profissionais = [
            ['id' => 1, 'nome' => 'Dr. Carlos Silva', 'especialidade' => 'Clínico Geral'],
            ['id' => 2, 'nome' => 'Dra. Ana Costa', 'especialidade' => 'Psicologia'],
            ['id' => 3, 'nome' => 'Dr. Roberto Lima', 'especialidade' => 'Fisioterapia']
        ];

        $procedimentos = [
            ['id' => 1, 'codigo' => '0101010013', 'descricao' => 'Consulta médica'],
            ['id' => 2, 'codigo' => '0301050010', 'descricao' => 'Sessão de psicoterapia'],
            ['id' => 3, 'codigo' => '0404010012', 'descricao' => 'Fisioterapia']
        ];

        // Gerar options para selects
        $pacientesOptions = '';
        foreach ($pacientes as $paciente) {
            $pacientesOptions .= "<option value=\"{$paciente['id']}\">" . htmlspecialchars($paciente['nome']) . " - CNS: " . htmlspecialchars($paciente['cns']) . "</option>";
        }

        $profissionaisOptions = '';
        foreach ($profissionais as $profissional) {
            $profissionaisOptions .= "<option value=\"{$profissional['id']}\">" . htmlspecialchars($profissional['nome']) . " - " . htmlspecialchars($profissional['especialidade']) . "</option>";
        }

        $procedimentosOptions = '';
        foreach ($procedimentos as $procedimento) {
            $procedimentosOptions .= "<option value=\"{$procedimento['id']}\">" . htmlspecialchars($procedimento['codigo']) . " - " . htmlspecialchars($procedimento['descricao']) . "</option>";
        }

        // Conteúdo do formulário de atendimentos
        $formularioAtendimentos = '
        <!-- Overlay de carregamento -->
        <div id="loading-overlay">
            <div class="spinner-container">
                <div class="spinner">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <p>Salvando atendimentos, aguarde...</p>
            </div>
        </div>

        <div class="form-container">
            <form action="" method="POST">
                <!-- Dados Básicos -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="competencia">Competência (AAAAMM)</label>
                        <div class="input-with-button">
                            <input type="text" id="competencia" name="competencia" required pattern="\d{6}" title="Formato: AAAAMM">
                            <!--<button type="button" class="btn-mes-atual" onclick="document.getElementById(\'competencia\').value=\'' . date('Ym') . '\'">
                                Usar mês atual<br>
                            </button>-->
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="data_atendimento">Data do Atendimento</label>
                        <input type="date" id="data_atendimento" name="data_atendimento" required>
                    </div>
                    <div class="form-group">
                        <label for="cid">CID-10 (Opcional)</label>
                        <input type="text" id="cid" name="cid" maxlength="4" placeholder="Ex: F800">
                    </div>
                </div>

                <!-- Seleção Múltipla de Pacientes -->
                <div class="form-group">
                    <label>Selecionar Pacientes</label>
                    <div class="procedure-selector bloco-pacientes">
                        <div class="list-container">
                            <select id="available-patients" multiple size="10">
                                ' . $pacientesOptions . '
                            </select>
                        </div>
                        <div class="buttons-container">
                            <button type="button" id="btn-add-patient" class="btn-add-patient">▶</button>
                            <button type="button" id="btn-remove-patient" class="btn-remove-patient">◀</button>
                            <button type="button" id="btn-add-all-patients" class="btn-add-all-patients">▶▶</button>
                            <button type="button" id="btn-remove-all-patients" class="btn-remove-all-patients">◀◀</button>
                        </div>
                        <div class="list-container">
                            <select id="selected-patients" name="paciente_id[]" multiple size="10"></select>
                        </div>
                    </div>
                </div>

                <!-- Profissionais -->
                <div class="form-group">
                    <label>Profissionais</label>
                    <div class="procedure-selector bloco-profissionais">
                        <div class="list-container">
                            <select id="available-professionals" multiple size="10">
                                ' . $profissionaisOptions . '
                            </select>
                        </div>
                        <div class="buttons-container">
                            <button type="button" id="btn-add-professional" class="btn-add-professional">▶</button>
                            <button type="button" id="btn-remove-professional" class="btn-remove-professional">◀</button>
                            <button type="button" id="btn-add-all-professionals" class="btn-add-all-professionals">▶▶</button>
                            <button type="button" id="btn-remove-all-professionals" class="btn-remove-all-professionals">◀◀</button>
                        </div>
                        <div class="list-container">
                            <select id="selected-professionals" name="profissional_id[]" multiple size="10"></select>
                        </div>
                    </div>
                </div>

                <!-- Procedimentos -->
                <div class="form-group">
                    <label>Procedimentos</label>
                    <div class="procedure-selector bloco-procedimentos">
                        <div class="list-container">
                            <select id="available-procedures" multiple size="10">
                                ' . $procedimentosOptions . '
                            </select>
                        </div>
                        <div class="buttons-container">
                            <button type="button" id="btn-add-procedure" class="btn-add-single">▶</button>
                            <button type="button" id="btn-remove-procedure" class="btn-remove-single">◀</button>
                            <button type="button" id="btn-add-all-procedures" class="btn-add-all">▶▶</button>
                            <button type="button" id="btn-remove-all-procedures" class="btn-remove-all">◀◀</button>
                        </div>
                        <div class="list-container">
                            <select id="selected-procedures" name="procedimento_id[]" multiple size="10"></select>
                        </div>
                    </div>
                </div>

                <!-- Botão final -->
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn-add">Inserir Atendimento</button>
                </div>
            </form>
        </div>';

$html = <<<HTML
            <body>
                <header>
                    <div class="logo">
                        <img src="#" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="../../">INICIO</a></li>
                            <li><a href="#">SUPORTE</a></li>
                            <li><a href="?sair">SAIR</a></li>
                        </ul>
                    </nav>
                </header>

                <section class="simple-box">
                    <h2>Registro Clínico</h2>
                    
                    <!-- Abas principais de navegação entre módulos -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn" onclick="window.location.href='../pcnt/'">Pacientes</button>
                        <button class="tab-btn active">Atendimentos</button>
                        <button class="tab-btn" onclick="window.location.href='../evlt/'">Evoluções</button>
                    </div>
                    
                    <!-- Sub-abas do módulo atual -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-atendimentos">
                            <button class="tab-btn active" data-main="atendimentos" data-sub="novo" onclick="showSubTab('atendimentos', 'novo', this)">Novo</button>
                            <button class="tab-btn" data-main="atendimentos" data-sub="listar" onclick="showSubTab('atendimentos', 'listar', this)">Listar</button>
                            <button class="tab-btn" data-main="atendimentos" data-sub="relatorios" onclick="showSubTab('atendimentos', 'relatorios', this)">Relatórios</button>
                        </div>
                    </div>
                    
                    <!-- Conteúdo das abas -->
                    <div id="tab-content">
                        <div id="atendimentos-novo" class="tab-content active">
                            {$formularioAtendimentos}
                        </div>
                        <div id="atendimentos-listar" class="tab-content" style="display:none;">
                            <p>Conteúdo Listar Atendimentos.</p>
                        </div>
                        <div id="atendimentos-relatorios" class="tab-content" style="display:none;">
                            <p>Conteúdo Relatórios de Atendimentos.</p>
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
}

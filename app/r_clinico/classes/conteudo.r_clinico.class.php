<?php
class ContentRClinico
{
    public function renderHeader()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico</title>
                <link rel="stylesheet" href="./src/style.css">
            </head>
        HTML;
        return $html;
    }

    private function generateMainTabs($tabs)
    {
        $html = '<div class="tabs" id="main-tabs">';
        foreach ($tabs as $index => $tab) {
            $activeClass = $index === 0 ? 'active' : '';
            $html .= "<button class=\"tab-btn {$activeClass}\" data-tab=\"{$tab['id']}\" onclick=\"showMainTab('{$tab['id']}', this)\">{$tab['label']}</button>";
        }
        $html .= '</div>';
        return $html;
    }

    private function generateSubTabs($tabs)
    {
        $html = '<div id="sub-tabs">';
        foreach ($tabs as $tab) {
            $displayStyle = $tab['id'] === 'pacientes' ? '' : 'style="display:none;"';
            $html .= "<div class=\"sub-tabs\" id=\"sub-{$tab['id']}\" {$displayStyle}>";
            
            foreach ($tab['subtabs'] as $subIndex => $subtab) {
                $activeClass = $subIndex === 0 ? 'active' : '';
                $html .= "<button class=\"tab-btn {$activeClass}\" data-main=\"{$tab['id']}\" data-sub=\"{$subtab['id']}\" onclick=\"showSubTab('{$tab['id']}', '{$subtab['id']}', this)\">{$subtab['label']}</button>";
            }
            
            $html .= "</div>";
        }
        $html .= '</div>';
        return $html;
    }

    private function generateContent($tabs)
    {
        $html = '<div id="tab-content">';
        foreach ($tabs as $tab) {
            foreach ($tab['subtabs'] as $subIndex => $subtab) {
                $activeClass = $subIndex === 0 ? 'active' : '';
                $displayStyle = $subIndex === 0 ? '' : 'style="display:none;"';
                
                $html .= "<div id=\"{$tab['id']}-{$subtab['id']}\" class=\"tab-content {$activeClass}\" {$displayStyle}>";
                $html .= "<p>{$subtab['content']}</p>";
                $html .= "</div>";
            }
        }
        $html .= '</div>';
        return $html;
    }

    public function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);

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
                        <button type="button" class="btn-mes-atual" onclick="document.getElementById(\'competencia\').value=\'' . date('Ym') . '\'">
                            Usar mês atual
                        </button>
                    </div>
                </div>
                <br>
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

        $mainTabs = [
            [
                'id' => 'pacientes',
                'label' => 'Pacientes',
                'subtabs' => [
                    ['id' => 'cadastro', 'label' => 'Cadastro', 'content' => '
                <div class="form-container">
                    <form action="" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome">Nome Completo*</label>
                                <input required type="text" id="nome" name="nome" required maxlength="100" placeholder="Digite o nome completo">
                            </div>
                            <div class="form-group">
                                <label for="cns">CNS*</label>
                                <input required type="text" id="cns" name="cns" required maxlength="100" placeholder="Digite o CNS">
                            </div>
                            <div class="form-group">
                                <label for="data_nascimento">Data*</label>
                                <input type="date" id="data_nascimento" name="data_nascimento" required placeholder="dd/mm/aaaa">
                            </div>
                            <div class="form-group">
                                <label for="raca_cor">Raça/Cor*</label>
                                <select required id="raca_cor" name="raca_cor" required>
                                    <option value="">Selecionar</option>
                                    <option value="01">Branca</option>
                                    <option value="02">Preta</option>
                                    <option value="03">Parda</option>
                                    <option value="04">Amarela</option>
                                    <option value="05">Indígena</option>
                                    <option value="99">Sem informação</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="sexo">Sexo*</label>
                                <select required id="sexo" name="sexo" required>
                                    <option value="">Selecionar</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="etnia">Etnia</label>
                                <input type="text" id="etnia" name="etnia" maxlength="4" placeholder="Selecionar">
                            </div>
                            <div class="form-group">
                                <label for="nacionalidade">Nacionalidade*</label>
                                <select required id="nacionalidade" name="nacionalidade" required>
                                    <option value="">Selecionar</option>
                                    <option value="10">Brasileira</option>
                                    <option value="20">Naturalizado</option>
                                    <option value="30">Estrangeiro</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="codigo_logradouro">Tipo do Logradouro*</label>
                                <select required id="codigo_logradouro" name="codigo_logradouro" required>
                                    <option value="">Selecionar</option>
                                    <option value="81">Rua</option>
                                    <option value="8">Avenida</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="endereco">Logradouro*</label>
                                <input required type="text" id="endereco" name="endereco" required maxlength="100" placeholder="Digite o logradouro">
                            </div>
                            <div class="form-group">
                                <label for="numero">Número*</label>
                                <input required type="text" id="numero" name="numero" required maxlength="10" placeholder="Digite o número">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="complemento">Complemento</label>
                                <input type="text" id="complemento" name="complemento" maxlength="30" placeholder="Ex: Apt 101">
                            </div>
                            <div class="form-group">
                                <label for="bairro">Bairro*</label>
                                <input required type="text" id="bairro" name="bairro" required maxlength="60" placeholder="Informe o bairro">
                            </div>
                            <div class="form-group">
                                <label for="cep">CEP*</label>
                                <input required type="text" id="cep" name="cep" required maxlength="9" placeholder="00000-000">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefone">Telefone*</label>
                                <input required type="text" id="telefone" name="telefone" required maxlength="15" placeholder="(00) 00000-0000">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com">
                            </div>
                            <div class="form-group">
                                <label for="situacao_rua">Situação de Rua?</label>
                                <select required id="situacao_rua" name="situacao_rua" required>
                                    <option value="N">Não</option>
                                    <option value="S">Sim</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-add">
                            <i class="fas fa-save"></i> Salvar Paciente
                        </button>
                    </form>
                </div>'
        ],



                    ['id' => 'documentos', 'label' => 'Documentos', 'content' => 'Conteúdo Documentos de Pacientes.'],
                    ['id' => 'historico', 'label' => 'Histórico', 'content' => 'Conteúdo Histórico de Pacientes.']
                ]
            ],
            [
                'id' => 'atendimentos',
                'label' => 'Atendimentos',
                'subtabs' => [
                    ['id' => 'novo', 'label' => 'Novo', 'content' =>   $formularioAtendimentos],
                    ['id' => 'listar', 'label' => 'Listar', 'content' => 'Conteúdo Listar Atendimentos.'],
                    ['id' => 'relatorios', 'label' => 'Relatórios', 'content' => 'Conteúdo Relatórios de Atendimentos.']
                ]
            ],
            [
                'id' => 'evolucoes',
                'label' => 'Evoluções',
                'subtabs' => [
                    ['id' => 'nova', 'label' => 'Nova', 'content' => 'Conteúdo Nova Evolução.'],
                    ['id' => 'listar', 'label' => 'Listar', 'content' => 'Conteúdo Listar Evoluções.'],
                    ['id' => 'graficos', 'label' => 'Gráficos', 'content' => 'Conteúdo Gráficos de Evoluções.']
                ]
            ]
        ];

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
                    {$this->generateMainTabs($mainTabs)}
                    
                    <!-- Sub-abas -->
                    {$this->generateSubTabs($mainTabs)}
                    
                    <!-- Conteúdo das abas -->
                    {$this->generateContent($mainTabs)}
                </section>

                <script src="src/script.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
            </body>
HTML;

        return $html;
    }
}
?>
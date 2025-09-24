<?php
class ConteudoFormsPrincipal
{
    public function render()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulários - Evoluções</title>
    <link rel="stylesheet" href="../src/style.css">
    <link rel="stylesheet" href="./src/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="#" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="../../">INICIO</a></li>
                <li><a href="/atendimentos.php">SUPORTE</a></li>
                <li><a href="/paciente">SAIR</a></li>
            </ul>
        </nav>
    </header>

    <section class="simple-box">
        <h2>Formulários de Evolução</h2>
        
        <!-- Botão de voltar para evoluções -->
        <div style="margin-bottom: 20px;">
            <button class="btn-clear" onclick="voltarParaEvolucoes()">
                <i class="fas fa-arrow-left"></i> Voltar para Evoluções
            </button>
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
                    <button class="btn-add" onclick="window.location.href='?acao=criar'">
                        <i class="fas fa-plus"></i> Criar Formulário
                    </button>
                </div>
            </div>
            
            <div class="formulario-card">
                <div class="card-header">
                    <i class="fas fa-list"></i>
                    <h4>Gerenciar Formulários</h4>
                </div>
                <div class="card-body">
                    <p>Gerencie formulários já criados e seus campos</p>
                </div>
                <div class="card-footer">
                    <button class="btn-edit" onclick="window.location.href='?acao=gerenciar'">
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
                    <button class="btn-evolucao" onclick="window.location.href='?acao=aplicar'">
                        <i class="fas fa-play"></i> Aplicar
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
    function voltarParaEvolucoes() {
        window.location.href = '../';
    }
    </script>
</body>
</html>
HTML;

        return $html;
    }
}

class ConteudoFormsCriar
{
    public function render()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Formulário - Evoluções</title>
    <link rel="stylesheet" href="../src/style.css">
    <link rel="stylesheet" href="./src/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="#" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="../../">INICIO</a></li>
                <li><a href="/atendimentos.php">SUPORTE</a></li>
                <li><a href="/paciente">SAIR</a></li>
            </ul>
        </nav>
    </header>

    <section class="simple-box">
        <h2>Construtor de Formulários - Evoluções</h2>
        
        <!-- Botão de voltar para evoluções -->
        <div style="margin-bottom: 20px;">
            <button class="btn-clear" onclick="voltarParaEvolucoes()">
                <i class="fas fa-arrow-left"></i> Voltar para Evoluções
            </button>
        </div>
        
        <!-- Formulário para criar template -->
        <div class="form-builder-container">
            <h3><i class="fas fa-plus-circle"></i> Criar Novo Formulário</h3>
            
            <form id="form-template" class="form-template">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nome_template">Nome do Formulário*</label>
                        <input type="text" id="nome_template" name="nome" required maxlength="100" placeholder="Ex: Avaliação Fonoaudiológica">
                    </div>
                    <div class="form-group">
                        <label for="area_atendimento">Área de Atendimento*</label>
                        <select id="area_atendimento" name="area_atendimento" required>
                            <option value="">Selecione...</option>
                            <option value="fonoaudiologia">Fonoaudiologia</option>
                            <option value="psicologia">Psicologia</option>
                            <option value="fisioterapia">Fisioterapia</option>
                            <option value="nutricao">Nutrição</option>
                            <option value="odontologia">Odontologia</option>
                            <option value="medicina">Medicina</option>
                            <option value="enfermagem">Enfermagem</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="descricao_template">Descrição</label>
                        <textarea id="descricao_template" name="descricao" rows="3" maxlength="500" placeholder="Descrição do formulário..."></textarea>
                    </div>
                </div>
                
                <button type="submit" class="btn-add">
                    <i class="fas fa-save"></i> Criar Template
                </button>
            </form>
            
            <!-- Área de construção de campos -->
            <div class="form-builder-area" style="display: none; margin-top: 30px;">
                <h4><i class="fas fa-tools"></i> Construtor de Campos</h4>
                
                <!-- Lista de campos adicionados -->
                <div class="campos-adicionados" id="campos-adicionados">
                    <div class="no-data">Nenhum campo adicionado ainda.</div>
                </div>
                
                <!-- Formulário para adicionar novo campo -->
                <div class="add-campo-form">
                    <h5><i class="fas fa-plus"></i> Adicionar Novo Campo</h5>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_campo">Tipo de Campo*</label>
                            <select id="tipo_campo" onchange="mostrarOpcoesCampo()">
                                <option value="">Selecione...</option>
                                <option value="texto">Texto Curto</option>
                                <option value="textarea">Texto Longo</option>
                                <option value="radio">Radio Button</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="select">Lista Suspensa</option>
                                <option value="numero">Número</option>
                                <option value="data">Data</option>
                                <option value="hora">Hora</option>
                                <option value="email">Email</option>
                                <option value="telefone">Telefone</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="titulo_campo">Título do Campo*</label>
                            <input type="text" id="titulo_campo" maxlength="200" placeholder="Título do campo">
                        </div>
                        
                        <div class="form-group">
                            <label for="descricao_campo">Descrição do Campo</label>
                            <input type="text" id="descricao_campo" maxlength="500" placeholder="Descrição do campo (opcional)">
                        </div>
                    </div>
                    
                    <!-- Opções para campos com opções (radio, checkbox, select) -->
                    <div class="opcoes-campo" id="opcoes-campo" style="display: none;">
                        <div class="form-group">
                            <label>Opções (uma por linha):</label>
                            <textarea id="opcoes_texto" rows="4" placeholder="Opção 1&#10;Opção 2&#10;Opção 3"></textarea>
                            <small class="form-text">Insira uma opção por linha</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="campo_obrigatorio"> Campo Obrigatório
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="campo_multipla_escolha"> Múltipla Escolha (somente para checkbox)
                            </label>
                        </div>
                        <div class="form-group">
                            <button type="button" onclick="adicionarCampo()" class="btn-add">
                                <i class="fas fa-plus"></i> Adicionar Campo
                            </button>
                        </div>
                    </div>
                </div>
                
                <button type="button" onclick="salvarFormulario()" class="btn-add" style="margin-top: 30px;">
                    <i class="fas fa-save"></i> Salvar Formulário Completo
                </button>
            </div>
        </div>
    </section>

</body>
</html>
HTML;

        return $html;
    }
}
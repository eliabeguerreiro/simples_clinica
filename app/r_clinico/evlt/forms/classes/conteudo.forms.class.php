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

    <script>
    function voltarParaEvolucoes() {
        window.location.href = '../';
    }
    
    // Funções para o construtor de formulários
    
    let camposFormulario = [];
    let templateId = null;

    // Mostrar opções de campo conforme tipo selecionado
    function mostrarOpcoesCampo() {
        const tipo = document.getElementById('tipo_campo').value;
        const opcoesDiv = document.getElementById('opcoes-campo');
        
        if (['radio', 'checkbox', 'select'].includes(tipo)) {
            opcoesDiv.style.display = 'block';
        } else {
            opcoesDiv.style.display = 'none';
        }
    }

    // Adicionar campo ao formulário
    function adicionarCampo() {
        const tipo = document.getElementById('tipo_campo').value;
        const titulo = document.getElementById('titulo_campo').value;
        const descricao = document.getElementById('descricao_campo').value;
        const obrigatorio = document.getElementById('campo_obrigatorio').checked;
        const multiplaEscolha = document.getElementById('campo_multipla_escolha').checked;
        
        if (!tipo || !titulo) {
            alert('Preencha todos os campos obrigatórios!');
            return;
        }
        
        const campo = {
            id: Date.now(), // ID temporário
            tipo: tipo,
            titulo: titulo,
            descricao: descricao,
            obrigatorio: obrigatorio,
            multipla_escolha: multiplaEscolha,
            ordem: camposFormulario.length + 1
        };
        
        // Adiciona opções se for campo de seleção
        if (['radio', 'checkbox', 'select'].includes(tipo)) {
            const opcoesTexto = document.getElementById('opcoes_texto').value;
            campo.opcoes = opcoesTexto.split('\\n').filter(op => op.trim() !== '').map(op => op.trim());
            
            if (campo.opcoes.length === 0) {
                alert('Adicione pelo menos uma opção para este tipo de campo!');
                return;
            }
        }
        
        camposFormulario.push(campo);
        renderizarCampos();
        
        // Limpa formulário
        document.getElementById('tipo_campo').value = '';
        document.getElementById('titulo_campo').value = '';
        document.getElementById('descricao_campo').value = '';
        document.getElementById('campo_obrigatorio').checked = false;
        document.getElementById('campo_multipla_escolha').checked = false;
        document.getElementById('opcoes-campo').style.display = 'none';
        document.getElementById('opcoes_texto').value = '';
        
        // Mostra mensagem de sucesso
        mostrarMensagem('Campo adicionado com sucesso!', 'success');
    }

    // Renderizar campos adicionados
    function renderizarCampos() {
        const container = document.getElementById('campos-adicionados');
        
        if (camposFormulario.length === 0) {
            container.innerHTML = '<div class="no-data">Nenhum campo adicionado ainda.</div>';
            return;
        }
        
        container.innerHTML = '';
        
        camposFormulario.forEach((campo, index) => {
            const campoDiv = document.createElement('div');
            campoDiv.className = 'campo-preview';
            campoDiv.innerHTML = \`
                <div class="campo-header">
                    <span class="campo-titulo">\${campo.titulo}</span>
                    <span class="campo-tipo">(\${campo.tipo})</span>
                    \${campo.obrigatorio ? '<span class="campo-obrigatorio">Obrigatório</span>' : ''}
                    \${campo.multipla_escolha ? '<span class="campo-multipla">Múltipla</span>' : ''}
                    <button type="button" onclick="removerCampo(\${index})" class="btn-delete-small">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                \${campo.descricao ? \`<div class="campo-descricao"><strong>Descrição:</strong> \${campo.descricao}</div>\` : ''}
                \${campo.opcoes ? \`
                    <div class="campo-opcoes">
                        <strong>Opções:</strong>
                        <ul>
                            \${campo.opcoes.map(op => \`<li>\${op}</li>\`).join('')}
                        </ul>
                    </div>
                \` : ''}
            \`;
            container.appendChild(campoDiv);
        });
    }

    // Remover campo
    function removerCampo(index) {
        if (confirm('Tem certeza que deseja excluir este campo?')) {
            camposFormulario.splice(index, 1);
            // Reordenar campos
            camposFormulario.forEach((campo, i) => campo.ordem = i + 1);
            renderizarCampos();
            mostrarMensagem('Campo removido com sucesso!', 'success');
        }
    }

    // Salvar formulário completo
    function salvarFormulario() {
        if (camposFormulario.length === 0) {
            alert('Adicione pelo menos um campo ao formulário!');
            return;
        }
        
        const nomeTemplate = document.getElementById('nome_template').value;
        const areaAtendimento = document.getElementById('area_atendimento').value;
        
        if (!nomeTemplate || !areaAtendimento) {
            alert('Preencha o nome do formulário e a área de atendimento!');
            return;
        }
        
        const formData = {
            template_id: templateId,
            nome: nomeTemplate,
            area_atendimento: areaAtendimento,
            descricao: document.getElementById('descricao_template').value,
            campos: camposFormulario
        };
        
        // Aqui você enviaria para o servidor
        console.log('Salvando formulário:', formData);
        mostrarMensagem('Formulário salvo com sucesso!', 'success');
        
        // Resetar formulário
        camposFormulario = [];
        document.getElementById('form-template').reset();
        document.querySelector('.form-builder-area').style.display = 'none';
        renderizarCampos();
    }

    // Mostrar mensagem de feedback
    function mostrarMensagem(mensagem, tipo) {
        const mensagemDiv = document.createElement('div');
        mensagemDiv.className = \`form-message \${tipo}\`;
        mensagemDiv.innerHTML = \`<p>\${mensagem}</p>\`;
        
        // Adiciona ao início do formulário
        const formContainer = document.querySelector('.form-container');
        if (formContainer) {
            formContainer.insertBefore(mensagemDiv, formContainer.firstChild);
            
            // Remove mensagem após 5 segundos
            setTimeout(() => {
                if (mensagemDiv.parentNode) {
                    mensagemDiv.parentNode.removeChild(mensagemDiv);
                }
            }, 5000);
        }
    }

    // Evento de submit do formulário de template
    document.addEventListener('DOMContentLoaded', function() {
        const formTemplate = document.getElementById('form-template');
        if (formTemplate) {
            formTemplate.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nomeTemplate = document.getElementById('nome_template').value;
                const areaAtendimento = document.getElementById('area_atendimento').value;
                
                if (nomeTemplate && areaAtendimento) {
                    // Mostra área de construção de campos
                    document.querySelector('.form-builder-area').style.display = 'block';
                    mostrarMensagem('Template criado com sucesso! Agora adicione os campos.', 'success');
                } else {
                    mostrarMensagem('Preencha todos os campos obrigatórios!', 'error');
                }
            });
        }
    });
    </script>
</body>
</html>
HTML;

        return $html;
    }
}
?>
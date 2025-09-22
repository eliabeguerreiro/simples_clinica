// Redireciona para a pasta correta via GET
function redirectToTab(tabId) {
    let destino = '';
    if (tabId === 'pacientes') destino = 'pcnt';
    if (tabId === 'atendimentos') destino = 'atndm';
    if (tabId === 'evolucoes') destino = 'evlt';
    window.location.href = '../index.php?A=' + destino;
}

// Função para mostrar sub-aba
function showSubTab(mainId, subId, clickedButton) {
    // Oculta todos os conteúdos
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });

    // Remove ativação dos botões de sub-aba
    document.querySelectorAll(`#sub-${mainId} .tab-btn`).forEach(btn => {
        btn.classList.remove('active');
    });

    // Ativa o botão clicado
    if (clickedButton) {
        clickedButton.classList.add('active');
    }

    // Mostra o conteúdo da sub-aba
    const content = document.getElementById(`${mainId}-${subId}`);
    if (content) {
        content.style.display = 'block';
    }
}

$(document).ready(function(){
    $('#telefone').mask('(00) 00000-0000');
    $('#cep').mask('00000-000');
    $('#cns').mask('000 0000 0000 0000');
});

// Funções para listagem de pacientes

// Selecionar todos os checkboxes
function selecionarTodos(checkbox) {
    const checkboxes = document.querySelectorAll('.checkbox-paciente');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    atualizarBotaoExcluirSelecionados();
}

// Atualizar visibilidade do botão de exclusão múltipla
function atualizarBotaoExcluirSelecionados() {
    const selecionados = document.querySelectorAll('.checkbox-paciente:checked');
    const btn = document.getElementById('btn-excluir-selecionados');
    if (selecionados.length > 0) {
        btn.style.display = 'inline-block';
    } else {
        btn.style.display = 'none';
    }
}

// Adicionar listener para checkboxes individuais
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('checkbox-paciente')) {
        atualizarBotaoExcluirSelecionados();
    }
});

// Editar paciente (implementação básica)
function editarPaciente(id) {
    alert('Função de edição ainda será implementada. ID do paciente: ' + id);
    // Aqui você pode redirecionar para uma página de edição ou abrir um modal
}

// Confirmar exclusão individual
let pacienteParaExcluir = null;

function confirmarExclusao(id) {
    pacienteParaExcluir = id;
    document.getElementById('modal-exclusao').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modal-exclusao').style.display = 'none';
    pacienteParaExcluir = null;
}

// Confirmar exclusão no modal
document.getElementById('confirmar-exclusao').addEventListener('click', function() {
    if (pacienteParaExcluir) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="acao" value="excluir">
            <input type="hidden" name="id" value="${pacienteParaExcluir}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
});

// Excluir selecionados
function excluirSelecionados() {
    const selecionados = document.querySelectorAll('.checkbox-paciente:checked');
    if (selecionados.length === 0) {
        alert('Nenhum paciente selecionado.');
        return;
    }
    
    if (confirm(`Tem certeza que deseja excluir ${selecionados.length} paciente(s)?`)) {
        const ids = Array.from(selecionados).map(cb => cb.value);
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="acao" value="excluir_multiplos">
            ${ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modal-exclusao');
    if (e.target === modal) {
        fecharModal();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal();
    }
});

// Função para abrir evolução do paciente
function abrirEvolucao(pacienteId) {
    // Aqui você pode redirecionar para a página de evolução ou abrir um modal
    alert('Função de evolução ainda será implementada. ID do paciente: ' + pacienteId);
    
    // Exemplo de redirecionamento:
    // window.location.href = '../evolucoes/?paciente=' + pacienteId;
    
    // Ou exemplo de abertura de modal:
    // abrirModalEvolucao(pacienteId);
}

// Função para redirecionar para o módulo de evoluções
function redirectToEvolucoes(pacienteId) {
    // Implementação do redirecionamento para evoluções
    window.location.href = '../evlt/?paciente=' + pacienteId;
}

// Redireciona para a pasta correta via GET
function redirectToTab(tabId) {
    let destino = '';
    if (tabId === 'pacientes') destino = 'pcnt';
    if (tabId === 'atendimentos') destino = 'atndm';
    if (tabId === 'evolucoes') destino = 'evlt';
    window.location.href = '../index.php?A=' + destino;
}

// Função para mostrar sub-aba
function showSubTab(mainId, subId, clickedButton) {
    // Oculta todos os conteúdos
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });

    // Remove ativação dos botões de sub-aba
    document.querySelectorAll(`#sub-${mainId} .tab-btn`).forEach(btn => {
        btn.classList.remove('active');
    });

    // Ativa o botão clicado
    if (clickedButton) {
        clickedButton.classList.add('active');
    }

    // Mostra o conteúdo da sub-aba
    const content = document.getElementById(`${mainId}-${subId}`);
    if (content) {
        content.style.display = 'block';
    }
}

// Funções para evoluções

// Visualizar evolução (implementação básica)
function visualizarEvolucao(id) {
    alert('Função de visualização ainda será implementada. ID da evolução: ' + id);
    // Aqui você pode abrir um modal com os detalhes da evolução
}

// Editar evolução (implementação básica)
function editarEvolucao(id) {
    alert('Função de edição ainda será implementada. ID da evolução: ' + id);
    // Aqui você pode redirecionar para uma página de edição ou abrir um modal
}

// Confirmar exclusão individual
let evolucaoParaExcluir = null;

function confirmarExclusao(id) {
    evolucaoParaExcluir = id;
    document.getElementById('modal-exclusao').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modal-exclusao').style.display = 'none';
    evolucaoParaExcluir = null;
}

// Confirmar exclusão no modal
document.getElementById('confirmar-exclusao').addEventListener('click', function() {
    if (evolucaoParaExcluir) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="acao" value="excluir">
            <input type="hidden" name="id" value="${evolucaoParaExcluir}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
});

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modal-exclusao');
    if (e.target === modal) {
        fecharModal();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal();
    }
});

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa a primeira aba
    const firstMainTab = document.querySelector('#main-tabs .tab-btn');
    if (firstMainTab) {
        firstMainTab.click();
    }
});

// Função para limpar formulário
function limparFormulario() {
    if (confirm('Tem certeza que deseja limpar todos os campos?')) {
        document.getElementById('form-evolucao').reset();
        
        // Limpa textareas específicas se necessário
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => textarea.value = '');
        
        // Remove mensagens de erro/sucesso
        const messages = document.querySelectorAll('.form-message');
        messages.forEach(msg => msg.remove());
    }
}

// Função para validar formulário antes do envio
function validarFormulario() {
    const profissional = document.getElementById('profissional_id').value;
    const paciente = document.getElementById('paciente_id').value;
    const descricao = document.getElementById('descricao').value.trim();
    
    if (!profissional) {
        alert('Selecione um profissional.');
        document.getElementById('profissional_id').focus();
        return false;
    }
    
    if (!paciente) {
        alert('Selecione um paciente.');
        document.getElementById('paciente_id').focus();
        return false;
    }
    
    if (!descricao) {
        alert('Preencha a descrição da evolução.');
        document.getElementById('descricao').focus();
        return false;
    }
    
    if (descricao.length < 10) {
        alert('A descrição deve ter pelo menos 10 caracteres.');
        document.getElementById('descricao').focus();
        return false;
    }
    
    return true;
}

// Adicionar validação ao formulário
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-evolucao');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validarFormulario()) {
                e.preventDefault();
            }
        });
    }
    
    // Contador de caracteres para descrição
    const descricaoField = document.getElementById('descricao');
    if (descricaoField) {
        const maxLength = 1000;
        descricaoField.addEventListener('input', function() {
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            // Atualiza contador visual (opcional)
            let counter = document.getElementById('descricao-counter');
            if (!counter) {
                counter = document.createElement('div');
                counter.id = 'descricao-counter';
                counter.className = 'char-counter';
                this.parentNode.appendChild(counter);
            }
            counter.textContent = `${currentLength}/${maxLength} caracteres`;
            
            if (remaining < 100) {
                counter.style.color = '#ff6b6b';
            } else {
                counter.style.color = '#666';
            }
        });
    }
    
    // Inicializa a primeira aba
    const firstMainTab = document.querySelector('#main-tabs .tab-btn');
    if (firstMainTab) {
        firstMainTab.click();
    }
});

// Funções para navegação em etapas

// Habilita botão de continuar quando paciente é selecionado
document.addEventListener('change', function(e) {
    if (e.target.id === 'paciente_id') {
        const btnContinuar = document.getElementById('btn-continuar-paciente');
        if (btnContinuar) {
            btnContinuar.disabled = !e.target.value;
        }
    }
    
    if (e.target.name === 'tipo_evolucao') {
        const btnContinuar = document.getElementById('btn-continuar-evolucao');
        if (btnContinuar) {
            btnContinuar.disabled = false;
        }
    }
});

// Busca histórico do paciente
function buscarHistoricoPaciente(pacienteId) {
    if (!pacienteId) return;
    
    const historicoDiv = document.getElementById('historico-paciente');
    const selecaoDiv = document.getElementById('selecao-procedimento');
    
    if (historicoDiv) {
        historicoDiv.style.display = 'block';
        document.getElementById('conteudo-historico').innerHTML = '<p>Carregando histórico...</p>';
        
        // Aqui você faria a chamada AJAX para buscar o histórico
        // Por enquanto, simulando:
        setTimeout(() => {
            document.getElementById('conteudo-historico').innerHTML = `
                <div class="historico-item">
                    <h5>Últimas Evoluções</h5>
                    <p>Nenhuma evolução registrada para este paciente.</p>
                </div>
            `;
        }, 500);
    }
    
    if (selecaoDiv) {
        selecaoDiv.style.display = 'block';
    }
}

// Mostra opções de procedimento
function mostrarOpcoesProcedimento() {
    document.getElementById('opcoes-procedimento').style.display = 'block';
    document.getElementById('opcoes-generico').style.display = 'none';
    
    // Carrega procedimentos via AJAX (simulação)
    carregarProcedimentos();
}

// Mostra opções genéricas
function mostrarOpcoesGenerico() {
    document.getElementById('opcoes-procedimento').style.display = 'none';
    document.getElementById('opcoes-generico').style.display = 'block';
}

// Carrega procedimentos (simulação)
function carregarProcedimentos() {
    const select = document.getElementById('procedimento_id');
    if (select) {
        select.innerHTML = '<option value="">Carregando procedimentos...</option>';
        
        // Simulação - em produção buscaria via AJAX
        setTimeout(() => {
            select.innerHTML = `
                <option value="">Selecione um procedimento...</option>
                <option value="1">Avaliação Fonoaudiológica</option>
                <option value="2">Sessão de Psicoterapia</option>
                <option value="3">Consulta Médica</option>
                <option value="4">Fisioterapia</option>
            `;
        }, 800);
    }
}

// Continuar para evolução
function continuarParaEvolucao() {
    const tipoEvolucao = document.querySelector('input[name="tipo_evolucao"]:checked');
    if (!tipoEvolucao) {
        alert('Selecione o tipo de evolução.');
        return;
    }
    
    const pacienteId = document.getElementById('paciente_id').value;
    if (!pacienteId) {
        alert('Selecione um paciente.');
        return;
    }
    
    let url = `?sub=nova&paciente=${pacienteId}`;
    
    if (tipoEvolucao.value === 'procedimento') {
        const procedimentoId = document.getElementById('procedimento_id').value;
        if (!procedimentoId) {
            alert('Selecione um procedimento.');
            return;
        }
        url += `&procedimento=${procedimentoId}`;
    } else {
        url += '&generico=1';
    }
    
    window.location.href = url;
}

// Volta para seleção de paciente
function voltarSelecaoPaciente() {
    const historicoDiv = document.getElementById('historico-paciente');
    const selecaoDiv = document.getElementById('selecao-procedimento');
    
    if (historicoDiv) historicoDiv.style.display = 'none';
    if (selecaoDiv) selecaoDiv.style.display = 'none';
    
    document.getElementById('paciente_id').value = '';
    document.getElementById('btn-continuar-paciente').disabled = true;
}

// Volta para seleção inicial
function voltarParaSelecao() {
    window.location.href = '?sub=nova';
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Verifica se já tem paciente selecionado na URL
    const urlParams = new URLSearchParams(window.location.search);
    const pacienteId = urlParams.get('paciente');
    
    if (pacienteId) {
        document.getElementById('paciente_id').value = pacienteId;
        buscarHistoricoPaciente(pacienteId);
    }
});
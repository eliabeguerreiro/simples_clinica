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
    window.location.href = '../evlt/escolher_forms.php?paciente_id=' + pacienteId;
}

// Função para redirecionar para o módulo de evoluções
function redirectToEvolucoes(pacienteId) {
    // Implementação do redirecionamento para evoluções
    window.location.href = '../evlt/?paciente=' + pacienteId;
}
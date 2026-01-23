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

// Máscaras e validações em tempo real
$(document).ready(function(){
    // Máscaras
    $('#telefone').mask('(00) 00000-0000');
    $('#cep').mask('00000-000');
    $('#cns').mask('000 0000 0000 0000');

    // Impedir letras em campos numéricos
    $('#numero, #cep, #telefone, #cns').on('input', function() {
        this.value = this.value.replace(/[^0-9\s]/g, '');
    });

    // Garantir teclado numérico em dispositivos móveis
    $('#numero, #cep, #telefone').attr('inputmode', 'numeric').attr('pattern', '[0-9]*');
});

// Editar paciente
function editarPaciente(id) {
    showSubTab('pacientes', 'edicao', document.querySelector(`[data-main="pacientes"][data-sub="edicao"]`));
    const url = new URL(window.location.href);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
}

// Fechar modal
function fecharModal() {
    const modal = document.getElementById('modal-exclusao');
    if (modal) {
        modal.style.display = 'none';
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

// Abrir evolução
function abrirEvolucao(pacienteId) {
    window.location.href = '../evlt/escolher_forms.php?paciente_id=' + pacienteId;
}

// Modal de exclusão personalizado (sem confirm() nativo)
function confirmarExclusao(pacienteId) {
    const modal = document.getElementById('modal-exclusao');
    if (!modal) return;

    const btnConfirmar = document.getElementById('confirmar-exclusao');
    if (btnConfirmar) {
        // Remove evento anterior para evitar duplicação
        const novoBotao = btnConfirmar.cloneNode(true);
        btnConfirmar.parentNode.replaceChild(novoBotao, btnConfirmar);
        novoBotao.addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            const inputAcao = document.createElement('input');
            inputAcao.type = 'hidden';
            inputAcao.name = 'acao';
            inputAcao.value = 'excluir';

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.value = pacienteId;

            form.appendChild(inputAcao);
            form.appendChild(inputId);
            document.body.appendChild(form);
            form.submit();
        });
    }

    modal.style.display = 'flex';
}

// Atualiza o texto da sub-aba "Listagem" para "Detalhes do Paciente"
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('titulo-paciente')) {
        const botaoListagem = document.querySelector('#sub-pacientes .tab-btn[data-sub="documentos"]');
        if (botaoListagem) {
            botaoListagem.textContent = 'Detalhes do Paciente';
        }
    }
});
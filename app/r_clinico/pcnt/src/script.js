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

// Editar paciente (implementação básica)
function editarPaciente(id) {
    // Mostra a aba de edição
    showSubTab('pacientes', 'edicao', document.querySelector(`[data-main="pacientes"][data-sub="edicao"]`));
    
    // Atualiza a URL para manter o estado (opcional, mas útil)
    const url = new URL(window.location.href);
    url.searchParams.set('id', id);
    window.history.pushState({}, '', url);
}

// Função para fechar o modal de exclusão
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

// Função para abrir evolução do paciente
function abrirEvolucao(pacienteId) {
    window.location.href = '../evlt/escolher_forms.php?paciente_id=' + pacienteId;
}

// Função para redirecionar para o módulo de evoluções
function redirectToEvolucoes(pacienteId) {
    window.location.href = '../evlt/?paciente=' + pacienteId;
}

// Atualiza o texto da sub-aba "Listagem" para "Detalhes do Paciente", se estiver visualizando um paciente
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('titulo-paciente')) {
        const botaoListagem = document.querySelector('#sub-pacientes .tab-btn[data-sub="documentos"]');
        if (botaoListagem) {
            botaoListagem.textContent = 'Detalhes do Paciente';
        }
    }
});

// ✅ Nova função: confirmar exclusão de paciente
// Função para abrir o modal de exclusão
function confirmarExclusao(pacienteId) {
    const modal = document.getElementById('modal-exclusao');
    if (!modal) return;

    // Armazena o ID do paciente no botão de confirmação
    const btnConfirmar = document.getElementById('confirmar-exclusao');
    if (btnConfirmar) {
        // Remove evento anterior (evita duplicação)
        const novoBotao = btnConfirmar.cloneNode(true);
        btnConfirmar.parentNode.replaceChild(novoBotao, btnConfirmar);
        novoBotao.addEventListener('click', function() {
            // Cria e envia formulário de exclusão
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

    // Exibe o modal
    modal.style.display = 'flex';
}
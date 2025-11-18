// Redireciona para outro módulo
function redirectToTab(tabId) {
    let destino = '';
    if (tabId === 'usuarios') destino = 'usuarios';
    if (tabId === 'perfis') destino = 'perfis';
    if (tabId === 'pacientes') destino = '../pcnt';
    if (tabId === 'atendimentos') destino = '../atndm';
    if (tabId === 'evolucoes') destino = '../evlt';
    if (destino) {
        window.location.href = destino + '/';
    }
}

// Mostra sub-aba e oculta as outras
function showSubTab(mainId, subId, clickedButton) {
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });
    const activeButtons = document.querySelectorAll(`#sub-${mainId} .tab-btn`);
    activeButtons.forEach(btn => btn.classList.remove('active'));
    if (clickedButton) {
        clickedButton.classList.add('active');
    }
    const content = document.getElementById(`${mainId}-${subId}`);
    if (content) {
        content.style.display = 'block';
    }
}

// Confirmação de DESATIVAÇÃO
let usuarioParaDesativar = null;

function confirmarDesativacao(id) {
    usuarioParaDesativar = id;
    document.getElementById('modal-exclusao').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modal-exclusao').style.display = 'none';
    usuarioParaDesativar = null;
}

// Confirma desativação via POST
document.addEventListener('DOMContentLoaded', function () {
    const confirmarBtn = document.getElementById('confirmar-exclusao');
    if (confirmarBtn) {
        confirmarBtn.textContent = 'Desativar';
        confirmarBtn.addEventListener('click', function () {
            if (usuarioParaDesativar) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                const inputAcao = document.createElement('input');
                inputAcao.type = 'hidden';
                inputAcao.name = 'acao';
                inputAcao.value = 'desativar';

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = usuarioParaDesativar;

                form.appendChild(inputAcao);
                form.appendChild(inputId);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});

// Fechar modal ao clicar fora
document.addEventListener('click', function (e) {
    const modal = document.getElementById('modal-exclusao');
    if (modal && e.target === modal) {
        fecharModal();
    }
});

// Fechar com ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        fecharModal();
    }
});
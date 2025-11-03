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
    // Oculta todos os conteúdos das abas
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });

    // Remove classe 'active' de todos os botões da sub-aba
    const activeButtons = document.querySelectorAll(`#sub-${mainId} .tab-btn`);
    activeButtons.forEach(btn => btn.classList.remove('active'));

    // Ativa o botão clicado
    if (clickedButton) {
        clickedButton.classList.add('active');
    }

    // Mostra o conteúdo correspondente
    const content = document.getElementById(`${mainId}-${subId}`);
    if (content) {
        content.style.display = 'block';
    }
}

// Função para editar usuário (redireciona para página de edição)
function editarUsuario(id) {
    window.location.href = 'atualizar_usuario.php?id=' + id;
}

// Função para confirmar exclusão com modal
let usuarioParaExcluir = null;

function confirmarExclusao(id) {
    usuarioParaExcluir = id;
    document.getElementById('modal-exclusao').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modal-exclusao').style.display = 'none';
    usuarioParaExcluir = null;
}

// Confirma exclusão no modal
document.addEventListener('DOMContentLoaded', function() {
    const confirmarBtn = document.getElementById('confirmar-exclusao');
    if (confirmarBtn) {
        confirmarBtn.addEventListener('click', function() {
            if (usuarioParaExcluir) {
                window.location.href = 'apagar.php?id=' + usuarioParaExcluir;
            }
        });
    }
});

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modal-exclusao');
    if (modal && e.target === modal) {
        fecharModal();
    }
});

// Fechar modal com tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal();
    }
});
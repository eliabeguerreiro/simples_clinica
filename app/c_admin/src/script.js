function switchMainTab(tabId, clickedButton) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    clickedButton.classList.add('active');

    document.querySelectorAll('#sub-tabs > div').forEach(el => el.style.display = 'none');
    document.getElementById(`sub-${tabId}`).style.display = 'flex';

    const firstBtn = document.querySelector(`#sub-${tabId} .tab-btn`);
    if (firstBtn) {
        const subId = firstBtn.textContent.trim() === 'Cadastro' ? 'cadastro' :
                      firstBtn.textContent.trim() === 'Novo Perfil' ? 'cadastro' : 'listagem';
        showSubTab(tabId, subId, firstBtn);
    }
}

function showSubTab(mainId, subId, clickedButton) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll(`#sub-${mainId} .tab-btn`).forEach(btn => btn.classList.remove('active'));
    if (clickedButton) clickedButton.classList.add('active');
    const contentId = `${mainId}-${subId}`;
    const content = document.getElementById(contentId);
    if (content) content.style.display = 'block';
}

// Para preencher formulário de edição sem AJAX
function preencherFormEdicaoUsuario(id) {
    // Opcional: carregar via JS ou manter como está (com GET)
    // Aqui mantemos o modelo com abas e dados já carregados
}

function preencherFormEdicaoPerfil(id) {
    const url = new URL(window.location);
    url.searchParams.set('tab', 'perfis');
    url.searchParams.set('sub', 'edicao');
    url.searchParams.set('id_perfil', id);
    window.history.pushState({}, '', url);
    showSubTab('perfis', 'edicao', null);
}

// Desativação de usuário
let usuarioParaDesativar = null;
function confirmarDesativacao(id) {
    usuarioParaDesativar = id;
    document.getElementById('modal-exclusao').style.display = 'flex';
}
function fecharModal() {
    document.getElementById('modal-exclusao').style.display = 'none';
    usuarioParaDesativar = null;
}
document.addEventListener('DOMContentLoaded', function () {
    const confirmBtn = document.getElementById('confirmar-exclusao');
    if (confirmBtn) {
        confirmBtn.textContent = 'Desativar';
        confirmBtn.addEventListener('click', function () {
            if (usuarioParaDesativar) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="acao" value="desativar">
                    <input type="hidden" name="id" value="${usuarioParaDesativar}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});

// Fechar modal
document.addEventListener('click', e => {
    const modal = document.getElementById('modal-exclusao');
    if (modal && e.target === modal) fecharModal();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fecharModal();
});
function switchMainTab(tabId, clickedButton) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    clickedButton.classList.add('active');

    document.querySelectorAll('#sub-tabs > div').forEach(el => el.style.display = 'none');
    document.getElementById(`sub-${tabId}`).style.display = 'flex';

    const firstBtn = document.querySelector(`#sub-${tabId} .tab-btn`);
    if (firstBtn) {
        const subId = firstBtn.dataset.sub || firstBtn.textContent.trim().toLowerCase();
        showSubTab(tabId, subId, firstBtn);
    }
}

function showSubTab(mainId, subId, clickedButton) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll(`#sub-${mainId} .tab-btn`).forEach(btn => btn.classList.remove('active'));
    if (clickedButton) clickedButton.classList.add('active');

    const content = document.getElementById(`${mainId}-${subId}`);
    if (content) content.style.display = 'block';
}

let usuarioParaDesativar = null;
function confirmarDesativacao(id) {
    usuarioParaDesativar = id;
    document.getElementById('modal-exclusao').style.display = 'flex';
}
function fecharModal() {
    document.getElementById('modal-exclusao').style.display = 'none';
    usuarioParaExcluir = null;
}
document.addEventListener('DOMContentLoaded', function () {
    const confirmBtn = document.getElementById('confirmar-exclusao');
    if (confirmBtn) {
        confirmBtn.textContent = 'Desativar';
        confirmBtn.addEventListener('click', function () {
            if (usuarioParaDesativar) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
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
document.addEventListener('click', function (e) {
    const modal = document.getElementById('modal-exclusao');
    if (modal && e.target === modal) fecharModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') fecharModal();
});
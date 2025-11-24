// Redireciona para abas principais
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

// Mostra sub-abas
function showSubTab(mainId, subId, clickedButton) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll(`#sub-${mainId} .tab-btn`).forEach(btn => btn.classList.remove('active'));
    if (clickedButton) clickedButton.classList.add('active');
    const content = document.getElementById(`${mainId}-${subId}`);
    if (content) content.style.display = 'block';
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

    // Acordeão + Toggle mestre (com visibilidade condicional)
    document.querySelectorAll('.master-toggle').forEach(master => {
        const prefix = master.dataset.prefix;
        const content = master.closest('.accordion-item').querySelector('.accordion-content');

        // Inicializa visibilidade com base no estado do toggle
        if (master.checked) {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }

        master.addEventListener('change', function() {
            if (this.checked) {
                content.style.display = 'block';
                // Marca todas
                document.querySelectorAll(`input[name="permissoes[]"][value^="${prefix}."]`).forEach(cb => {
                    cb.checked = true;
                });
            } else {
                content.style.display = 'none';
                // Desmarca todas
                document.querySelectorAll(`input[name="permissoes[]"][value^="${prefix}."]`).forEach(cb => {
                    cb.checked = false;
                });
            }
        });
    });

    // Sincroniza toggle mestre com checkboxes individuais
    document.querySelectorAll('input[name="permissoes[]"]').forEach(cb => {
        cb.addEventListener('change', function() {
            const prefix = this.value.split('.')[0];
            const master = document.querySelector(`.master-toggle[data-prefix="${prefix}"]`);
            const content = master?.closest('.accordion-item')?.querySelector('.accordion-content');
            if (!master || !content) return;

            const checkboxesDoModulo = Array.from(document.querySelectorAll(`input[name="permissoes[]"][value^="${prefix}."]`));
            const todasMarcadas = checkboxesDoModulo.every(c => c.checked);
            const nenhumaMarcada = checkboxesDoModulo.every(c => !c.checked);

            master.checked = todasMarcadas;

            if (todasMarcadas) {
                content.style.display = 'block';
            } else if (nenhumaMarcada) {
                content.style.display = 'none';
            } else {
                // Parcialmente marcado → mantém visível e ativa o mestre
                master.checked = true;
                content.style.display = 'block';
            }
        });
    });
});

// Fecha modal
document.addEventListener('click', e => {
    const modal = document.getElementById('modal-exclusao');
    if (modal && e.target === modal) fecharModal();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fecharModal();
});
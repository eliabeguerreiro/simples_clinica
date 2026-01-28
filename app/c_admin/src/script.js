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
    // Configura o modal para desativação
    document.querySelector('#modal-exclusao .modal-header h3').textContent = 'Confirmar Desativação';
    document.querySelector('#modal-exclusao .modal-body p:first-child').textContent = 'Tem certeza que deseja desativar este usuário?';
    document.querySelector('#modal-exclusao .modal-body p:last-child').innerHTML = '<strong>O usuário perderá acesso ao sistema.</strong>';
    document.getElementById('confirmar-exclusao').textContent = 'Desativar';
    document.getElementById('modal-exclusao').style.display = 'flex';
}

// Exclusão de perfil
let perfilParaExcluir = null;
function confirmarExclusaoPerfil(id) {
    perfilParaExcluir = id;
    // Configura o modal para exclusão de perfil
    document.querySelector('#modal-exclusao .modal-header h3').textContent = 'Confirmar Exclusão de Perfil';
    document.querySelector('#modal-exclusao .modal-body p:first-child').textContent = 'Tem certeza que deseja excluir este perfil?';
    document.querySelector('#modal-exclusao .modal-body p:last-child').innerHTML = '<strong>Usuários vinculados perderão acesso.</strong>';
    document.getElementById('confirmar-exclusao').textContent = 'Excluir';
    document.getElementById('modal-exclusao').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modal-exclusao').style.display = 'none';
    usuarioParaDesativar = null;
    perfilParaExcluir = null;
}

// Reativação de usuário
function confirmarReativacao(id) {
    if (confirm('Deseja reativar este usuário? Ele voltará a ter acesso ao sistema.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        form.innerHTML = `
            <input type="hidden" name="acao" value="reativar">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Inicialização ao carregar a página
document.addEventListener('DOMContentLoaded', function () {
    const confirmBtn = document.getElementById('confirmar-exclusao');
    if (confirmBtn) {
        // Evento genérico: verifica qual ação está pendente
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
            } else if (perfilParaExcluir) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="acao" value="excluir_perfil">
                    <input type="hidden" name="id" value="${perfilParaExcluir}">
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
                // Marca todas as permissões do módulo
                document.querySelectorAll(`input[name="permissoes[]"][value^="${prefix}."]`).forEach(cb => {
                    cb.checked = true;
                });
            } else {
                content.style.display = 'none';
                // Desmarca todas as permissões do módulo
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

// Fecha modal ao clicar fora
document.addEventListener('click', e => {
    const modal = document.getElementById('modal-exclusao');
    if (modal && e.target === modal) fecharModal();
});

// Fecha modal com tecla ESC
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') fecharModal();
});
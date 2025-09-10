// Função para mostrar aba principal
function showMainTab(tabId, clickedButton) {
    // Oculta todas as sub-abas
    document.querySelectorAll('.sub-tabs').forEach(el => {
        el.style.display = 'none';
    });

    // Oculta todos os conteúdos
    document.querySelectorAll('.tab-content').forEach(el => {
        el.style.display = 'none';
    });

    // Remove ativação dos botões principais
    document.querySelectorAll('#main-tabs .tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Ativa a sub-aba correspondente
    const subTabsContainer = document.getElementById(`sub-${tabId}`);
    if (subTabsContainer) {
        subTabsContainer.style.display = 'flex';
    }

    // Ativa o botão principal clicado
    if (clickedButton) {
        clickedButton.classList.add('active');
    }

    // Ativa a primeira sub-aba
    const firstSubBtn = document.querySelector(`#sub-${tabId} .tab-btn`);
    if (firstSubBtn) {
        firstSubBtn.click(); // Dispara o clique na primeira sub-aba
    }
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
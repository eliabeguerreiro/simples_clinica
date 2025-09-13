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

// Funções de movimentação para seletores múltiplos
function moveSelected(from, to) {
    Array.from(from.selectedOptions).forEach(option => to.appendChild(option));
}

function moveAll(from, to) {
    while (from.firstChild) {
        to.appendChild(from.firstChild);
    }
}

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Configura eventos dos seletores múltiplos
    const setupSelectors = function() {
        const selectors = {
            patients: {
                available: document.getElementById('available-patients'),
                selected: document.getElementById('selected-patients'),
                add: document.getElementById('btn-add-patient'),
                remove: document.getElementById('btn-remove-patient'),
                addAll: document.getElementById('btn-add-all-patients'),
                removeAll: document.getElementById('btn-remove-all-patients')
            },
            professionals: {
                available: document.getElementById('available-professionals'),
                selected: document.getElementById('selected-professionals'),
                add: document.getElementById('btn-add-professional'),
                remove: document.getElementById('btn-remove-professional'),
                addAll: document.getElementById('btn-add-all-professionals'),
                removeAll: document.getElementById('btn-remove-all-professionals')
            },
            procedures: {
                available: document.getElementById('available-procedures'),
                selected: document.getElementById('selected-procedures'),
                add: document.getElementById('btn-add-procedure'),
                remove: document.getElementById('btn-remove-procedure'),
                addAll: document.getElementById('btn-add-all-procedures'),
                removeAll: document.getElementById('btn-remove-all-procedures')
            }
        };
        
        // Verifica se os elementos básicos existem
        if (selectors.patients.available && selectors.patients.selected) {
            // Configura eventos para pacientes
            if (selectors.patients.add) selectors.patients.add.onclick = () => moveSelected(selectors.patients.available, selectors.patients.selected);
            if (selectors.patients.remove) selectors.patients.remove.onclick = () => moveSelected(selectors.patients.selected, selectors.patients.available);
            if (selectors.patients.addAll) selectors.patients.addAll.onclick = () => moveAll(selectors.patients.available, selectors.patients.selected);
            if (selectors.patients.removeAll) selectors.patients.removeAll.onclick = () => moveAll(selectors.patients.selected, selectors.patients.available);
        }
        
        // Configura eventos para profissionais
        if (selectors.professionals.available && selectors.professionals.selected) {
            if (selectors.professionals.add) selectors.professionals.add.onclick = () => moveSelected(selectors.professionals.available, selectors.professionals.selected);
            if (selectors.professionals.remove) selectors.professionals.remove.onclick = () => moveSelected(selectors.professionals.selected, selectors.professionals.available);
            if (selectors.professionals.addAll) selectors.professionals.addAll.onclick = () => moveAll(selectors.professionals.available, selectors.professionals.selected);
            if (selectors.professionals.removeAll) selectors.professionals.removeAll.onclick = () => moveAll(selectors.professionals.selected, selectors.professionals.available);
        }
        
        // Configura eventos para procedimentos
        if (selectors.procedures.available && selectors.procedures.selected) {
            if (selectors.procedures.add) selectors.procedures.add.onclick = () => moveSelected(selectors.procedures.available, selectors.procedures.selected);
            if (selectors.procedures.remove) selectors.procedures.remove.onclick = () => moveSelected(selectors.procedures.selected, selectors.procedures.available);
            if (selectors.procedures.addAll) selectors.procedures.addAll.onclick = () => moveAll(selectors.procedures.available, selectors.procedures.selected);
            if (selectors.procedures.removeAll) selectors.procedures.removeAll.onclick = () => moveAll(selectors.procedures.selected, selectors.procedures.available);
        }
        
        // Configura validação do formulário
        const form = document.querySelector('form');
        if (form && selectors.procedures.selected) {
            form.addEventListener('submit', function(e) {
                // Marca todas as opções como selecionadas
                Object.values(selectors).forEach(selectorGroup => {
                    if (selectorGroup.selected) {
                        Array.from(selectorGroup.selected.options).forEach(opt => opt.selected = true);
                    }
                });
                
                // Verifica se há procedimentos selecionados
                if (selectors.procedures.selected.options.length === 0) {
                    e.preventDefault();
                    alert("Selecione pelo menos um procedimento.");
                    return false;
                }
                
                // Mostra overlay de carregamento
                const loadingOverlay = document.getElementById('loading-overlay');
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }
            });
        }
    };
    
    // Configura os seletores
    setupSelectors();
});

// Inicialização com jQuery (para máscaras e funcionalidades adicionais)
$(document).ready(function(){
    // Máscaras para campos (se necessário)
    if ($.fn.mask) {
        // Adicione máscaras específicas se necessário
    }
});
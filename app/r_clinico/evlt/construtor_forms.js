/**
 * Construtor de Formulários - Módulo de Grid e Drag & Drop
 * Versão: 2.0 (com suporte a grid 3 colunas e spans)
 */

document.addEventListener('DOMContentLoaded', function() {
    // ==================== INICIALIZAÇÃO ====================
    const tipoSelect = document.getElementById('tipo_input');
    const opcoesContainer = document.getElementById('opcoes-container');
    const textoContainer = document.getElementById('texto-container');
    const multiplaContainer = document.getElementById('multipla-container');
    const justificativaContainer = document.getElementById('justificativa-container');
    const tabelaContainer = document.getElementById('tabela-container');
    const gridFieldsContainer = document.getElementById('grid-fields-container');
    const ordemContainer = document.getElementById('ordem-container');

    // Estado global
    let currentViewMode = localStorage.getItem('form_builder_view_mode') || 'list';
    let sortableInstance = null;
    const questionsList = document.getElementById('questions-list');

    // ==================== CAMPOS DINÂMICOS DO FORMULÁRIO ====================
    function atualizarCampos() {
        const tipo = tipoSelect ? tipoSelect.value : '';
        
        if (opcoesContainer) opcoesContainer.style.display = 'none';
        if (textoContainer) textoContainer.style.display = 'none';
        if (multiplaContainer) multiplaContainer.style.display = 'none';
        if (justificativaContainer) justificativaContainer.style.display = 'none';
        if (tabelaContainer) tabelaContainer.style.display = 'none';

        // Limpa valores quando não aplicável
        if (tipo && !['radio', 'checkbox', 'select'].includes(tipo)) {
            const opcoesInput = document.getElementById('opcoes');
            if (opcoesInput) opcoesInput.value = '';
        }
        if (tipo && tipo !== 'checkbox') {
            const multiplaInput = document.getElementById('multipla_escolha');
            if (multiplaInput) multiplaInput.value = '0';
        }

        // Mostra campos relevantes
        if (tipo === 'radio' || tipo === 'checkbox' || tipo === 'select') {
            if (opcoesContainer) opcoesContainer.style.display = 'block';
        }
        if (tipo === 'checkbox') {
            if (multiplaContainer) multiplaContainer.style.display = 'block';
        }
        if (tipo === 'texto' || tipo === 'textarea' || tipo === 'number') {
            if (textoContainer) textoContainer.style.display = 'block';
        }
        if (tipo === 'sim_nao_justificativa') {
            if (justificativaContainer) justificativaContainer.style.display = 'block';
        }
        if (tipo === 'tabela') {
            if (tabelaContainer) tabelaContainer.style.display = 'block';
        }
    }

    if (tipoSelect) {
        tipoSelect.addEventListener('change', atualizarCampos);
        atualizarCampos(); // Executa na carga inicial
    }

    // ==================== TOGGLE GRID FIELDS ====================
    window.toggleGridFields = function() {
        const useGridSelect = document.getElementById('use_grid');
        if (!useGridSelect) return;
        
        const useGrid = useGridSelect.value;
        
        if (gridFieldsContainer) {
            gridFieldsContainer.style.display = useGrid === '1' ? 'block' : 'none';
        }
        if (ordemContainer) {
            if (useGrid === '1') {
                ordemContainer.style.opacity = '0.5';
                ordemContainer.style.pointerEvents = 'none';
            } else {
                ordemContainer.style.opacity = '1';
                ordemContainer.style.pointerEvents = 'auto';
            }
        }
    };

    // Inicializa toggle grid
    window.toggleGridFields();

    // ==================== MODO DE VISUALIZAÇÃO (LISTA vs GRID) ====================
    window.switchViewMode = function(mode, btnElement) {
        // Atualiza botões UI
        document.querySelectorAll('.btn-view-mode').forEach(btn => {
            btn.classList.remove('active');
        });
        if (btnElement) {
            btnElement.classList.add('active');
        }

        currentViewMode = mode;
        localStorage.setItem('form_builder_view_mode', mode);

        if (mode === 'grid') {
            enableGridMode();
        } else {
            disableGridMode();
        }
    };

    function enableGridMode() {
        if (!questionsList) return;

        // Adiciona classe de container grid
        questionsList.classList.add('questions-grid-container');

        // Configura cada item como grid-mode
        const items = questionsList.querySelectorAll('.question-item');
        items.forEach(item => {
            item.classList.add('grid-mode');
            
            // Aplica classes de span se existirem nos data attributes
            const colspan = item.dataset.colspan || 1;
            const rowspan = item.dataset.rowspan || 1;
            
            if (colspan > 1) {
                item.classList.add(`col-span-${colspan}`);
            }
            if (rowspan > 1) {
                item.classList.add(`row-span-${rowspan}`);
            }
        });

        // Inicializa SortableJS para drag & drop
        initSortable();
    }

    function disableGridMode() {
        if (!questionsList) return;

        // Remove classe de container grid
        questionsList.classList.remove('questions-grid-container');

        // Remove classes de grid dos itens
        const items = questionsList.querySelectorAll('.question-item');
        items.forEach(item => {
            item.classList.remove('grid-mode');
            item.classList.remove('col-span-2', 'col-span-3');
            item.classList.remove('row-span-2', 'row-span-3');
        });

        // Destroi Sortable
        destroySortable();
    }

    // ==================== SORTABLEJS (DRAG & DROP) ====================
    function initSortable() {
        if (!questionsList || sortableInstance) return;

        sortableInstance = new Sortable(questionsList, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            easing: "cubic-bezier(1, 0, 0, 1)",
            delay: 0,
            delayOnTouchOnly: true,
            touchStartThreshold: 5,
            forceFallback: false,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            invertSwap: false,
            direction: 'vertical', // Importante: mesmo em grid, usamos vertical
            
            // Início do drag
            onStart: function(evt) {
                evt.item.classList.add('dragging');
                document.body.style.cursor = 'grabbing';
            },
            
            // Fim do drag
            onEnd: function(evt) {
                evt.item.classList.remove('dragging');
                document.body.style.cursor = '';
                
                // Calcula e salva nova posição
                handleDragEnd(evt);
            },
            
            // Durante o drag
            onSort: function(evt) {
                // Atualiza visualmente os badges de ordem
                updateOrderBadges();
            }
        });

        console.log('SortableJS inicializado no modo Grid');
    }

    function destroySortable() {
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
            console.log('SortableJS destruído');
        }
    }

    // ==================== CÁLCULO DE POSIÇÃO APÓS DRAG ====================
    function handleDragEnd(evt) {
        const item = evt.item;
        const newIndex = evt.newIndex; // Nova posição no array (0-based)
        const perguntaId = item.dataset.id;
        
        if (!perguntaId) {
            console.error('Item sem data-id');
            return;
        }

        // Calcula coluna e linha baseado no índice
        // Grid de 3 colunas: índices 0,1,2 = linha 1; 3,4,5 = linha 2; etc.
        const gridCol = (newIndex % 3) + 1; // 1, 2, ou 3
        const gridRow = Math.floor(newIndex / 3) + 1; // 1, 2, 3...

        // Atualiza data attributes
        item.dataset.col = gridCol;
        item.dataset.row = gridRow;

        // Envia para o servidor
        atualizarPosicaoGridServidor(perguntaId, gridCol, gridRow, item);
    }

    function atualizarPosicaoGridServidor(perguntaId, gridCol, gridRow, itemElement) {
        // Pega colspan e rowspan atuais
        const colspan = parseInt(itemElement.dataset.colspan) || 1;
        const rowspan = parseInt(itemElement.dataset.rowspan) || 1;

        const formData = new FormData();
        formData.append('acao', 'atualizar_posicao_grid');
        formData.append('pergunta_id', perguntaId);
        formData.append('grid_col', gridCol);
        formData.append('grid_row', gridRow);
        formData.append('grid_colspan', colspan);
        formData.append('grid_rowspan', rowspan);

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                mostrarMensagem('success', 'Posição atualizada: Coluna ' + gridCol + ', Linha ' + gridRow);
                
                // Atualiza badge visual
                const badge = itemElement.querySelector('.ordem-badge');
                if (badge) {
                    badge.textContent = 'G' + gridRow;
                    badge.title = `Grid: C${gridCol} / L${gridRow} (${colspan}x${rowspan})`;
                }
                
                // Atualiza texto informativo se existir
                const gridInfo = itemElement.querySelector('small[color*="primaria"]');
                if (gridInfo) {
                    gridInfo.innerHTML = `<i class="fas fa-th"></i> Grid: C${gridCol} / L${gridRow} (${colspan}x${rowspan})`;
                }
            } else {
                mostrarMensagem('error', data.erro || 'Erro ao atualizar posição');
                // Recarrega para reverter em caso de erro
                setTimeout(() => location.reload(), 1500);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            mostrarMensagem('error', 'Erro de conexão: ' + error.message);
            setTimeout(() => location.reload(), 1500);
        });
    }

    // ==================== ATUALIZAÇÃO DE BADGES ====================
    function updateOrderBadges() {
        const items = questionsList.querySelectorAll('.question-item');
        items.forEach((item, index) => {
            const badge = item.querySelector('.ordem-badge');
            if (badge && currentViewMode === 'grid') {
                const gridRow = Math.floor(index / 3) + 1;
                badge.textContent = 'G' + gridRow;
            }
        });
    }

    // ==================== FUNÇÕES GLOBAIS (para onclick inline) ====================
    
    // Reordenar na lista vertical (botões ↑↓)
    window.reordenarPergunta = function(perguntaId, direcao) {
        const formData = new FormData();
        formData.append('acao', 'reordenar');
        formData.append('pergunta_id', perguntaId);
        formData.append('direcao', direcao);

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                mostrarMensagem('success', data.mensagem);
                setTimeout(() => location.reload(), 800);
            } else {
                mostrarMensagem('error', data.erro || 'Erro ao reordenar.');
            }
        })
        .catch(error => {
            mostrarMensagem('error', 'Erro na requisição: ' + error.message);
        });
    };

    // Modal de exclusão
    window.abrirModalExclusaoPergunta = function(formId, perguntaId, titulo) {
        const modalTitulo = document.getElementById('titulo-pergunta-excluir');
        const btnConfirmar = document.getElementById('btn-confirmar-exclusao');
        const modal = document.getElementById('modal-exclusao-pergunta');
        
        if (modalTitulo) modalTitulo.textContent = titulo;
        if (btnConfirmar) btnConfirmar.href = '?form_id=' + formId + '&excluir=' + perguntaId;
        if (modal) modal.style.display = 'flex';
    };

    window.fecharModalExclusaoPergunta = function() {
        const modal = document.getElementById('modal-exclusao-pergunta');
        if (modal) modal.style.display = 'none';
    };

    // Mensagens temporárias
    window.mostrarMensagem = function(tipo, texto) {
        // Remove mensagens antigas
        const mensagensAntigas = document.querySelectorAll('.ajax-message');
        mensagensAntigas.forEach(msg => msg.remove());
        
        // Cria nova mensagem
        const mensagem = document.createElement('div');
        mensagem.className = `form-message ${tipo === 'success' ? 'success' : 'error'} ajax-message`;
        mensagem.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease, fadeOut 0.5s ease 2.5s forwards;
        `;
        mensagem.textContent = texto;
        
        document.body.appendChild(mensagem);
        
        // Remove após 3 segundos
        setTimeout(() => {
            if (mensagem.parentNode) {
                mensagem.remove();
            }
        }, 3000);
    };

    // ==================== EVENTOS DE MODAL ====================
    // Fechar modal ao clicar fora
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modal-exclusao-pergunta');
        if (modal && e.target === modal) {
            fecharModalExclusaoPergunta();
        }
    });

    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharModalExclusaoPergunta();
        }
    });

    // ==================== INICIALIZAÇÃO DO MODO SALVO ====================
    // Restaura modo de visualização salvo no localStorage
    if (currentViewMode === 'grid') {
        const gridBtn = document.querySelector('.btn-view-mode:nth-child(2)');
        if (gridBtn) {
            // Simula clique no botão Grid
            setTimeout(() => {
                switchViewMode('grid', gridBtn);
            }, 100);
        }
    }

    // ==================== ANIMAÇÕES CSS DINÂMICAS ====================
    // Adiciona estilos de animação dinamicamente se não existirem
    if (!document.getElementById('dynamic-animations')) {
        const style = document.createElement('style');
        style.id = 'dynamic-animations';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; transform: translateX(400px); }
            }
        `;
        document.head.appendChild(style);
    }

    // ==================== DEBUG (opcional - remover em produção) ====================
    console.log('Construtor de Formulários JS carregado');
    console.log('Modo inicial:', currentViewMode);
    console.log('Total de perguntas:', questionsList ? questionsList.querySelectorAll('.question-item').length : 0);
});
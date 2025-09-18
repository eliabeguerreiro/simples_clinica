// Funções específicas para o módulo de formulários

// Função para voltar para evoluções
function voltarParaEvolucoes() {
    window.location.href = '../';
}

// Funções para construção de formulários
let camposFormulario = [];
let templateId = null;

// Mostrar opções de campo conforme tipo selecionado
function mostrarOpcoesCampo() {
    const tipo = document.getElementById('tipo_campo').value;
    const opcoesDiv = document.getElementById('opcoes-campo');
    
    if (['radio', 'checkbox', 'select'].includes(tipo)) {
        opcoesDiv.style.display = 'block';
    } else {
        opcoesDiv.style.display = 'none';
    }
}

// Adicionar campo ao formulário
function adicionarCampo() {
    const tipo = document.getElementById('tipo_campo').value;
    const titulo = document.getElementById('titulo_campo').value;
    const obrigatorio = document.getElementById('campo_obrigatorio').checked;
    
    if (!tipo || !titulo) {
        alert('Preencha todos os campos obrigatórios!');
        return;
    }
    
    const campo = {
        id: Date.now(), // ID temporário
        tipo: tipo,
        titulo: titulo,
        obrigatorio: obrigatorio,
        ordem: camposFormulario.length + 1
    };
    
    // Adiciona opções se for campo de seleção
    if (['radio', 'checkbox', 'select'].includes(tipo)) {
        const opcoesTexto = document.getElementById('opcoes_texto').value;
        campo.opcoes = opcoesTexto.split('\n').filter(op => op.trim() !== '');
    }
    
    camposFormulario.push(campo);
    renderizarCampos();
    
    // Limpa formulário
    document.getElementById('tipo_campo').value = '';
    document.getElementById('titulo_campo').value = '';
    document.getElementById('campo_obrigatorio').checked = false;
    document.getElementById('opcoes-campo').style.display = 'none';
    document.getElementById('opcoes_texto').value = '';
}

// Renderizar campos adicionados
function renderizarCampos() {
    const container = document.getElementById('campos-adicionados');
    if (!container) return;
    
    container.innerHTML = '';
    
    camposFormulario.forEach((campo, index) => {
        const campoDiv = document.createElement('div');
        campoDiv.className = 'campo-preview';
        campoDiv.innerHTML = `
            <div class="campo-header">
                <span class="campo-titulo">${campo.titulo}</span>
                <span class="campo-tipo">(${campo.tipo})</span>
                ${campo.obrigatorio ? '<span class="campo-obrigatorio">Obrigatório</span>' : ''}
                <button type="button" onclick="removerCampo(${index})" class="btn-delete-small">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            ${campo.opcoes ? `
                <div class="campo-opcoes">
                    <strong>Opções:</strong>
                    <ul>
                        ${campo.opcoes.map(op => `<li>${op}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
        `;
        container.appendChild(campoDiv);
    });
}

// Remover campo
function removerCampo(index) {
    camposFormulario.splice(index, 1);
    // Reordenar campos
    camposFormulario.forEach((campo, i) => campo.ordem = i + 1);
    renderizarCampos();
}

// Salvar formulário completo
function salvarFormulario() {
    if (camposFormulario.length === 0) {
        alert('Adicione pelo menos um campo ao formulário!');
        return;
    }
    
    const formData = {
        template_id: templateId,
        campos: camposFormulario
    };
    
    // Aqui você enviaria para o servidor
    console.log('Salvando formulário:', formData);
    alert('Formulário salvo com sucesso!');
}

// Funções de navegação entre seções de formulários
function acessarCriacaoFormulario() {
    window.location.href = '?acao=criar';
}

function acessarGerenciamentoFormularios() {
    window.location.href = '?acao=gerenciar';
}

function acessarAplicacaoFormulario() {
    window.location.href = '?acao=aplicar';
}

// Inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    // Verifica se é página de formulários
    if (window.location.pathname.includes('/forms/')) {
        console.log('Página de formulários carregada');
        // Inicializa funcionalidades específicas de formulários
    }
});

function acessarFormularios() {
    window.location.href = './forms/';
}
function voltarParaEvolucoes() {
    window.location.href = '../';
}

// Funções para o construtor de formulários

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
    const descricao = document.getElementById('descricao_campo').value;
    const obrigatorio = document.getElementById('campo_obrigatorio').checked;
    const multiplaEscolha = document.getElementById('campo_multipla_escolha').checked;
    
    if (!tipo || !titulo) {
        alert('Preencha todos os campos obrigatórios!');
        return;
    }
    
    const campo = {
        id: Date.now(), // ID temporário
        tipo: tipo,
        titulo: titulo,
        descricao: descricao,
        obrigatorio: obrigatorio,
        multipla_escolha: multiplaEscolha,
        ordem: camposFormulario.length + 1
    };
    
    // Adiciona opções se for campo de seleção
    if (['radio', 'checkbox', 'select'].includes(tipo)) {
        const opcoesTexto = document.getElementById('opcoes_texto').value;
        campo.opcoes = opcoesTexto.split('\n').filter(op => op.trim() !== '').map(op => op.trim());
        
        if (campo.opcoes.length === 0) {
            alert('Adicione pelo menos uma opção para este tipo de campo!');
            return;
        }
    }
    
    camposFormulario.push(campo);
    renderizarCampos();
    
    // Limpa formulário
    document.getElementById('tipo_campo').value = '';
    document.getElementById('titulo_campo').value = '';
    document.getElementById('descricao_campo').value = '';
    document.getElementById('campo_obrigatorio').checked = false;
    document.getElementById('campo_multipla_escolha').checked = false;
    document.getElementById('opcoes-campo').style.display = 'none';
    document.getElementById('opcoes_texto').value = '';
    
    // Mostra mensagem de sucesso
    mostrarMensagem('Campo adicionado com sucesso!', 'success');
}

// Renderizar campos adicionados
function renderizarCampos() {
    const container = document.getElementById('campos-adicionados');
    
    if (camposFormulario.length === 0) {
        container.innerHTML = '<div class="no-data">Nenhum campo adicionado ainda.</div>';
        return;
    }
    
    container.innerHTML = '';
    
    camposFormulario.forEach((campo, index) => {
        const campoDiv = document.createElement('div');
        campoDiv.className = 'campo-preview';
        campoDiv.innerHTML = `
            <div class="campo-header">
                <span class="campo-titulo">${campo.titulo}</span>
                <span class="campo-tipo">${campo.tipo}</span>
                ${campo.obrigatorio ? '<span class="campo-obrigatorio">Obrigatório</span>' : ''}
                ${campo.multipla_escolha ? '<span class="campo-multipla">Múltipla</span>' : ''}
                <button type="button" onclick="removerCampo(${index})" class="btn-delete-small">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            ${campo.descricao ? `<div class="campo-descricao"><strong>Descrição:</strong> ${campo.descricao}</div>` : ''}
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
    if (confirm('Tem certeza que deseja excluir este campo?')) {
        camposFormulario.splice(index, 1);
        // Reordenar campos
        camposFormulario.forEach((campo, i) => campo.ordem = i + 1);
        renderizarCampos();
        mostrarMensagem('Campo removido com sucesso!', 'success');
    }
}

// Salvar formulário completo
function salvarFormulario() {
    if (camposFormulario.length === 0) {
        alert('Adicione pelo menos um campo ao formulário!');
        return;
    }
    
    const nomeTemplate = document.getElementById('nome_template').value;
    const areaAtendimento = document.getElementById('area_atendimento').value;
    
    if (!nomeTemplate || !areaAtendimento) {
        alert('Preencha o nome do formulário e a área de atendimento!');
        return;
    }
    
    const formData = {
        template_id: templateId,
        nome: nomeTemplate,
        area_atendimento: areaAtendimento,
        descricao: document.getElementById('descricao_template').value,
        campos: camposFormulario
    };
    
    // Aqui você enviaria para o servidor
    console.log('Salvando formulário:', formData);
    mostrarMensagem('Formulário salvo com sucesso!', 'success');
    
    // Resetar formulário
    camposFormulario = [];
    document.getElementById('form-template').reset();
    document.getElementById('form-builder-area').style.display = 'none';
    renderizarCampos();
}

// Mostrar mensagem de feedback
function mostrarMensagem(mensagem, tipo) {
    const mensagemDiv = document.createElement('div');
    mensagemDiv.className = `form-message ${tipo}`;
    mensagemDiv.innerHTML = `<p>${mensagem}</p>`;
    
    // Adiciona ao início do formulário
    const formContainer = document.querySelector('.form-container');
    if (formContainer) {
        formContainer.insertBefore(mensagemDiv, formContainer.firstChild);
        
        // Remove mensagem após 5 segundos
        setTimeout(() => {
            if (mensagemDiv.parentNode) {
                mensagemDiv.parentNode.removeChild(mensagemDiv);
            }
        }, 5000);
    }
}

// Evento de submit do formulário de template
document.addEventListener('DOMContentLoaded', function() {
    const formTemplate = document.getElementById('form-template');
    if (formTemplate) {
        formTemplate.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nomeTemplate = document.getElementById('nome_template').value;
            const areaAtendimento = document.getElementById('area_atendimento').value;
            
            if (nomeTemplate && areaAtendimento) {
                // Mostra área de construção de campos
                document.querySelector('.form-builder-area').style.display = 'block';
                mostrarMensagem('Template criado com sucesso! Agora adicione os campos.', 'success');
            } else {
                mostrarMensagem('Preencha todos os campos obrigatórios!', 'error');
            }
        });
    }
});
// ==================== AJAX FUNCTIONS ====================

/**
 * Envia formulário via AJAX
 */
function enviarFormularioAjax(formElement, callbackSucesso) {
    const formData = new FormData(formElement);
    const acao = formData.get('acao');
    
    // Mostra loading
    mostrarLoading(true);
    
    fetch('ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mostrarLoading(false);
        
        if (data.sucesso) {
            mostrarMensagem('success', data.mensagem || 'Operação realizada com sucesso!');
            
            // Limpa formulário após cadastro
            if (acao === 'cadastrar') {
                formElement.reset();
            }
            
            // Callback de sucesso
            if (typeof callbackSucesso === 'function') {
                callbackSucesso(data);
            }
            
            // Atualiza listagem após operação
            if (['cadastrar', 'atualizar', 'excluir', 'excluir_multiplos'].includes(acao)) {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } else {
            const erros = data.erros || ['Erro desconhecido'];
            mostrarMensagem('error', erros.join('<br>'));
        }
    })
    .catch(error => {
        mostrarLoading(false);
        mostrarMensagem('error', 'Erro ao processar requisição: ' + error.message);
        console.error('Erro AJAX:', error);
    });
}

/**
 * Mostra/oculta overlay de loading
 */
function mostrarLoading(mostrar) {
    let overlay = document.getElementById('ajax-loading-overlay');
    
    if (mostrar && !overlay) {
        overlay = document.createElement('div');
        overlay.id = 'ajax-loading-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        `;
        
        const spinner = document.createElement('div');
        spinner.style.cssText = `
            border: 4px solid #f3f3f3;
            border-top: 4px solid #6c63ff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        `;
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        
        overlay.appendChild(spinner);
        document.body.appendChild(overlay);
        document.body.appendChild(style);
    } else if (!mostrar && overlay) {
        overlay.remove();
    }
}

/**
 * Mostra mensagem de feedback
 */
function mostrarMensagem(tipo, texto) {
    // Remove mensagens antigas
    const mensagensAntigas = document.querySelectorAll('.ajax-message');
    mensagensAntigas.forEach(msg => msg.remove());
    
    // Cria nova mensagem
    const mensagem = document.createElement('div');
    mensagem.className = `form-message ${tipo === 'success' ? 'success' : 'error'} ajax-message`;
    mensagem.innerHTML = texto;
    mensagem.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        max-width: 400px;
        animation: slideIn 0.3s ease, fadeOut 0.5s ease 2.5s forwards;
    `;
    
    const style = document.createElement('style');
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
    
    document.body.appendChild(mensagem);
    document.body.appendChild(style);
    
    // Remove após 3 segundos
    setTimeout(() => {
        mensagem.remove();
        style.remove();
    }, 3000);
}

/**
 * Manipulador de submissão de formulários
 */
document.addEventListener('DOMContentLoaded', function() {
    // Intercepta submissão de formulários
    const forms = document.querySelectorAll('form[method="POST"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Verifica se é formulário de exclusão (pode ter lógica diferente)
            const acao = this.querySelector('[name="acao"]')?.value;
            if (acao === 'excluir' || acao === 'excluir_multiplos') {
                // Pode ter confirmação antes
                if (confirm('Tem certeza que deseja excluir? Esta ação não pode ser desfeita.')) {
                    enviarFormularioAjax(this);
                }
            } else {
                enviarFormularioAjax(this);
            }
        });
    });
    
    // Botões de exclusão individuais
    const botoesExcluir = document.querySelectorAll('.btn-delete, .btn-delete-multiple');
    botoesExcluir.forEach(botao => {
        botao.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Tem certeza que deseja excluir? Esta ação não pode ser desfeita.')) {
                const pacienteId = this.dataset.id || this.getAttribute('onclick')?.match(/\d+/)?.[0];
                
                if (pacienteId) {
                    fetch('ajax.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `acao=excluir&id=${pacienteId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            mostrarMensagem('success', data.mensagem || 'Paciente excluído com sucesso!');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            mostrarMensagem('error', data.erros?.join('<br>') || 'Erro ao excluir paciente.');
                        }
                    })
                    .catch(error => {
                        mostrarMensagem('error', 'Erro ao processar exclusão.');
                    });
                }
            }
        });
    });
});
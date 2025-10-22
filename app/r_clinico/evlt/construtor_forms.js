document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_input');
    const opcoesContainer = document.getElementById('opcoes-container');
    const textoContainer = document.getElementById('texto-container');
    const multiplaContainer = document.getElementById('multipla-container');
    const justificativaContainer = document.getElementById('justificativa-container');
    const tabelaContainer = document.getElementById('tabela-container');

    function atualizarCampos() {
        const tipo = tipoSelect.value;
        
        opcoesContainer.style.display = 'none';
        textoContainer.style.display = 'none';
        multiplaContainer.style.display = 'none';
        justificativaContainer.style.display = 'none';
        tabelaContainer.style.display = 'none';

        if (!['radio', 'checkbox', 'select'].includes(tipo)) {
            document.getElementById('opcoes').value = '';
        }
        if (tipo !== 'checkbox') {
            document.getElementById('multipla_escolha').value = '0';
        }

        if (tipo === 'radio' || tipo === 'checkbox' || tipo === 'select') {
            opcoesContainer.style.display = 'flex';
        }
        if (tipo === 'checkbox') {
            multiplaContainer.style.display = 'flex';
        }
        if (tipo === 'texto' || tipo === 'textarea' || tipo === 'number') {
            textoContainer.style.display = 'flex';
        }
        if (tipo === 'sim_nao_justificativa') {
            justificativaContainer.style.display = 'flex';
        }
        if (tipo === 'tabela') {
            tabelaContainer.style.display = 'flex';
        }
    }

    if (tipoSelect) {
        tipoSelect.addEventListener('change', atualizarCampos);
        atualizarCampos();
    }
});
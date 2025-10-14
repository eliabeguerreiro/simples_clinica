document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_input');
    const opcoesContainer = document.getElementById('opcoes-container');
    const textoContainer = document.getElementById('texto-container');
    const multiplaContainer = document.getElementById('multipla-container');
    const tabelaContainer = document.getElementById('tabela-container'); // <-- Novo container

    function atualizarCampos() {
        const tipo = tipoSelect.value;
        
        // Esconde todos os containers
        opcoesContainer.style.display = 'none';
        textoContainer.style.display = 'none';
        multiplaContainer.style.display = 'none';
        tabelaContainer.style.display = 'none'; // <-- Esconde o container da tabela

        // Limpa os valores dos campos ao mudar o tipo
        if (!['radio', 'checkbox', 'select'].includes(tipo)) {
            document.getElementById('opcoes').value = '';
        }
        if (tipo !== 'checkbox') {
            document.getElementById('multipla_escolha').value = '0';
        }

        // Mostra os containers conforme o tipo
        if (tipo === 'radio' || tipo === 'checkbox' || tipo === 'select') { 
            opcoesContainer.style.display = 'flex';
        }
        if (tipo === 'checkbox') {
            multiplaContainer.style.display = 'flex';
        }
        if (tipo === 'texto' || tipo === 'textarea' || tipo === 'number') {
            textoContainer.style.display = 'flex';
        }
        if (tipo === 'tabela') { // <-- Mostra o container da tabela
            tabelaContainer.style.display = 'flex';
        }
    }

    if (tipoSelect) {
        tipoSelect.addEventListener('change', atualizarCampos);
        atualizarCampos(); // Executa uma vez ao carregar
    }
});
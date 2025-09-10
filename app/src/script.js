document.addEventListener('DOMContentLoaded', () => {
    const finalizaSubmit = document.getElementById('finalizaSubmit');
    const buscaInput = document.getElementById('busca'); // Campo de busca

    // Evento para o botão "Buscar"
    finalizaSubmit.addEventListener('click', () => {
        const chaveSelect = document.getElementById('chaveSelect').value;
        const busca = buscaInput.value;
        fetch('pesquisa_itens.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'chave=' + encodeURIComponent(chaveSelect) + '&busca=' + encodeURIComponent(busca)
        })
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('produtos');
            tableBody.innerHTML = ''; // Limpar tabela antes de preencher
            data.forEach(item => {
                const row = document.createElement('tr');
                if (item.nr_disponibilidade === 0) {
                    row.innerHTML = `
                        <td>${item.cod_patrimonio}</td>
                        <td>${item.ds_item}</td>
                        <td>${item.familia}</td>
                        <td>${item.movimentacao}</td>
                        <td>${item.usuario}</td>
                        <td><button class="btn btn-success btn-sm atualiza-button" data-id="${item.id_item}">Devolver Item</button></td>  
                    `;
                } else {
                    row.innerHTML = `
                        <td>${item.cod_patrimonio}</td>
                        <td>${item.ds_item}</td>
                        <td>${item.familia}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>Disponivel</td>
                    `;
                }
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));
    });

    // Evento para capturar o Enter no campo de busca
    buscaInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault(); // Impede o recarregamento da página
            finalizaSubmit.click(); // Simula o clique no botão "Buscar"
        }
    });

    // Delegação de eventos para os botões "Devolver Item"
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.btn.btn-success.btn-sm.atualiza-button');
        if (button) {
            const itemId = button.getAttribute('data-id'); // Pegar o ID do item
            fetch('devolver.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(itemId)
            })
            .then(response => {
                if (response.ok) {
                    return response.json(); // Supondo que devolver.php retorna JSON
                } else {
                    throw new Error('Erro ao processar a requisição.');
                }
            })
            .then(data => {
                if (data.success) {
                    alert('Item devolvido com sucesso!');
                    const buttonCell = button.closest('td'); // Encontra a célula do botão
                    buttonCell.textContent = 'Disponivel';   // Altera o conteúdo da célula
                } else {
                    alert('Falha ao devolver o item: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocorreu um erro ao devolver o item.');
            });
        }
    });
});
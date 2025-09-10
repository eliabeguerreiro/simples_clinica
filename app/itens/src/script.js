document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('atualizaModal');
    const itemIdInput = document.getElementById('itemId');
    const novoNomeInput = document.getElementById('novoNome');
    const novaNaturezaSelect = document.getElementById('novaNatureza');

    // Preencher o modal quando o botão "Editar" for clicado
    document.querySelectorAll('.atualiza-button').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const row = this.closest('tr');
            const nomeAtual = row.querySelector('td:nth-child(3)').innerText; // Coluna do nome
            const naturezaAtual = row.querySelector('td:nth-child(4)').innerText; // Coluna da natureza

            itemIdInput.value = id;
            novoNomeInput.value = nomeAtual;
            novaNaturezaSelect.value = naturezaAtual.toLowerCase();
        });
    });

    // Enviar os dados atualizados para o servidor
    document.getElementById('atualizaSubmit').addEventListener('click', function () {
        const id = itemIdInput.value;
        const novoNome = novoNomeInput.value;
        const novaNatureza = novaNaturezaSelect.value;

        fetch('atualizar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                ds_item: novoNome,
                natureza: novaNatureza
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recarregar a página após a atualização
                } else {
                    alert('Erro ao atualizar o item.');
                }
            })
            .catch(error => console.error('Erro:', error));
    });
});
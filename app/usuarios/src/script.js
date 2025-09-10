document.addEventListener("DOMContentLoaded", function () {
    const filtroPrincipal = document.getElementById("filtro_principal");
    const divNatureza = document.getElementById("filtro_natureza");
    const divFamilia = document.getElementById("filtro_familia");

    // Função para alternar a visibilidade das divs
    filtroPrincipal.addEventListener("change", function () {
        const valorSelecionado = filtroPrincipal.value;

        // Oculta ambas as divs inicialmente
        divNatureza.style.display = "none";
        divFamilia.style.display = "none";

        // Exibe a div correspondente à opção selecionada
        if (valorSelecionado === "natureza") {
            divNatureza.style.display = "inline-block";
        } else if (valorSelecionado === "familia") {
            divFamilia.style.display = "inline-block";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const finalizaButtons = document.querySelectorAll('.atualiza-button');
    const modal = new bootstrap.Modal(document.getElementById('atualizaModal')); // Usa o Modal do Bootstrap
    const closeModal = document.querySelector('#atualizaModal .btn-close'); // Fecha a modal pelo botão de fechar
    const finalizaSubmit = document.getElementById('atualizaSubmit');
    let currentItemId;

    // Captura o ID do item e preenche os campos da modal quando o botão "Editar" é clicado
    finalizaButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Obtém o ID do funcionário do atributo data-id
            currentItemId = button.getAttribute('data-id');

            // Encontra o funcionário correspondente na tabela
            const row = button.closest('tr'); // Linha da tabela onde o botão foi clicado
            const nome = row.querySelector('td:nth-child(2)').textContent; // Nome (2ª coluna)
            const contato = row.querySelector('td:nth-child(3)').textContent; // Contato (3ª coluna)
            const nivel = row.querySelector('td:nth-child(4)').textContent; // Nível (4ª coluna)

            // Preenche os campos da modal com os valores do funcionário
            document.getElementById('novoNome').value = nome;
            document.getElementById('novoContato').value = contato;

            // Define o valor selecionado no dropdown de nível de permissão
            const nivelSelect = document.querySelector('#atualizaModal select');
            nivelSelect.value = nivel.trim(); // Remove espaços em branco e define o valor

            // Exibe a modal
            modal.show();
        });
    });

    // Fecha a modal ao clicar no botão de fechar
    closeModal?.addEventListener('click', () => {
        modal.hide();
    });

    // Fecha a modal ao clicar fora dela
    window.onclick = function (event) {
        if (event.target === document.getElementById('atualizaModal')) {
            modal.hide();
        }
    };

    // Envia os dados via AJAX ao clicar no botão "Salvar"
    finalizaSubmit.addEventListener('click', () => {
        const novoNome = document.getElementById('novoNome').value;
        const novoContato = document.getElementById('novoContato').value;
        const novoNivel = document.querySelector('#atualizaModal select').value;

        fetch('atualizar_usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_usuario=${encodeURIComponent(currentItemId)}&nm_usuario=${encodeURIComponent(novoNome)}&nr_contato=${encodeURIComponent(novoContato)}&nv_permissao=${encodeURIComponent(novoNivel)}`
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                modal.hide(); // Fecha a modal após salvar
                window.location.reload(); // Recarrega a página para refletir as mudanças
            })
            .catch(error => console.error('Error:', error));
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const cpfInput = document.getElementById('cpf');

    // Formata o CPF visualmente enquanto o usuário digita
    cpfInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é número
        if (value.length > 11) value = value.slice(0, 11); // Limita a 11 dígitos

        // Aplica a máscara
        if (value.length > 9) {
            value = `${value.slice(0, 3)}.${value.slice(3, 6)}.${value.slice(6, 9)}-${value.slice(9, 11)}`;
        } else if (value.length > 6) {
            value = `${value.slice(0, 3)}.${value.slice(3, 6)}.${value.slice(6, 9)}`;
        } else if (value.length > 3) {
            value = `${value.slice(0, 3)}.${value.slice(3, 6)}`;
        }

        e.target.value = value; // Atualiza o valor do input com a máscara
    });

    // Remove a máscara ao enviar o formulário
    document.querySelector('form').addEventListener('submit', function (e) {
        const rawCpf = cpfInput.value.replace(/\D/g, ''); // Remove pontos e traços
        cpfInput.value = rawCpf; // Define o valor limpo no input
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const contatoInput = document.getElementById('nr_contato');

    // Function to format the contact number
    function formatarContato(value) {
        // Remove all non-numeric characters
        value = value.replace(/\D/g, '');

        // Apply the mask: DDD 9 nnnn-nnnn
        let formatted = '';
        if (value.length > 0) {
            formatted += value.slice(0, 2); // DDD
            if (value.length > 2) {
                formatted += ' ' + value[2]; // Space and 9
                if (value.length > 3) {
                    formatted += value.slice(3, 7); // nnnn
                    if (value.length > 7) {
                        formatted += '-' + value.slice(7); // -nnnn
                    }
                }
            }
        }

        return formatted;
    }

    // Event listener for input changes
    contatoInput.addEventListener('input', (e) => {
        const rawValue = e.target.value; // Original value entered by the user
        const formattedValue = formatarContato(rawValue); // Apply formatting
        e.target.value = formattedValue; // Update the input with the formatted value
    });

    // Remove formatting before submitting the form
    document.querySelector('form').addEventListener('submit', (e) => {
        const rawValue = contatoInput.value; // Get the current value
        const unformattedValue = rawValue.replace(/\D/g, ''); // Remove all non-numeric characters
        contatoInput.value = unformattedValue; // Set the unformatted value for submission
    });
});
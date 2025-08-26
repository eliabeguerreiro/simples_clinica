
        const bairroInput = document.getElementById('bairro');
        bairroInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Formatação de CEP
            const cepInput = document.getElementById('cep');
            cepInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 5) {
                    value = value.substring(0, 5) + '-' + value.substring(5, 8);
                }
                this.value = value;
            });

            // Formatação de telefone
            const telefoneInput = document.getElementById('telefone');
            telefoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 10) {
                    value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + '-' + value.substring(7, 11);
                } else if (value.length > 6) {
                    value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 6) + '-' + value.substring(6, 10);
                } else if (value.length > 2) {
                    value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
                }
                this.value = value;
            });

            // Validação de CNS e CPF (apenas um pode ser preenchido)
            const cnsInput = document.getElementById('cns');
            const cpfInput = document.getElementById('cpf');

            cnsInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    cpfInput.value = '';
                    cpfInput.disabled = true;
                } else {
                    cpfInput.disabled = false;
                }
            });

            cpfInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    cnsInput.value = '';
                    cnsInput.disabled = true;
                } else {
                    cnsInput.disabled = false;
                }
            });
        });

                // Função para mostrar uma seção e ocultar as outras
    function mostrarSeccao(seccao) {
        const secoes = document.querySelectorAll('.conteudo-seccao');
        secoes.forEach(secao => secao.classList.remove('ativa'));
        document.getElementById('secao-' + seccao).classList.add('ativa');
    }

    // Mostra a listagem por padrão ao carregar a página
    window.onload = function() {
        mostrarSeccao('listagem');
    }
<?php
session_start();


require_once "classes/db.class.php";
require_once "classes/painel.class.php";

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dadosPaciente = [
            'nome' => $_POST['nome'],
            'data_nascimento' => $_POST['data_nascimento'],
            'sexo' => $_POST['sexo'],
            'cns' => !empty($_POST['cns']) ? $_POST['cns'] : null,
            'cpf' => !empty($_POST['cpf']) ? $_POST['cpf'] : null,
            'raca_cor' => $_POST['raca_cor'],
            'etnia' => $_POST['etnia'] ?? null,
            'nacionalidade' => $_POST['nacionalidade'],
            'municipio_ibge' => 261160,
            'cep' => preg_replace('/[^0-9]/', '', $_POST['cep']),
            'codigo_logradouro' => $_POST['codigo_logradouro'],
            'endereco' => $_POST['endereco'],
            'numero' => $_POST['numero'],
            'complemento' => !empty($_POST['complemento']) ? $_POST['complemento'] : null,
            'bairro' => mb_strtoupper($_POST['bairro']),
            'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone']),
            'email' => !empty($_POST['email']) ? $_POST['email'] : null,
            'situacao_rua' => $_POST['situacao_rua']
        ];

        if (Painel::SetPaciente($dadosPaciente)) {
            $_SESSION['mensagem'] = "Paciente cadastrado com sucesso!";
            header("Location: pacientes.php");
            exit();
        } else {
            $_SESSION['erro'] = "Erro ao cadastrar paciente.";
            header("Location: pacientes.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao cadastrar paciente: " . $e->getMessage();
           header("Location: cadastro_pacientes.php");
        exit();
    }
}

// Obtém a lista de pacientes para exibição
$pacientes = Painel::GetPacientes();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pacientes - BPA Simplificado</title>
    <link rel="stylesheet" href="styles_cad.css">
</head>
<body>

<?php if (!empty($_SESSION['mensagem'])): ?>
    <div class="alert-container">
        <div class="alert alert-success">
            <?= is_array($_SESSION['mensagem']) ? htmlspecialchars($_SESSION['mensagem']['texto'] ?? $_SESSION['mensagem'][0]) : htmlspecialchars($_SESSION['mensagem']) ?>
        </div>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['erro'])): ?>
    <div class="alert-container">
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['erro']) ?>
        </div>
    </div>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>

    <header>
        <div class="logo">
            <img src="vivenciar_logov2.png" alt="Logo Vivenciar">
        </div>
        <nav>
            <ul>
                <li><a href="../">Inicio</a></li>
                <li><a href="./">Novos atendimentos</a></li>
                <li><a href="atendimentos.php">Atendimentos</a></li>
                <li><a href="pacientes.php">Pacientes</a></li>
                <li><a href="profissionais.php">Profissionais</a></li>
                <li><a href="procedimentos.php">Procedimentos</a></li>
            </ul>
        </nav>
    </header>
    <div class="toggle-buttons">
        <button type="button" onclick="mostrarSeccao('cadastro')">Cadastro</button>
        <button type="button" onclick="mostrarSeccao('listagem')">Listagem</button>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['mensagem'] ?>
            <?php unset($_SESSION['mensagem']); ?>
        </div>
    <?php endif; ?>
<section id="secao-cadastro" class="conteudo-seccao">
    <!-- Aqui vai todo o conteúdo do cadastro -->
    <section class="form-section">
    <h2>Cadastro de Paciente</h2>
    <form action="" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="nome">Nome Completo*</label>
                <input required type="text" id="nome" name="nome" required maxlength="100" placeholder="Digite o nome completo">
            </div>
            <div class="form-group">
                <label for="cns">CNS*</label>
                <input required type="text" id="cns" name="cns" required maxlength="100" placeholder="Digite o CNS">
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data*</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required placeholder="dd/mm/aaaa">
            </div>
            <div class="form-group">
                <label for="raca_cor">Raça/Cor*</label>
                <select required id="raca_cor" name="raca_cor" required>
                    <option value="">Selecionar</option>
                    <option value="01">Branca</option>
                    <option value="02">Preta</option>
                    <option value="03">Parda</option>
                    <option value="04">Amarela</option>
                    <option value="05">Indígena</option>
                    <option value="99">Sem informação</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sexo">Sexo*</label>
                <select required id="sexo" name="sexo" required>
                    <option value="">Selecionar</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                </select>
            </div>
            <div class="form-group">
                <label for="etnia">Etnia</label>
                <input type="text" id="etnia" name="etnia" maxlength="4" placeholder="Selecionar">
            </div>
            <div class="form-group">
                <label for="nacionalidade">Nacionalidade*</label>
                <select required id="nacionalidade" name="nacionalidade" required>
                    <option value="">Selecionar</option>
                    <option value="10">Brasileira</option>
                    <option value="20">Naturalizado</option>
                    <option value="30">Estrangeiro</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="codigo_logradouro">Tipo do Logradouro*</label>
                <select required id="codigo_logradouro" name="codigo_logradouro" required>
                    <option value="">Selecionar</option>
                    <option value='81'>Rua</option>
                    <option value="8">Avenida</option>
                </select>
            </div>  
        </div>  
        <div class="form-group">
                <label for="endereco">Logradouro*</label>
                <input required type="text" id="endereco" name="endereco" required maxlength="100" placeholder="Digite o logradouro">
            </div>
            <div class="form-group">
                <label for="numero">Número*</label>
                <input required type="text" id="numero" name="numero" required maxlength="10" placeholder="Digite o número">
            </div>
            <div class="form-group">
                <label for="complemento">Complemento</label>
                <input type="text" id="complemento" name="complemento" maxlength="30" placeholder="Ex: Apt 101">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bairro">Bairro*</label>
                <input required type="text" id="bairro" name="bairro" required maxlength="60" placeholder="Informe o bairro">
            </div>
            <div class="form-group">
                <label for="cep">CEP*</label>
                <input required type="text" id="cep" name="cep" required maxlength="9" placeholder="00000-000">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone*</label>
                <input required type="text" id="telefone" name="telefone" required maxlength="15" placeholder="(00) 00000-0000">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="situacao_rua">Situação de Rua?</label>
                <select required id="situacao_rua" name="situacao_rua" required>
                    <option value="N">Não</option>
                    <option value="S">Sim</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn-add">Salvar</button>
</form>
    </section>
</section>

<section id="secao-listagem" class="conteudo-seccao">
    <!-- Aqui vai toda a listagem -->
    <section class="patients-list">
    <h2>Pacientes Cadastrados</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data de Nascimento</th>
                    <th>CNS/CPF</th>
                    <th>Telefone</th>
                    <th>Bairro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes['dados'] as $paciente): ?>
                    <tr>
                        <td><?= htmlspecialchars($paciente['nome']) ?></td>
                        <td><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></td>
                        <td>
                            <?php if (!empty($paciente['cns'])): ?>
                                CNS: <?= htmlspecialchars($paciente['cns']) ?>
                            <?php elseif (!empty($paciente['cpf'])): ?>
                                CPF: <?= htmlspecialchars($paciente['cpf']) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($paciente['telefone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($paciente['bairro'] ?? '-') ?></td>
                        <td class="actions">
                            <!--a href="editar_paciente.php?id=<?= $paciente['id'] ?>" class="btn-edit">Editar</a-->
                            <a href="excluir_paciente.php?id=<?= $paciente['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este paciente?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
</section> 
    <script>

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
    </script>
</body>
</html>

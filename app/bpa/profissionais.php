<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

require_once "classes/db.class.php";
require_once "classes/painel.class.php";

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dadosProfissional = [
            'nome' => $_POST['nome'],
            'cpf' => !empty($_POST['cpf']) ? preg_replace('/[^0-9]/', '', $_POST['cpf']) : null,
            'cns' => !empty($_POST['cns']) ? $_POST['cns'] : null,
            'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone']),
            'email' => !empty($_POST['email']) ? $_POST['email'] : null,
            'especialidade' => $_POST['especialidade'],
            'cbo' => !empty($_POST['cbo']) ? $_POST['cbo'] : null
        ];

        //var_dump($dadosProfissional); // Debug: Verifica os dados recebidos
        if (Painel::SetProfissional($dadosProfissional)) {
            $_SESSION['mensagem'] = "Profissional cadastrado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao cadastrar profissional.";
        }

    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao processar formulário: " . $e->getMessage();
    }

    header("Location: profissionais.php");
    exit();
}

$profissionais = Painel::GetProfissionais();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Profissionais - BPA Simplificado</title>
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

<?php if (isset($_SESSION['erro'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['erro'] ?>
        <?php unset($_SESSION['erro']); ?>
    </div>
<?php endif; ?>
<section id="secao-cadastro" class="conteudo-seccao">
    <!-- Aqui vai todo o conteúdo do cadastro -->
    <section class="form-section">
    <h2>Cadastro de Profissional</h2>
    <form action="" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="nome">Nome Completo*</label>
                <input type="text" id="nome" name="nome" required maxlength="100" placeholder="Digite o nome completo">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" maxlength="14" placeholder="000.000.000-00">
            </div>
            <div class="form-group">
                <label for="cns">CNS</label>
                <input type="text" id="cns" name="cns" maxlength="15" placeholder="Digite o CNS">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone*</label>
                <input type="text" id="telefone" name="telefone" required maxlength="15" placeholder="(00) 00000-0000">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="50" placeholder="exemplo@email.com">
            </div>
            <div class="form-group">
                <label for="especialidade">Especialidade*</label>
                <select id="especialidade" name="especialidade" required>
                    <option value="">Selecionar</option>
                    <option value="TERAPIA OCUPACIONAL">TERAPIA OCUPACIONAL</option>
                    <option value="FISIOTERAPIA">FISIOTERAPIA</option>
                    <option value="PSICOLOGIA">PSICOLOGIA</option>
                    <option value="FONOAUDIOLOGIA">FONOAUDIOLOGIA</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cbo">CBO</label>
                <input type="text" id="cbo" name="cbo" maxlength="6" placeholder="">
            </div>
        </div>

        <button type="submit" class="btn-add">Salvar</button>
</form>
    </section>
</section>


<section id="secao-listagem" class="conteudo-seccao">
    <!-- Aqui vai toda a listagem -->
    <section class="patients-list">
    <h2>Profissionais Cadastrados</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Especialidade</th>
                    <th>CPF/CNS</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profissionais['dados'] as $profissional): ?>
                    <tr>
                        <td><?= htmlspecialchars($profissional['nome']) ?></td>
                        <td><?= htmlspecialchars($profissional['especialidade'] ?? '-') ?></td>
                        <td>
                            <?php if (!empty($profissional['cpf'])): ?>
                                CPF: <?= htmlspecialchars($profissional['cpf']) ?>
                            <?php elseif (!empty($profissional['cns'])): ?>
                                CNS: <?= htmlspecialchars($profissional['cns']) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($profissional['telefone'] ?? '-') ?></td>
                        <td class="actions">
                            <!--a href="editar_profissional.php?id=<?= $profissional['id'] ?>" class="btn-edit">Editar</a-->
                            <a href="excluir_profissional.php?id=<?= $profissional['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este profissional?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Formatação de CPF
        const cpfInput = document.getElementById('cpf');
        cpfInput.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 9) {
                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9) + '-' + value.substring(9, 11);
            } else if (value.length > 6) {
                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9);
            } else if (value.length > 3) {
                value = value.substring(0, 3) + '.' + value.substring(3, 6);
            }
            this.value = value;
        });

        // Formatação de Telefone
        const telefoneInput = document.getElementById('telefone');
        telefoneInput.addEventListener('input', function (e) {
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
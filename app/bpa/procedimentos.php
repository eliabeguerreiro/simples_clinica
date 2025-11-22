<?php
session_start();

require_once "classes/db.class.php";
require_once "classes/painel.class.php";

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dadosProcedimento = [
            'codigo' => $_POST['codigo'],
            'descricao' => $_POST['descricao'],
            'especialidade' => $_POST['especialidade'],
            'ativo' => $_POST['ativo'],
            'servico' => $_POST['servico'],
            'classificacao' => $_POST['classificacao'],
            'caracter_atendimento' => $_POST['caracter_atendimento']
        ];

        if (Painel::SetProcedimento($dadosProcedimento)) {
            $_SESSION['mensagem'] = "Procedimento cadastrado com sucesso!";
            header("Location: procedimentos.php");
            exit();
        } else {
            $_SESSION['erro'] = "Erro ao cadastrar procedimento.";
            header("Location: procedimentos.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao cadastrar procedimento: " . $e->getMessage();
        header("Location: procedimentos.php");
        exit();
    }
}

// Obtém a lista de procedimentos para exibição
$procedimentos = Painel::GetProcedimentos();
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
                <label for="codigo">Código*</label>
                <input required type="text" id="codigo" name="codigo" required maxlength="100" placeholder="Digite o código">
            </div>
            <div class="form-group">
                <label for="descricao">Descrição*</label>
                <input required type="text" id="descricao" name="descricao" required maxlength="100" placeholder="Digite a descrição">
            </div>
            <div class="form-group">
                <label for="especialidade">Especialidade*</label>
                <input type="text" id="especialidade" name="especialidade" required maxlength="100" placeholder="Digite a especialidade">
            </div>
             <div class="form-group">
                <label for="servico">Serviço*</label>
                <input type="text" id="servico" name="servico" required maxlength="100" placeholder="Digite o serviço">
            </div>
             <div class="form-group">
                <label for="classificacao">Classificação*</label>
                <select required id="classificacao" name="classificacao" required>
                    <option selected value="10">10</option>
                    <option value="11">11</option>
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
    <h2>Procedimentos</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Descrição</th>
                    <th>Especialidade</th>
                    <th>Serviço</th>
                    <th>Classificação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($procedimentos['dados'] as $procedimento): ?>
                    <tr>
                        <td><?= htmlspecialchars($procedimento['codigo']) ?></td>
                        <td><?= htmlspecialchars($procedimento['descricao']) ?></td>
                        <td><?= htmlspecialchars($procedimento['especialidade']) ?></td>
                        <td><?= htmlspecialchars($procedimento['servico']) ?></td>
                        <td><?= htmlspecialchars($procedimento['classificacao']) ?></td>
                        <td class="actions">
                            <!--a href="editar_paciente.php?id=<?= $paciente['id'] ?>" class="btn-edit">Editar</a-->
                            <a href="desativar_procedimento.php?id=<?= $procedimento['id'] ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja excluir este procedimento?')">Desativar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
</section> 
    <script>
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

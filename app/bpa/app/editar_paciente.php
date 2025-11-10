<?php
session_start();
include_once "classes/db.class.php";
include_once "classes/painel.class.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dados = [
            'id' => $_POST['id'],
            'nome' => $_POST['nome'],
            'data_nascimento' => $_POST['data_nascimento'],
            'sexo' => $_POST['sexo'],
            'cns' => !empty($_POST['cns']) ? $_POST['cns'] : null,
            'cpf' => !empty($_POST['cpf']) ? $_POST['cpf'] : null,
            'raca_cor' => $_POST['raca_cor'],
            'etnia' => !empty($_POST['etnia']) ? $_POST['etnia'] : null,
            'nacionalidade' => $_POST['nacionalidade'],
            'municipio_ibge' => 261160,
            'cep' => $_POST['cep'],
            'codigo_logradouro' => $_POST['codigo_logradouro'],
            'endereco' => $_POST['endereco'],
            'numero' => $_POST['numero'],
            'complemento' => !empty($_POST['complemento']) ? $_POST['complemento'] : null,
            'bairro' => $_POST['bairro'],
            'telefone' => $_POST['telefone'],
            'email' => !empty($_POST['email']) ? $_POST['email'] : null,
            'situacao_rua' => $_POST['situacao_rua']
        ];

        if (Painel::UpdatePaciente($dados)) {
            $_SESSION['mensagem'] = "Paciente atualizado com sucesso!";
        } else {
            throw new Exception("Erro ao atualizar paciente.");
        }

    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao atualizar paciente: " . $e->getMessage();
    }

    header("Location: pacientes.php");
    exit;
}

// Verifica se o ID foi passado pela URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID do paciente inválido.";
    header("Location: pacientes.php");
    exit();
}

$id = intval($_GET['id']);
$paciente = Painel::GetPacienteById($id);

if (!$paciente) {
    $_SESSION['erro'] = "Paciente não encontrado.";
    header("Location: pacientes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente - BPA Simplificado</title>
    <link rel="stylesheet" href="styles_cad.css">
</head>
<body>

<?php if (!empty($_SESSION['mensagem'])): ?>
    <div class="alert-container">
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['mensagem']) ?>
        </div>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
<?php elseif (!empty($_SESSION['erro'])): ?>
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
            <li><a href="/pacientes.php">Voltar</a></li>
        </ul>
    </nav>
</header>

<section class="form-section">
    <h2>Editar dados de <?= htmlspecialchars($paciente['nome']) ?></h2>
    <form action="" method="POST">
        <!-- ID oculto -->
        <input type="hidden" name="id" value="<?= $paciente['id'] ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="nome">Nome Completo*</label>
                <input required type="text" id="nome" name="nome" value="<?= htmlspecialchars($paciente['nome']) ?>">
            </div>
            <div class="form-group">
                <label for="cns">CNS</label>
                <input type="text" id="cns" name="cns" value="<?= htmlspecialchars($paciente['cns'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data*</label>
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?= date('Y-m-d', strtotime($paciente['data_nascimento'])) ?>">
            </div>
            <div class="form-group">
                <label for="raca_cor">Raça/Cor*</label>
                <select required id="raca_cor" name="raca_cor">
                    <option value="">Selecionar</option>
                    <option value="01" <?= ($paciente['raca_cor'] == '01') ? 'selected' : '' ?>>Branca</option>
                    <option value="02" <?= ($paciente['raca_cor'] == '02') ? 'selected' : '' ?>>Preta</option>
                    <option value="03" <?= ($paciente['raca_cor'] == '03') ? 'selected' : '' ?>>Parda</option>
                    <option value="04" <?= ($paciente['raca_cor'] == '04') ? 'selected' : '' ?>>Amarela</option>
                    <option value="05" <?= ($paciente['raca_cor'] == '05') ? 'selected' : '' ?>>Indígena</option>
                    <option value="99" <?= ($paciente['raca_cor'] == '99') ? 'selected' : '' ?>>Sem informação</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sexo">Sexo*</label>
                <select required id="sexo" name="sexo">
                    <option value="">Selecionar</option>
                    <option value="M" <?= ($paciente['sexo'] == 'M') ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= ($paciente['sexo'] == 'F') ? 'selected' : '' ?>>Feminino</option>
                </select>
            </div>
            <div class="form-group">
                <label for="etnia">Etnia</label>
                <input type="text" id="etnia" name="etnia" value="<?= htmlspecialchars($paciente['etnia'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="nacionalidade">Nacionalidade*</label>
                <select required id="nacionalidade" name="nacionalidade">
                    <option value="">Selecionar</option>
                    <option value="10" <?= ($paciente['nacionalidade'] == '10') ? 'selected' : '' ?>>Brasileira</option>
                    <option value="20" <?= ($paciente['nacionalidade'] == '20') ? 'selected' : '' ?>>Naturalizado</option>
                    <option value="30" <?= ($paciente['nacionalidade'] == '30') ? 'selected' : '' ?>>Estrangeiro</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="codigo_logradouro">Tipo do Logradouro*</label>
                <select required id="codigo_logradouro" name="codigo_logradouro">
                    <option value="">Selecionar</option>
                    <option value="81" <?= ($paciente['codigo_logradouro'] == '81') ? 'selected' : '' ?>>Rua</option>
                    <option value="8" <?= ($paciente['codigo_logradouro'] == '8') ? 'selected' : '' ?>>Avenida</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="endereco">Logradouro*</label>
                <input required type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($paciente['endereco']) ?>">
            </div>
            <div class="form-group">
                <label for="numero">Número*</label>
                <input required type="text" id="numero" name="numero" value="<?= htmlspecialchars($paciente['numero']) ?>">
            </div>
            <div class="form-group">
                <label for="complemento">Complemento</label>
                <input type="text" id="complemento" name="complemento" value="<?= htmlspecialchars($paciente['complemento'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bairro">Bairro*</label>
                <input required type="text" id="bairro" name="bairro" value="<?= htmlspecialchars($paciente['bairro']) ?>">
            </div>
            <div class="form-group">
                <label for="cep">CEP*</label>
                <input required type="text" id="cep" name="cep" value="<?= htmlspecialchars($paciente['cep']) ?>">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone*</label>
                <input required type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($paciente['telefone']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($paciente['email'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="situacao_rua">Situação de Rua?</label>
                <select id="situacao_rua" name="situacao_rua">
                    <option value="N" <?= ($paciente['situacao_rua'] == 'N') ? 'selected' : '' ?>>Não</option>
                    <option value="S" <?= ($paciente['situacao_rua'] == 'S') ? 'selected' : '' ?>>Sim</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-add">Atualizar Paciente</button>
    </form>
</section>

</body>
</html>
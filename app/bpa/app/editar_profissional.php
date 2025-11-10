<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

require_once "classes/db.class.php";
require_once "classes/painel.class.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dados = [
            'id' => $_POST['id'],
            'nome' => trim($_POST['nome']),
            'cpf' => !empty($_POST['cpf']) ? preg_replace('/[^0-9]/', '', $_POST['cpf']) : null,
            'cns' => !empty($_POST['cns']) ? trim($_POST['cns']) : null,
            'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone']),
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'especialidade' => $_POST['especialidade'],
            'cbo' => !empty($_POST['cbo']) ? trim($_POST['cbo']) : null
        ];

        if (Painel::UpdateProfissional($dados)) {
            $_SESSION['mensagem'] = "Profissional atualizado com sucesso!";
        } else {
            throw new Exception("Erro ao atualizar profissional.");
        }

    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro ao atualizar profissional: " . $e->getMessage();
    }

    header("Location: profissionais.php");
    exit;
}

// Verifica se o ID foi passado pela URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID do paciente inválido.";
    header("Location: pacientes.php");
    exit();
}

$id = intval($_GET['id']);
$profissional = Painel::GetProfissionalById($id);

if (!$profissional) {
    $_SESSION['erro'] = "Profissional não encontrado.";
    header("Location: profissionais.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Profissional - BPA Simplificado</title>
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
            <li><a href="profissionais.php">Voltar</a></li>
        </ul>
    </nav>
</header>

<section class="form-section">
    <h2>Editar Profissional - <?= htmlspecialchars($profissional['nome']) ?></h2>
    <form action="" method="POST">
        <!-- ID oculto -->
        <input type="hidden" name="id" value="<?= $profissional['id'] ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="nome">Nome Completo*</label>
                <input required type="text" id="nome" name="nome" value="<?= htmlspecialchars($profissional['nome']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($profissional['cpf'] ?? '') ?>" maxlength="14" placeholder="000.000.000-00">
            </div>
            <div class="form-group">
                <label for="cns">CNS</label>
                <input type="text" id="cns" name="cns" value="<?= htmlspecialchars($profissional['cns'] ?? '') ?>" maxlength="15" placeholder="Digite o CNS">
            </div>
            <div class="form-group">
                <label for="telefone">Telefone*</label>
                <input required type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($profissional['telefone']) ?>" maxlength="15" placeholder="(00) 00000-0000">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($profissional['email'] ?? '') ?>" maxlength="50" placeholder="exemplo@email.com">
            </div>
            <div class="form-group">
                <label for="especialidade">Especialidade*</label>
                <select required id="especialidade" name="especialidade">
                    <option value="">Selecionar</option>
                    <option value="TERAPIA OCUPACIONAL" <?= ($profissional['especialidade'] == 'TERAPIA OCUPACIONAL') ? 'selected' : '' ?>>TERAPIA OCUPACIONAL</option>
                    <option value="FISIOTERAPIA" <?= ($profissional['especialidade'] == 'FISIOTERAPIA') ? 'selected' : '' ?>>FISIOTERAPIA</option>
                    <option value="PSICOLOGIA" <?= ($profissional['especialidade'] == 'PSICOLOGIA') ? 'selected' : '' ?>>PSICOLOGIA</option>
                    <option value="FONOAUDIOLOGIA" <?= ($profissional['especialidade'] == 'FONOAUDIOLOGIA') ? 'selected' : '' ?>>FONOAUDIOLOGIA</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cbo">CBO</label>
                <input type="text" id="cbo" name="cbo" value="<?= htmlspecialchars($profissional['cbo'] ?? '') ?>" maxlength="6" placeholder="Ex: 2251">
            </div>
        </div>

        <button type="submit" class="btn-add">Atualizar Profissional</button>
    </form>
</section>

<script>
    // Formatação automática do CPF
    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.getElementById('cpf');
        if (cpfInput) {
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
        }

        // Formatação automática do telefone
        const telefoneInput = document.getElementById('telefone');
        if (telefoneInput) {
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
        }
    });
</script>

</body>
</html>
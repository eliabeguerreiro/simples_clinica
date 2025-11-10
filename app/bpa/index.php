<?php
session_start();
/*
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}
*/
include_once "./classes/painel.class.php";
include_once "./classes/db.class.php";
$clinica = Painel::GetClinica();
$profissionais = Painel::GetProfissionais();
$pacientes = Painel::GetPacientes();
$procedimentos = Painel::GetProcedimentos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['paciente_id']) || empty($_POST['profissional_id']) || empty($_POST['procedimento_id'])) {
            throw new Exception("Selecione pelo menos um paciente, profissional e procedimento.");
        }

        $baseData = [
            'clinica_id' => 1,
            'competencia' => $_POST['competencia'],
            'data_atendimento' => $_POST['data_atendimento'],
            'cid' => mb_strtoupper($_POST['cid']) ?? null,
            'caracter_atendimento' => $_POST['caracter_atendimento'] ?? null
        ];

        foreach ($_POST['paciente_id'] as $paciente_id) {
            foreach ($_POST['profissional_id'] as $profissional_id) {
                foreach ($_POST['procedimento_id'] as $procedimento_id) {
                    $dados = $baseData;
                    $dados['paciente_id'] = $paciente_id;
                    $dados['profissional_id'] = $profissional_id;
                    $dados['procedimento_id'] = $procedimento_id;

                    if (!Painel::SetAtendimento($dados)) {
                        throw new Exception("Falha ao registrar um dos atendimentos.");
                    }
                }
            }
        }

        $_SESSION['mensagem'] = ['tipo' => 'sucesso', 'texto' => 'Atendimentos registrados com sucesso!'];
    } catch (Exception $e) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => $e->getMessage()];
    }

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vivenciar - Espaço Terapêutico</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if (!empty($_SESSION['mensagem']) && is_array($_SESSION['mensagem'])): ?>
    <?php 
        $tipo = htmlspecialchars($_SESSION['mensagem']['tipo'] ?? 'erro');
        $texto = htmlspecialchars($_SESSION['mensagem']['texto'] ?? 'Mensagem desconhecida.');
    ?>
    <div class="alert-container">
        <div class="alert alert-<?php echo $tipo === 'sucesso' ? 'success' : 'error'; ?>">
            <?= $texto ?>
        </div>
    </div>
    <?php unset($_SESSION['mensagem']); ?>
<?php endif; ?>

<!-- Cabeçalho -->
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

<!-- Overlay de carregamento -->
<div id="loading-overlay">
    <div class="spinner-container">
        <div class="spinner"></div>
        <p>Salvando atendimentos, aguarde...</p>
    </div>
</div>

<!-- Seção de Novo Atendimento -->
<section class="new-appointment">
    <h2>Novo Atendimento BPA-I</h2>
    <form action="" method="POST">
        <!-- Dados Básicos -->
        <div class="form-row">
            <div class="form-group">
                <label for="competencia">Competência (AAAAMM)</label>
                <div class="input-with-button">
                    <input type="text" id="competencia" name="competencia" required pattern="\d{6}" title="Formato: AAAAMM">
                    <button type="button" class="btn-mes-atual" onclick="document.getElementById('competencia').value='<?= date('Ym') ?>'">
                        Usar mês atual
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="data_atendimento">Data do Atendimento</label>
                <input type="date" id="data_atendimento" name="data_atendimento" required>
            </div>
            <br>
            <div class="form-group">
                <label for="cid">CID-10 (Opcional)</label>
                <input type="text" id="cid" name="cid" maxlength="4" placeholder="Ex: F800">
            </div>
        </div>

        <!-- Seleção Múltipla de Pacientes -->
        <div class="form-row form-row-horizontal">
            <div class="form-group">
                <label>Selecionar Pacientes</label>
                <div class="procedure-selector bloco-pacientes">
                    <div class="list-container">
                        <select id="available-patients" multiple size="10">
                            <?php foreach ($pacientes['dados'] as $pac): ?>
                                <option value="<?= htmlspecialchars($pac['id']) ?>">
                                    <?= htmlspecialchars($pac['nome']) ?> - CNS: <?= htmlspecialchars($pac['cns']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="buttons-container">
                        <button type="button" id="btn-add-patient" class="btn-add-patient">▶</button>
                        <button type="button" id="btn-remove-patient" class="btn-remove-patient">◀</button>
                        <button type="button" id="btn-add-all-patients" class="btn-add-all-patients">▶▶</button>
                        <button type="button" id="btn-remove-all-patients" class="btn-remove-all-patients">◀◀</button>
                    </div>
                    <div class="list-container">
                        <select id="selected-patients" name="paciente_id[]" multiple size="10"></select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profissionais -->
        <div class="procedure-row-center">
            <div class="form-group">
                <label>Profissionais</label>
                <div class="procedure-selector bloco-profissionais">
                    <div class="list-container">
                        <select id="available-professionals" multiple size="10">
                            <?php foreach ($profissionais['dados'] as $prof): ?>
                                <option value="<?= htmlspecialchars($prof['id']) ?>">
                                    <?= htmlspecialchars($prof['nome']) ?> - <?= htmlspecialchars($prof['especialidade']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="buttons-container">
                        <button type="button" id="btn-add-professional" class="btn-add-professional">▶</button>
                        <button type="button" id="btn-remove-professional" class="btn-remove-professional">◀</button>
                        <button type="button" id="btn-add-all-professionals" class="btn-add-all-professionals">▶▶</button>
                        <button type="button" id="btn-remove-all-professionals" class="btn-remove-all-professionals">◀◀</button>
                    </div>
                    <div class="list-container">
                        <select id="selected-professionals" name="profissional_id[]" multiple size="10"></select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Procedimentos -->
        <div class="procedure-row-center">
            <div class="form-group">
                <label>Procedimentos</label>
                <div class="procedure-selector bloco-procedimentos">
                    <div class="list-container">
                        <select id="available-procedures" multiple size="10">
                            <?php foreach ($procedimentos['dados'] as $proc): ?>
                                <option value="<?= htmlspecialchars($proc['id']) ?>">
                                    <?= htmlspecialchars($proc['codigo']) ?> - <?= htmlspecialchars($proc['descricao']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="buttons-container">
                        <button type="button" id="btn-add-procedure" class="btn-add-single">▶</button>
                        <button type="button" id="btn-remove-procedure" class="btn-remove-single">◀</button>
                        <button type="button" id="btn-add-all-procedures" class="btn-add-all">▶▶</button>
                        <button type="button" id="btn-remove-all-procedures" class="btn-remove-all">◀◀</button>
                    </div>
                    <div class="list-container">
                        <select id="selected-procedures" name="procedimento_id[]" multiple size="10"></select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botão final -->
        <div class="procedure-row-center">
            <button type="submit" class="btn-add">Inserir Atendimento</button>
        </div>
    </form>
</section>

<script>
    // Funções de movimentação
    function moveSelected(from, to) {
        Array.from(from.selectedOptions).forEach(option => to.appendChild(option));
    }

    function moveAll(from, to) {
        while (from.firstChild) {
            to.appendChild(from.firstChild);
        }
    }

    // Eventos dos botões
    document.addEventListener('DOMContentLoaded', function () {
        const availablePatients = document.getElementById('available-patients');
        const selectedPatients = document.getElementById('selected-patients');

        const availableProfessionals = document.getElementById('available-professionals');
        const selectedProfessionals = document.getElementById('selected-professionals');

        const availableProcedures = document.getElementById('available-procedures');
        const selectedProcedures = document.getElementById('selected-procedures');

        // Pacientes
        document.getElementById('btn-add-patient').onclick = () => moveSelected(availablePatients, selectedPatients);
        document.getElementById('btn-remove-patient').onclick = () => moveSelected(selectedPatients, availablePatients);
        document.getElementById('btn-add-all-patients').onclick = () => moveAll(availablePatients, selectedPatients);
        document.getElementById('btn-remove-all-patients').onclick = () => moveAll(selectedPatients, availablePatients);

        // Profissionais
        document.getElementById('btn-add-professional').onclick = () => moveSelected(availableProfessionals, selectedProfessionals);
        document.getElementById('btn-remove-professional').onclick = () => moveSelected(selectedProfessionals, availableProfessionals);
        document.getElementById('btn-add-all-professionals').onclick = () => moveAll(availableProfessionals, selectedProfessionals);
        document.getElementById('btn-remove-all-professionals').onclick = () => moveAll(selectedProfessionals, availableProfessionals);

        // Procedimentos
        document.getElementById('btn-add-procedure').onclick = () => moveSelected(availableProcedures, selectedProcedures);
        document.getElementById('btn-remove-procedure').onclick = () => moveSelected(selectedProcedures, availableProcedures);
        document.getElementById('btn-add-all-procedures').onclick = () => moveAll(availableProcedures, selectedProcedures);
        document.getElementById('btn-remove-all-procedures').onclick = () => moveAll(selectedProcedures, availableProcedures);

        // Antes do envio do formulário
        document.querySelector('form').addEventListener('submit', function (e) {
            // Marcar todas as opções como selecionadas
            [selectedPatients, selectedProfessionals, selectedProcedures].forEach(list => {
                Array.from(list.options).forEach(opt => opt.selected = true);
            });

            // Verificar se há pelo menos um procedimento
            if (selectedProcedures.options.length === 0) {
                e.preventDefault();
                alert("Selecione pelo menos um procedimento.");
                return false;
            }

            // Mostrar overlay de carregamento
            document.getElementById('loading-overlay').style.display = 'flex';
        });
    });
</script>

</body>
</html>
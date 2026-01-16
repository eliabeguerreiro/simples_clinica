<?php
session_start();

function usuarioTemPermissao($permissao)
{
    return isset($_SESSION['data_user']['permissoes']) && in_array($permissao, $_SESSION['data_user']['permissoes']);
}

include "../../../classes/db.class.php";

if (!isset($_GET['paciente_id']) || !is_numeric($_GET['paciente_id'])) {
    die("<h2>Erro</h2><p>Paciente não especificado.</p>");
}

$paciente_id = (int)$_GET['paciente_id'];

try {
    $db = DB::connect();
    $stmt = $db->prepare("SELECT id, nome, descricao, especialidade FROM formulario WHERE ativo = 1 ORDER BY especialidade, nome");
    $stmt->execute();
    $formularios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT nome FROM paciente WHERE id = ?");
    $stmt->execute([$paciente_id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomePaciente = $paciente ? $paciente['nome'] : null;
} catch (Exception $e) {
    die("<h2>Erro</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}

if (!$nomePaciente) {
    die("<h2>Paciente não encontrado</h2><p>O paciente com ID <strong>{$paciente_id}</strong> não existe.</p>");
}

// Verifica se o usuário tem permissão para acessar esta página
$temAcesso = (
    usuarioTemPermissao('evolucoes.fisio.criar') ||
    usuarioTemPermissao('evolucoes.fono.criar') ||
    usuarioTemPermissao('evolucoes.teoc.criar')
);

if (!$temAcesso) {
    die("<h2>Acesso Negado</h2><p>Você não tem permissão para visualizar formulários de evolução.</p>");
}

// Agrupar por especialidade
$formulariosPorEspecialidade = [
    'FISIO' => [],
    'FONO' => [],
    'TEOC' => [],
    'OUTROS' => []
];

foreach ($formularios as $form) {
    $esp = $form['especialidade'] ?? '';
    if (isset($formulariosPorEspecialidade[$esp])) {
        $formulariosPorEspecialidade[$esp][] = $form;
    } else {
        $formulariosPorEspecialidade['OUTROS'][] = $form;
    }
}

$nomesEspecialidade = [
    'FISIO' => 'Fisioterapia',
    'FONO' => 'Fonoaudiologia',
    'TEOC' => 'Terapia Ocupacional',
    'OUTROS' => 'Outros Formulários'
];

$icones = [
    'FISIO' => '🦽',
    'FONO' => '🗣️',
    'TEOC' => '🧠',
    'OUTROS' => '📋'
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Formulário — <?= htmlspecialchars($nomePaciente) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="escolher_forms.css">
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="#" alt="Logo">
        </div>
        <div class="nav-actions">
            <a href="../">INÍCIO</a>
            <a href="?sair=1">SAIR</a>
        </div>
    </header>

    <div class="container">
        <a href="../pcnt/?id=<?= $paciente_id ?>&sub=documentos" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao paciente
        </a>
        <h2>Escolha um Formulário para Evolução</h2>
        <div class="paciente-info">
            <strong>Paciente:</strong> <?= htmlspecialchars($nomePaciente) ?>
        </div>

        <?php
        $temFormularios = false;
        foreach ($formulariosPorEspecialidade as $esp => $forms) {
            if (empty($forms)) continue;
            $temFormularios = true;
            ?>
            <div class="especialidade-secao">
                <div class="especialidade-titulo accordion-header" data-target="sec-<?= $esp ?>">
                    <span><?= $icones[$esp] ?></span>
                    <span><?= $nomesEspecialidade[$esp] ?></span>
                    <i class="fas fa-chevron-down accordion-icon"></i>
                </div>
                <div id="sec-<?= $esp ?>" class="accordion-content">
                    <?php foreach ($forms as $form): ?>
                        <div class="form-item">
                            <h3><?= htmlspecialchars($form['nome']) ?></h3>
                            <?php if (!empty($form['descricao'])): ?>
                                <p class="desc"><?= htmlspecialchars($form['descricao']) ?></p>
                            <?php endif; ?>
                            <div class="form-actions">
                                <?php
                                $mostrarBotao = false;
                                if ($esp === 'FISIO' && usuarioTemPermissao('evolucoes.fisio.criar')) {
                                    $mostrarBotao = true;
                                } elseif ($esp === 'FONO' && usuarioTemPermissao('evolucoes.fono.criar')) {
                                    $mostrarBotao = true;
                                } elseif ($esp === 'TEOC' && usuarioTemPermissao('evolucoes.teoc.criar')) {
                                    $mostrarBotao = true;
                                }

                                if ($mostrarBotao): ?>
                                    <a href="render_forms.php?form_id=<?= (int)$form['id'] ?>&paciente_id=<?= $paciente_id ?>" class="btn btn-usar">
                                        <i class="fas fa-file-medical"></i> Usar este formulário
                                    </a>
                                <?php else: ?>
                                    <span class="btn btn-disabled" title="Você não tem permissão para usar este formulário">
                                        <i class="fas fa-lock"></i> Sem permissão
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php } ?>

        <?php if (!$temFormularios): ?>
            <div class="no-forms">
                <p>Nenhum formulário ativo disponível para preenchimento.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="escolher_forms.js"></script>

    <?php
    if (isset($_GET['sair'])) {
        session_destroy();
        header('Location: ../');
        exit;
    }
    ?>
</body>
</html>
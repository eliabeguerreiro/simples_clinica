<?php
session_start();
include "../../../classes/db.class.php";

if (!isset($_GET['paciente_id']) || !is_numeric($_GET['paciente_id'])) {
    die("<h2>Erro</h2><p>Paciente não especificado.</p>");
}

$paciente_id = (int)$_GET['paciente_id'];

try {
    $db = DB::connect();
    $stmt = $db->prepare("SELECT id, nome, descricao, especialidade FROM formulario WHERE ativo = 1 ORDER BY nome");
    $stmt->execute();
    $formularios = $stmt->fetchAll(PDO::FETCH_ASSOC);




    $db = DB::connect();
    $stmt = $db->prepare("SELECT nome FROM paciente WHERE id = $paciente_id");
    $stmt->execute();
    $n = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $nome = $n[0]['nome'];

} catch (Exception $e) {
    die("<h2>Erro</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Formulário de Evolução</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f6fc;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            padding: 30px;
        }
        h2 {
            color: #6c63ff;
            text-align: center;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #6c63ff;
            text-decoration: none;
            font-weight: 500;
        }
        .form-item {
            padding: 16px;
            border-bottom: 1px solid #eee;
        }
        .form-item:last-child {
            border-bottom: none;
        }
        .form-item h3 {
            margin-bottom: 8px;
            color: #333;
        }
        .form-item p.desc {
            color: #666;
            font-size: 14px;
            margin-bottom: 12px;
        }
        .form-item p.esp {
            font-size: 13px;
            color: #574b90;
            margin-bottom: 12px;
        }
        .btn-usar {
            background: #6c63ff;
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-block;
            transition: background 0.2s;
        }
        .btn-usar:hover {
            background: #574b90;
            transform: translateY(-1px);
        }
        .no-forms {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../pcnt/?id=<?= $paciente_id ?>&sub=documentos" class="back-link">&larr; Voltar ao paciente</a>
        <h2>Escolha um Formulário para Evolução</h2>
        <p><strong>Paciente:</strong> <?= htmlspecialchars($nome) ?></p>

        <?php if (empty($formularios)): ?>
            <div class="no-forms">
                <p>Nenhum formulário ativo disponível para preenchimento.</p>
            </div>
        <?php else: ?>
            <?php foreach ($formularios as $form): ?>
                <div class="form-item">
                    <h3><?= htmlspecialchars($form['nome']) ?></h3>
                    <?php if (!empty($form['especialidade'])): ?>
                        <p class="esp"><strong>Especialidade:</strong> <?= htmlspecialchars($form['especialidade']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($form['descricao'])): ?>
                        <p class="desc"><?= htmlspecialchars($form['descricao']) ?></p>
                    <?php endif; ?>
                    <a href="render_forms.php?form_id=<?= (int)$form['id'] ?>&paciente_id=<?= $paciente_id ?>" class="btn-usar">
                        <i class="fas fa-file-medical"></i> Usar este formulário
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
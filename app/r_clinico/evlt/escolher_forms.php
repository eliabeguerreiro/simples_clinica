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

    $stmt = $db->prepare("SELECT nome FROM paciente WHERE id = ?");
    $stmt->execute([$paciente_id]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
    $nome = $paciente ? $paciente['nome'] : 'Paciente não encontrado';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        :root {
            --cor-primaria: #6c63ff;
            --cor-secundaria: #574b90;
            --cor-texto: #333;
            --cor-texto-secundario: #666;
            --cor-fundo: #f9f6fc;
            --cor-fundo-card: rgba(255, 255, 255, 0.95);
            --sombra-media: 0 8px 24px rgba(0, 0, 0, 0.08);
            --borda-raio: 16px;
            --transicao: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            line-height: 1.6;
            background-image: url('../../src/img/bkcg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            padding: 20px;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
            padding: 30px;
            background: var(--cor-fundo-card);
            border-radius: var(--borda-raio);
            box-shadow: var(--sombra-media);
            backdrop-filter: blur(6px);
            transition: transform 0.2s ease;
        }

        .container:hover {
            transform: translateY(-2px);
        }

        h2 {
            color: var(--cor-primaria);
            text-align: center;
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--cor-primaria);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transicao);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link:hover {
            color: var(--cor-secundaria);
            transform: translateY(-1px);
        }

        .form-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: #fafaff;
            border-radius: 10px;
            margin-bottom: 16px;
            transition: var(--transicao);
        }

        .form-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .form-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .form-item h3 {
            margin-bottom: 10px;
            color: var(--cor-texto);
            font-size: 18px;
            font-weight: 600;
        }

        .form-item p.desc {
            color: var(--cor-texto-secundario);
            font-size: 14px;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .form-item p.esp {
            font-size: 13px;
            color: var(--cor-secundaria);
            margin-bottom: 12px;
            font-weight: 500;
        }

        .btn-usar {
            background: linear-gradient(135deg, var(--cor-primaria), var(--cor-secundaria));
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: var(--transicao);
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(108, 99, 255, 0.2);
        }

        .btn-usar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(108, 99, 255, 0.3);
        }

        .btn-usar i {
            margin-right: 8px;
        }

        .no-forms {
            text-align: center;
            padding: 40px;
            color: var(--cor-texto-secundario);
            font-size: 16px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                margin: 20px;
                padding: 20px;
                max-width: 95%;
            }

            .form-item {
                padding: 16px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../pcnt/?id=<?= $paciente_id ?>&sub=documentos" class="back-link">
            <i class="fas fa-arrow-left"></i> Voltar ao paciente
        </a>
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
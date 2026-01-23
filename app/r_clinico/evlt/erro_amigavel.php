<?php
// Uso: include 'erro_amigavel.php'; exibirErro("Mensagem amigável", "voltar.php");
function exibirErro($mensagem, $urlVoltar = 'index.php', $titulo = 'Ops! Algo deu errado') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($titulo) ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <style>
            :root {
                --cor-primaria: #6c63ff;
                --cor-secundaria: #574b90;
                --cor-erro: #dc3545;
                --cor-fundo: #f9f6fc;
                --cor-texto: #333;
                --cor-fundo-card: rgba(255, 255, 255, 0.95);
                --sombra-media: 0 8px 24px rgba(0, 0, 0, 0.08);
                --borda-raio: 16px;
            }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: var(--cor-fundo);
                background-image: url('../../../src/img/bkcg.png');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 80px auto;
                padding: 40px;
                background: var(--cor-fundo-card);
                border-radius: var(--borda-raio);
                box-shadow: var(--sombra-media);
                text-align: center;
                backdrop-filter: blur(6px);
            }
            .container h2 {
                color: var(--cor-erro);
                font-size: 28px;
                margin-bottom: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 12px;
            }
            .container p {
                color: #555;
                font-size: 18px;
                line-height: 1.6;
                margin: 20px 0;
            }
            .btn-voltar {
                background: linear-gradient(135deg, var(--cor-primaria), var(--cor-secundaria));
                color: white;
                text-decoration: none;
                padding: 14px 28px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 16px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: transform 0.2s ease;
                box-shadow: 0 4px 12px rgba(108, 99, 255, 0.2);
            }
            .btn-voltar:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(108, 99, 255, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($titulo) ?></h2>
            <p><?= htmlspecialchars($mensagem) ?></p>
            <a href="<?= htmlspecialchars($urlVoltar) ?>" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
<?php
class ContentRClinico
{
    public function renderHeader()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico</title>
                <link rel="stylesheet" href="./src/style.css">
            </head>
        HTML;
        return $html;
    }

    public function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);

        $html = <<<HTML
            <body>
                <header>
                    <div class="logo">
                        <img src="#" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="./">INICIO</a></li>
                            <li><a href="/atendimentos.php">SUPORTE</a></li>
                            <li><a href="/paciente">SAIR</a></li>
                        </ul>
                    </nav>
                </header>



            <script src="src/script.js"></script>
            </body>
            
        HTML;
        return $html;
    }
}
<?php

class ContentPainelInicial
{
    public function renderHeader()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Vivenciar - Espaço Terapêutico</title>
                <link rel="stylesheet" href="./src/style.css">
            </head>
        HTML;

        return $html;
    }

    public function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']); // Escapa caracteres especiais para evitar XSS
       

        $html = <<<HTML
            <body>
                <header>
                    <div class="logo">
                        <img src="./style/vivenciar_logov2.png" alt="Logo Vivenciar">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="./">EMPRESA</a></li>
                            <li><a href="/atendimentos.php">SUPORTE</a></li>
                            <li><a href="/paciente">SAIR</a></li>
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
                    <h2>Modulos</h2>
                  
                        
                          <nav>
                            <ul>
                                <li><a href="./">RECEPÇÃO</a></li>
                                <li><a href="/atendimentos.php">FINANCEIRO</a></li>
                                <li><a href="/paciente">ATENDIMENTOS</a></li>
                                <li><a href="/profissional">CONFIGURAÇÕES</a></li>
                                <li><a href="/procedimento">RELATÓRIOS</a></li>
                            </ul>
                          </nav>
                        

                </section>
            <script src="src/script.js"></script>
            </body>
            
        HTML;

        return $html;
    }
}
?>
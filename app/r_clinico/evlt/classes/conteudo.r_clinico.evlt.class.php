<?php
class ContentRClinicoEvlt
{
    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico - Evoluções</title>
                <link rel="stylesheet" href="./src/style.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
            </head>
        HTML;

        // Renderiza o corpo da página
        $body = $this->renderBody();

        $html .= $body;

        $html .= <<<HTML
            <script src="./src/script.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        </body>
        </html>
        HTML;

        return $html;
    }

    private function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);

        // Conteúdo temporário para as sub-abas
        $conteudoNovaEvolucao = '<div class="form-container"><p>Conteúdo da Nova Evolução - Em desenvolvimento</p></div>';
        $conteudoListarEvolucoes = '<p>Conteúdo Listar Evoluções - Em desenvolvimento</p>';
        $conteudoGraficosEvolucoes = '<p>Conteúdo Gráficos de Evoluções - Em desenvolvimento</p>';

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

                <section class="simple-box">
                    <h2>Registro Clínico</h2>
                    
                    <!-- Abas principais de navegação entre módulos -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn" onclick="redirectToTab('pacientes')">Pacientes</button>
                        <button class="tab-btn" onclick="redirectToTab('atendimentos')">Atendimentos</button>
                        <button class="tab-btn active">Evoluções</button>
                    </div>
                    
                    <!-- Sub-abas do módulo atual -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-evolucoes">
                            <button class="tab-btn active" data-main="evolucoes" data-sub="nova" onclick="showSubTab('evolucoes', 'nova', this)">Nova</button>
                            <button class="tab-btn" data-main="evolucoes" data-sub="listar" onclick="showSubTab('evolucoes', 'listar', this)">Listar</button>
                            <button class="tab-btn" data-main="evolucoes" data-sub="graficos" onclick="showSubTab('evolucoes', 'graficos', this)">Gráficos</button>
                        </div>
                    </div>
                    
                    <!-- Conteúdo das abas -->
                    <div id="tab-content">
                        <div id="evolucoes-nova" class="tab-content active">
                            {$conteudoNovaEvolucao}
                        </div>
                        <div id="evolucoes-listar" class="tab-content" style="display:none;">
                            {$conteudoListarEvolucoes}
                        </div>
                        <div id="evolucoes-graficos" class="tab-content" style="display:none;">
                            {$conteudoGraficosEvolucoes}
                        </div>
                    </div>
                </section>

                <script src="./src/script.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
            </body>
        HTML;

        return $html;
    }
}
?>
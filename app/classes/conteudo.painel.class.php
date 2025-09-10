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
                <title>Clínica - Home</title>
                <link rel="stylesheet" href="./src/style.css">
            </head>
        HTML;
        return $html;
    }

    public function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);

        // Definição dos módulos com ícones e descrições
        $modules = [
            [
                'title' => 'Registro Clínico',
                'description' => 'Gerencie pacientes, atendimentos e evoluções clínicas',
                'icon' => '🏥',
                'link' => 'r_clinico/'
            ],
            [
                'title' => 'Configurações Administrativas',
                'description' => 'Gerencie usuários, permissões e configurações do sistema',
                'icon' => '⚙️',
                'link' => 'c_admin/'
            ],
            [
                'title' => 'BPA',
                'description' => 'Boletim de Produção Ambulatorial',
                'icon' => '📊',
                'link' => 'bpa/'
            ]
        ];

        $modulesHtml = '';
        foreach ($modules as $module) {
            $modulesHtml .= <<<HTML
                <a href="{$module['link']}" class="module-card">
                    <div class="module-icon">{$module['icon']}</div>
                    <h3>{$module['title']}</h3>
                    <p>{$module['description']}</p>
                </a>
HTML;
        }

        $html = <<<HTML
            <body>
                <header>
                    <div class="logo">
                        <img src="#" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="./">INÍCIO</a></li>
                            <li><a href="/atendimentos.php">SUPORTE</a></li>
                            <li><a href="/paciente">SAIR</a></li>
                        </ul>
                    </nav>
                </header>

                <section class="simple-box">
                    <h2>Bem-vindo, {$nome}!</h2>
                    <p>Selecione um módulo para começar</p>
                    
                    <div class="modules-grid">
                        {$modulesHtml}
                    </div>
                </section>

                <script src="src/script.js"></script>
            </body>
HTML;
        return $html;
    }
}
?>
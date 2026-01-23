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
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
            </head>
HTML;
        return $html;
    }

    public function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);
        $perfil = htmlspecialchars($_SESSION['data_user']['perfil_nome'] ?? 'Usuário');

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
                        <img src="src/img/vivenciar_logov2.png" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="../../">INICIO</a></li>
                            <li><a href="#">SUPORTE</a></li>
                            <li class="user-info">
                                <span class="user-icon"><i class="fas fa-user"></i></span>
                                <div class="user-details">
                                    <span class="user-name">{$nome}</span>
                                    <span class="user-role">{$perfil}</span>
                                </div>
                                <a href="?sair" class="btn-logout" title="Sair">
                                    <i class="fas fa-sign-out-alt"></i>
                                </a>
                            </li>
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

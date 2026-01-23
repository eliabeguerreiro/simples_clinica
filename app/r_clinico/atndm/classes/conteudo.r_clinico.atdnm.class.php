<?php
class ContentRClinicoAtdnm
{
    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registro Clínico - Atendimentos</title>
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

        $html = <<<HTML
            <body>
                <header>
                    <div class="logo">
                        <img src="src/vivenciar_logov2.png" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="../../">INICIO</a></li>
                            <li><a href="#">SUPORTE</a></li>
                            <li><a href="?sair">SAIR</a></li>
                        </ul>
                    </nav>
                </header>

                <section class="simple-box">
                    <h2>Registro Clínico - Atendimentos</h2>
                    
                    <!-- Abas principais de navegação entre módulos -->
                    <div class="tabs" id="main-tabs">
                        <button class="tab-btn" onclick="window.location.href='../pcnt/'">Pacientes</button>
                        <button class="tab-btn active">Atendimentos</button>
                        <button class="tab-btn" onclick="window.location.href='../evlt/'">Formulários</button>
                    </div>
                    
                    <!-- Sub-abas do módulo atual -->
                    <div id="sub-tabs">
                        <div class="sub-tabs" id="sub-atendimentos">
                            <button class="tab-btn active" data-main="atendimentos" data-sub="novo" onclick="showSubTab('atendimentos', 'novo', this)">Novo</button>
                            <button class="tab-btn" data-main="atendimentos" data-sub="listar" onclick="showSubTab('atendimentos', 'listar', this)">Listar</button>
                            <button class="tab-btn" data-main="atendimentos" data-sub="relatorios" onclick="showSubTab('atendimentos', 'relatorios', this)">Relatórios</button>
                        </div>
                    </div>
                    
                    <!-- Conteúdo das abas -->
                    <div id="tab-content">
                        <div id="atendimentos-novo" class="tab-content active">
                            <div class="form-message info" style="text-align: center; padding: 30px; font-size: 16px; line-height: 1.6;">
                                <i class="fas fa-exclamation-circle" style="font-size: 24px; margin-bottom: 15px; color: #574b90;"></i>
                                <p><strong>Este módulo está em pré-desenvolvimento.</strong></p>
                                <p>As funções de registro de atendimentos, horários, escalas e relatório de atendimentos realizados pelos profissionais estarão disponíveis na <strong>Fase 2 do projeto CLINIG</strong>.</p>
                                <p style="margin-top: 20px; font-size: 14px; color: #666;">
                                    Agradecemos pela compreensão.
                                </p>
                            </div>
                        </div>
                        <div id="atendimentos-listar" class="tab-content" style="display:none;">
                            <div class="form-message info" style="text-align: center; padding: 30px; font-size: 16px; line-height: 1.6;">
                                <i class="fas fa-exclamation-circle" style="font-size: 24px; margin-bottom: 15px; color: #574b90;"></i>
                                <p><strong>Este módulo está em pré-desenvolvimento.</strong></p>
                                <p>As funções de registro de atendimentos, horários, escalas e relatório de atendimentos realizados pelos profissionais estarão disponíveis na <strong>Fase 2 do projeto CLINIG</strong>.</p>
                                <p style="margin-top: 20px; font-size: 14px; color: #666;">
                                    Agradecemos pela compreensão.
                                </p>
                            </div>
                        </div>
                        <div id="atendimentos-relatorios" class="tab-content" style="display:none;">
                            <div class="form-message info" style="text-align: center; padding: 30px; font-size: 16px; line-height: 1.6;">
                                <i class="fas fa-exclamation-circle" style="font-size: 24px; margin-bottom: 15px; color: #574b90;"></i>
                                <p><strong>Este módulo está em pré-desenvolvimento.</strong></p>
                                <p>As funções de registro de atendimentos, horários, escalas e relatório de atendimentos realizados pelos profissionais estarão disponíveis na <strong>Fase 2 do projeto CLINIG</strong>.</p>
                                <p style="margin-top: 20px; font-size: 14px; color: #666;">
                                    Agradecemos pela compreensão.
                                </p>
                            </div>
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
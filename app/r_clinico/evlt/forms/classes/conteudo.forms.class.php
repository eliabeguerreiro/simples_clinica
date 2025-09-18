<?php
class ConteudoFormsPrincipal
{
    public function render()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Formulários - Evoluções</title>
                <link rel="stylesheet" href="../src/style.css">
                <link rel="stylesheet" href="./src/style.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
            </head>
            <body>
                <header>
                    <div class="logo">
                        <img src="#" alt="Logo">
                    </div>
                    <nav>
                        <ul>
                            <li><a href="../../">INICIO</a></li>
                            <li><a href="../../../atendimentos.php">SUPORTE</a></li>
                            <li><a href="../../../paciente">SAIR</a></li>
                        </ul>
                    </nav>
                </header>

                <section class="simple-box">
                    <h2>Formulários de Evolução</h2>
                    
                    <!-- Botão de voltar para evoluções -->
                    <div style="margin-bottom: 20px;">
                        <button class="btn-clear" onclick="voltarParaEvolucoes()">
                            <i class="fas fa-arrow-left"></i> Voltar para Evoluções
                        </button>
                    </div>
                    
                    <div class="formularios-grid">
                        <div class="formulario-card">
                            <div class="card-header">
                                <i class="fas fa-plus-circle"></i>
                                <h4>Criar Novo Formulário</h4>
                            </div>
                            <div class="card-body">
                                <p>Crie formulários personalizados para diferentes áreas de atendimento</p>
                            </div>
                            <div class="card-footer">
                                <button class="btn-add" onclick="window.location.href='?acao=criar'">
                                    <i class="fas fa-plus"></i> Criar Formulário
                                </button>
                            </div>
                        </div>
                        
                        <div class="formulario-card">
                            <div class="card-header">
                                <i class="fas fa-list"></i>
                                <h4>Gerenciar Formulários</h4>
                            </div>
                            <div class="card-body">
                                <p>Gerencie formulários já criados e seus campos</p>
                            </div>
                            <div class="card-footer">
                                <button class="btn-edit" onclick="window.location.href='?acao=gerenciar'">
                                    <i class="fas fa-edit"></i> Gerenciar
                                </button>
                            </div>
                        </div>
                        
                        <div class="formulario-card">
                            <div class="card-header">
                                <i class="fas fa-file-medical"></i>
                                <h4>Aplicar Formulário</h4>
                            </div>
                            <div class="card-body">
                                <p>Utilize formulários padronizados para registrar evoluções</p>
                            </div>
                            <div class="card-footer">
                                <button class="btn-evolucao" onclick="window.location.href='?acao=aplicar'">
                                    <i class="fas fa-play"></i> Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <script>
                function voltarParaEvolucoes() {
                    window.location.href = '../';
                }
                </script>
            </body>
            </html>
        HTML;

        return $html;
    }
}
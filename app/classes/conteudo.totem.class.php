<?php

class ContentPainelTotem
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
                            <li><a href="./">Novos atendimentos</a></li>
                            <li><a href="/atendimentos.php">Atendimentos</a></li>
                            <li><a href="/paciente">Pacientes</a></li>
                            <li><a href="/profissional">Profissionais</a></li>
                            <li><a href="/procedimento">Procedimentos</a></li>
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
                    <h2>Gerar Senha</h2>
                    <form action="" method="POST">
                        <!-- Dados Básicos -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="competencia">Competência (AAAAMM)</label>
                                <div class="input-with-button">
                                    <input type="text" id="competencia" name="competencia" required pattern="\d{6}" title="Formato: AAAAMM">
                                    <button type="button" class="btn-mes-atual" onclick="document.getElementById('competencia').value='<?= date('Ym') ?>'">
                                        Usar mês atual
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="data_atendimento">Data do Atendimento</label>
                                <input type="date" id="data_atendimento" name="data_atendimento" required>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="cid">CID-10 (Opcional)</label>
                                <input type="text" id="cid" name="cid" maxlength="4" placeholder="Ex: F800">
                            </div>
                        </div>

                        <!-- Botão final -->
                        <div class="procedure-row-center">
                            <button type="submit" class="btn-add">Inserir Atendimento</button>
                        </div>
                    </form>
                </section>
            <script src="src/script.js"></script>
            </body>
            
        HTML;

        return $html;
    }
}
?>
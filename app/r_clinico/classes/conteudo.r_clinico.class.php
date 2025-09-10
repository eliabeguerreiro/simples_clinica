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

    private function generateMainTabs($tabs)
    {
        $html = '<div class="tabs" id="main-tabs">';
        foreach ($tabs as $index => $tab) {
            $activeClass = $index === 0 ? 'active' : '';
            $html .= "<button class=\"tab-btn {$activeClass}\" data-tab=\"{$tab['id']}\" onclick=\"showMainTab('{$tab['id']}', this)\">{$tab['label']}</button>";
        }
        $html .= '</div>';
        return $html;
    }

    private function generateSubTabs($tabs)
    {
        $html = '<div id="sub-tabs">';
        foreach ($tabs as $tab) {
            $displayStyle = $tab['id'] === 'pacientes' ? '' : 'style="display:none;"';
            $html .= "<div class=\"sub-tabs\" id=\"sub-{$tab['id']}\" {$displayStyle}>";
            
            foreach ($tab['subtabs'] as $subIndex => $subtab) {
                $activeClass = $subIndex === 0 ? 'active' : '';
                $html .= "<button class=\"tab-btn {$activeClass}\" data-main=\"{$tab['id']}\" data-sub=\"{$subtab['id']}\" onclick=\"showSubTab('{$tab['id']}', '{$subtab['id']}', this)\">{$subtab['label']}</button>";
            }
            
            $html .= "</div>";
        }
        $html .= '</div>';
        return $html;
    }

    private function generateContent($tabs)
    {
        $html = '<div id="tab-content">';
        foreach ($tabs as $tab) {
            foreach ($tab['subtabs'] as $subIndex => $subtab) {
                $activeClass = $subIndex === 0 ? 'active' : '';
                $displayStyle = $subIndex === 0 ? '' : 'style="display:none;"';
                
                $html .= "<div id=\"{$tab['id']}-{$subtab['id']}\" class=\"tab-content {$activeClass}\" {$displayStyle}>";
                $html .= "<p>{$subtab['content']}</p>";
                $html .= "</div>";
            }
        }
        $html .= '</div>';
        return $html;
    }

    public function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']);

        $mainTabs = [
            [
                'id' => 'pacientes',
                'label' => 'Pacientes',
                'subtabs' => [
                    ['id' => 'cadastro', 'label' => 'Cadastro', 'content' => 'Conteúdo Cadastro de Pacientes.'],
                    ['id' => 'documentos', 'label' => 'Documentos', 'content' => 'Conteúdo Documentos de Pacientes.'],
                    ['id' => 'historico', 'label' => 'Histórico', 'content' => 'Conteúdo Histórico de Pacientes.']
                ]
            ],
            [
                'id' => 'atendimentos',
                'label' => 'Atendimentos',
                'subtabs' => [
                    ['id' => 'novo', 'label' => 'Novo', 'content' => 'Conteúdo Novo Atendimento.'],
                    ['id' => 'listar', 'label' => 'Listar', 'content' => 'Conteúdo Listar Atendimentos.'],
                    ['id' => 'relatorios', 'label' => 'Relatórios', 'content' => 'Conteúdo Relatórios de Atendimentos.']
                ]
            ],
            [
                'id' => 'evolucoes',
                'label' => 'Evoluções',
                'subtabs' => [
                    ['id' => 'nova', 'label' => 'Nova', 'content' => 'Conteúdo Nova Evolução.'],
                    ['id' => 'listar', 'label' => 'Listar', 'content' => 'Conteúdo Listar Evoluções.'],
                    ['id' => 'graficos', 'label' => 'Gráficos', 'content' => 'Conteúdo Gráficos de Evoluções.']
                ]
            ]
        ];

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
                    
                    <!-- Abas principais -->
                    {$this->generateMainTabs($mainTabs)}
                    
                    <!-- Sub-abas -->
                    {$this->generateSubTabs($mainTabs)}
                    
                    <!-- Conteúdo das abas -->
                    {$this->generateContent($mainTabs)}
                </section>

                <script src="src/script.js"></script>
            </body>
HTML;

        return $html;
    }
}
?>
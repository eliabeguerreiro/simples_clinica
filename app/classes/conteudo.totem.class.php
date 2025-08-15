<?php

class ContentPainel
{
    public function renderHeader()
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-br">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>GesQuip</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                <link rel="stylesheet" href="src/style.css">
            </head>
        HTML;

        return $html;
    }

    public function renderBody()
    {
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']); // Escapa caracteres especiais para evitar XSS
        $matricula = htmlspecialchars($_SESSION['data_user']['matricula']); // Escapa caracteres especiais para evitar XSS
        $obra = htmlspecialchars($_SESSION['obra_atual']); // Escapa caracteres especiais para evitar XSS


        $html = <<<HTML
            <body>

            <!--INICIO BARRA DE NAVEAGAÇÃO-->
            <nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="../"><b>GesQuip</b></a>
                    <div class="navbar-collapse" id="collapsibleNavbar">
                        <ul class="navbar-nav">
                            <!--GESTÃO DE ITENS-->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Itens</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="itens/?pagina=gestao_itens" id="CadastroItemLink">Gestão de Itens</a></li>
                                    <li><a class="dropdown-item" href="itens/?pagina=novo" id="CadastroItemLink">Novo Item</a></li>
                                </ul>
                            </li>
                            <!--GESTÃO MOVIMENTACAO-->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="moviment">Movimentações</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="moviment/?pagina=nova" id="NovaMoviment">Nova Movimentação</a></li>
                                    <li><a class="dropdown-item" href="moviment/?pagina=ativas" id="MovimentAtiva">Movimentações Ativas</a></li>
                                </ul>
                            </li>
                            <!--GESTÃO MANUTENÇÃO-->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="Manutencao">Manutenções</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="manutencao/?pagina=nova" id="NovaManutencao">Nova Manutenção</a></li>
                                    <li><a class="dropdown-item" href="manutencao/?pagina=ativas" id="ManutencaoAtiva">Manutenções Ativa</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="usuarios" id="usuario">Relatórios</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="itens/?pagina=itens" id="CadastroItemLink">Todos os Itens</a></li>
                                    <li><a class="dropdown-item" href="itens/?pagina=disponiveis" id="CadastroItemLink">Itens Disponíveis</a></li>
                                    <li><a class="dropdown-item" href="itens/?pagina=emuso" id="CadastroItemLink">Itens em Uso</a></li>
                                    <li><a class="dropdown-item" href="itens/?pagina=quebrados" id="CadastroItemLink">Itens Quebrados</a></li>
                                    <li><a class="dropdown-item" href="moviment/?pagina=encerradas" id="MovimentEncer">Movimentações Encerradas</a></li>
                                    <li><a class="dropdown-item" href="manutencao/?pagina=encerradas" id="ManutencaoAtiva">Manutenções Encerradas</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex ms-auto align-items-center">
                        <div class="d-flex align-items-center text-white me-3">
                            <span>$nome -</span>
                            <span class="mx-2">$matricula</span>
                            <span>Obra: $obra</span>
                        </div>
                        <div class="d-flex ms-auto">
                            <a href="?sair=1" class="btn btn-danger btn-sm">Sair</a>
                        </div>
                    </div>
                </div>
            </nav>
            <main>
                
      HTML;

      if (isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
      } 
      $html .= <<<HTML

            <!-- INÍCIO DO CONTEÚDO CENTRAL -->
            <div class="main-content" id="mainContent">
                <div class="container mt-4" id="novoItem" style="display: block;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="header-with-filter">
                                <h3><b><p class="text-primary">Buscar Item</p></b></h3>
                                <div class="filter-container">
                                    <label for="chaveSelect" class="form-label visually-hidden">Filtro</label>
                                    <select id="chaveSelect" name="filtro" class="form-select form-select-sm filter-select" required>
                                        <option value="ds_item">NOME</option>
                                        <option value="cod_patrimonio">PATRIMONIO</option>
                                    </select>
                                </div>
                            </div>
                            <form id="searchForm">
                                <div class="mb-3">
                                    <label for="busca" class="form-label">Digite o Valor</label>
                                    <input type="text" id="busca" name="valor" class="form-control" required>
                                </div>
                                <button type="button" id="finalizaSubmit" class="btn btn-primary">Buscar</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="container mt-4" id="containerFerramentas" style="display: block;">
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h3><b><p class="text-primary">Itens Encontrados</p></b></h3>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Cod</th>
                                        <th>Nome</th>
                                        <th>Família</th>
                                        <th>Movimentação</th>
                                        <th>Usuário</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="produtos">
                                    <!-- Os resultados serão inseridos dinamicamente pelo JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <script src="src/script.js"></script>
            </body>
            </html>
        HTML;

        return $html;
    }
}
?>
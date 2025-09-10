<?php
  
class ContentPainelItem
{

  public function renderHeader(){
 
    $html = <<<HTML
      <!DOCTYPE html>
      <html lang="pt-br">
      <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>GesQuip - Itens</title>
          <link rel="icon" type="image/png" href="src/img/favicon.png">
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
          <link rel="stylesheet" href="src/style.css">
      </head>

    HTML;   

    return($html);
}

    public function renderBody($pagina, $familia, $itens, $itens_disponiveis, $itens_locados, $itens_quebrados){

        //var_dump($itens);
        $nome = htmlspecialchars($_SESSION['data_user']['nm_usuario']); // Escapa caracteres especiais para evitar XSS
        $matricula = htmlspecialchars($_SESSION['data_user']['matricula']); // Escapa caracteres especiais para evitar XSS
        $obra = htmlspecialchars($_SESSION['obra_atual']); // Escapa caracteres especiais para evitar XSS

        
        // Verifica se os parâmetros GET estão definidos
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
        $valor = isset($_GET['valor']) ? $_GET['valor'] : null;
      
    
        function buildUrlItens($newParams = []) {
            $queryParams = $_GET;
            foreach ($newParams as $key => $value) {
                if ($value === null) {
                    unset($queryParams[$key]);
                } else {
                    $queryParams[$key] = $value;
                }
            }
            // Remove os parâmetros 'filtro' e 'v' se a página for alterada
            if (isset($newParams['pagina'])) {
                unset($queryParams['filtro']);
                unset($queryParams['valor']);
            }
            return '?' . http_build_query($queryParams);
        }


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
      HTML;                              
                          $html.="<li><a class='dropdown-item' href='" . buildUrlItens(['pagina' => 'gestao_itens']) . "'>Gestão de Itens</a></li>";
                          $html.="<li><a class='dropdown-item' href='" . buildUrlItens(['pagina' => 'novo']) . "'>Novo Item</a></li>";
      $html.= <<<HTML
                                </ul>
                            </li>
                            <!--GESTÃO MOVIMENTACAO-->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="moviment">Movimentações</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="../moviment?pagina=nova" id="NovaMoviment">Nova Movimentação</a></li>
                                    <li><a class="dropdown-item" href="../moviment?pagina=ativas" id="MovimentAtiva">Movimentações Ativas</a></li>
                                </ul>
                            </li>
                            <!--GESTÃO MANUTENÇÃO-->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="Manutencao">Manutenções</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="../manutencao/?pagina=nova" id="NovaManutencao">Nova Manutenção</a></li>
                                    <li><a class="dropdown-item" href="../manutencao/?pagina=ativas " id="ManutencaoAtiva">Manutenções Ativas</a></li>
                                </ul>
                            </li>
                            <!--GESTÃO DE USUARIOS-->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="usuarios" id="usuario">Funcionários</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="../usuarios?pagina=cadastro" id="NovosUsuarios">Cadastrar Funcionário</a></li>
                                    <li><a class="dropdown-item" href="../usuarios?pagina=usuarios" id="NovosUsuarios">Todos os Funcionários</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="usuarios" id="usuario">Relatórios</a>
                                <ul class="dropdown-menu">
        HTML;
                            $html.="<li><a class='dropdown-item' href='" . buildUrlItens(['pagina' => 'itens']) . "'>Todos os Itens</a></li>";
                            $html.="<li><a class='dropdown-item' href='" . buildUrlItens(['pagina' => 'disponiveis']) . "'>Itens Disponíveis</a></li>";
                            $html.="<li><a class='dropdown-item' href='" . buildUrlItens(['pagina' => 'emuso']) . "'>Itens em Uso</a></li>";
                            $html.="<li><a class='dropdown-item' href='" . buildUrlItens(['pagina' => 'quebrados']) . "'>Itens Quebrados</a></li>";
        $html.=<<<HTML
                                    <li><a class="dropdown-item" href="../moviment/?pagina=encerradas" id="MovimentEncer">Movimentações Encerradas</a></li>
                                    <li><a class="dropdown-item" href="../manutencao/?pagina=encerradas" id="ManutencaoAtiva">Manutenções Encerradas</a></li>
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
            <!--FIM BARRA DE NAVEAGAÇÃO-->
            <main>
                
        HTML;
    
        if (isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
        } 
    
           

      if (isset($pagina)) {
        
        switch ($pagina) {
           case 'novo':
            
            $html.= <<<HTML
              <div class="main-content" id="mainContent">
              <div class="container mt-4" id="novoItem" style="display: block;">
                <div class="row">
                  <div class="col-md-12">                    
                      <h3><b><p class="text-primary">Novo Item</p></b></h3>
                      <form method='POST' action='' id="formNovoItem">
                        <div class="mb-3">
                            <label for="familia" class="form-label">Família</label>
                            <select id="familia" name="familia" class="form-select" required>
                                <option value="">Escolha a família</option>
            HTML;
                                $fam = $familia;
                                foreach ($fam as $familiaaa) {
                                    $html.= "<option value='" . $familiaaa['id_familia'] . "'>" . $familiaaa['ds_familia'] . "</option>";
                                }
            $html.= <<<HTML
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nome" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Natureza de posse</label>
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="natureza_1" name="natureza" value="PRÓPRIO" required>
                                            <label class="form-check-label" for="natureza_1">Próprio</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="natureza_2" name="natureza" value="LOCADO" required>
                                            <label class="form-check-label" for="natureza_2">Locado</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                    <label class="form-label">Tipo</label>
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="tipo_1" name="tipo" value="F" required>
                                            <label class="form-check-label" for="natureza_1">Ferramenta</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="tipo_2" name="tipo" value="E" required>
                                            <label class="form-check-label" for="natureza_2">Equipamento</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nível de permissão</label>
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="nv_permissao_1" name="nv_permissao" value="1" required>
                                            <label class="form-check-label" for="nv_permissao_1">Nível 1</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="nv_permissao_2" name="nv_permissao" value="2" required>
                                            <label class="form-check-label" for="nv_permissao_2">Nível 2</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </form>
                  </div>
                </div>
              </div>    
            </div>
          HTML;

            break;
            case 'itens':
                if ($filtro && $valor) {

                    $itens_filtrados = Item::getItens(null, $filtro, $valor);
            
                    $itens = $itens_filtrados['dados'];
                }



              $html.= <<<HTML
        <!-- TABELA -->
        <div class="container mt-4" id="containerFerramentas" style="display: block;">
            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Header com filtro -->
                    <div class="header-with-filter">
                        <h3><b><p class="text-primary">Todos os Itens da obra: $obra</p></b></h3>
                        <div class="filter-container">
                            <label for="filtro_principal" class="form-label visually-hidden">Filtro Principal</label>
                            <select id="filtro_principal" class="form-select form-select-sm filter-select" required>
                                <option value="">Escolha um filtro</option>
                                <option value="id_familia">Família</option>
                                <option value="natureza">Natureza</option>
                            </select>
        
                            <!-- Div para Natureza -->
                            <div id="filtro_natureza" style="display: none; margin-left: 10px;">
                                <select id="filtro_natureza_select" class="form-select form-select-sm">
                                    <option value="">Escolha</option>
                                    <option value="próprio">Próprio</option>
                                    <option value="locado">Locado</option>
                                </select>
                            </div>

                            <!-- Div para Família -->
                            <div id="filtro_familia" style="display: none; margin-left: 10px;">
                                <input type="text" id="filtro_familia_input" class="form-control form-control-sm" placeholder="Digite o nome da família">
                                <div id="filtro_familia_suggestions" class="list-group mt-1" style="max-width:11.5%; max-height: 200px; overflow-y: auto; display: none;">
                                    <!-- As sugestões serão inseridas aqui pelo JavaScript -->
                                </div>
                            </div>
                        </div>
                        </div>
HTML;

                if ($filtro && $valor) {
                    $html .= <<<HTML
                    <!-- Identificador de Filtro -->
                    <div id="filtro_alert" class="alert alert-info">
                        <strong>Filtro aplicado:</strong> <span id="filtro_texto">
HTML;
                    if ($filtro === 'id_familia') {
                        $familiaNome = array_filter($familia, function($f) use ($valor) {
                            return $f['id_familia'] == $valor;
                        });
                        $familiaNome = reset($familiaNome);
                        $html .= "Família: " . $familiaNome['ds_familia'];
                    } else {
                        $html .= ucfirst($filtro) . ": " . $valor;
                    }
                    $html .= <<<HTML
                        </span>
                    </div>
HTML;
                }

                $html .= <<<HTML
                            <!-- Tabela de Itens -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Família</th>
                                <th>Modelo</th>
                                <th>Natureza</th>
                            
                            </tr>
                        </thead>
                        <tbody id="itens">
                            <tr>

      HTML;

      
      foreach ($itens as $item):
          $nm_familia = Item::getFamiliaNome($item['id_familia']);
          $html .="<td>".$item['cod_patrimonio']."</td>";
          $html .="<td>".$nm_familia."</td>";
          $html .="<td>".$item['ds_item']."</td>";
          $html .="<td>".$item['natureza']."</td>";
          $html .="</tr>";
      endforeach;
                 
      $html.= <<<HTML

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de Atualização -->
        <div class="modal fade" id="atualizaModal" tabindex="-1" aria-labelledby="atualizaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="atualizaModalLabel">Editar Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="itemId" />
                        <div class="mb-3">
                            <label for="novoNome" class="form-label">Novo Nome</label>
                            <input type="text" id="novoNome" class="form-control" placeholder="Digite o novo nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="novaNatureza" class="form-label">Nova Natureza</label>
                            <select id="novaNatureza" class="form-select" required>
                                <option value="PRÓPRIO">Próprio</option>
                                <option value="LOCADO">Locado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="atualizaSubmit">Salvar</button>
                    </div>
                </div>
            </div>
        </div>


      HTML;
              
                break;
                
            case 'gestao_itens':
                if ($filtro && $valor) {

                    $itens_filtrados = Item::getItens(null, $filtro, $valor);
            
                    $itens = $itens_filtrados['dados'];
                }



              $html.= <<<HTML
        <!-- TABELA -->
        <div class="container mt-4" id="containerFerramentas" style="display: block;">
            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Header com filtro -->
                    <div class="header-with-filter">
                        <h3><b><p class="text-primary">Gestão de Itens da obra: $obra</p></b></h3>
                        <div class="filter-container">
                            <label for="filtro_principal" class="form-label visually-hidden">Filtro Principal</label>
                            <select id="filtro_principal" class="form-select form-select-sm filter-select" required>
                                <option value="">Escolha um filtro</option>
                                <option value="id_familia">Família</option>
                                <option value="natureza">Natureza</option>
                            </select>
        
                            <!-- Div para Natureza -->
                            <div id="filtro_natureza" style="display: none; margin-left: 10px;">
                                <select id="filtro_natureza_select" class="form-select form-select-sm">
                                    <option value="">Escolha</option>
                                    <option value="próprio">Próprio</option>
                                    <option value="locado">Locado</option>
                                </select>
                            </div>

                            <!-- Div para Família -->
                            <div id="filtro_familia" style="display: none; margin-left: 10px;">
                                <input type="text" id="filtro_familia_input" class="form-control form-control-sm" placeholder="Digite o nome da família">
                                <div id="filtro_familia_suggestions" class="list-group mt-1" style="max-width:11.5%; max-height: 200px; overflow-y: auto; display: none;">
                                    <!-- As sugestões serão inseridas aqui pelo JavaScript -->
                                </div>
                            </div>
                        </div>
                        </div>
HTML;

                if ($filtro && $valor) {
                    $html .= <<<HTML
                    <!-- Identificador de Filtro -->
                    <div id="filtro_alert" class="alert alert-info">
                        <strong>Filtro aplicado:</strong> <span id="filtro_texto">
HTML;
                    if ($filtro === 'id_familia') {
                        $familiaNome = array_filter($familia, function($f) use ($valor) {
                            return $f['id_familia'] == $valor;
                        });
                        $familiaNome = reset($familiaNome);
                        $html .= "Família: " . $familiaNome['ds_familia'];
                    } else {
                        $html .= ucfirst($filtro) . ": " . $valor;
                    }
                    $html .= <<<HTML
                        </span>
                    </div>
HTML;
                }

                $html .= <<<HTML
                            <!-- Tabela de Itens -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Família</th>
                                <th>Modelo</th>
                                <th>Natureza</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="itens">
                            <tr>

      HTML;

      
      foreach ($itens as $item):
          $nm_familia = Item::getFamiliaNome($item['id_familia']);
          $html .="<td>".$item['cod_patrimonio']."</td>";
          $html .="<td>".$nm_familia."</td>";
          $html .="<td>".$item['ds_item']."</td>";
          $html .="<td>".$item['natureza']."</td>";
          $html .= "<td><button class='btn btn-success btn-sm atualiza-button' data-bs-toggle='modal' data-bs-target='#atualizaModal' data-id='".$item['id_item']."'>Editar</button>   ";
          $html .="<a href='desativar.php?id=".$item['id_item']."' class='btn btn-danger btn-sm' >Desativar</a></td>";
          $html .="</tr>";
      endforeach;
                 
      $html.= <<<HTML

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de Atualização -->
        <div class="modal fade" id="atualizaModal" tabindex="-1" aria-labelledby="atualizaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="atualizaModalLabel">Editar Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="itemId" />
                        <div class="mb-3">
                            <label for="novoNome" class="form-label">Novo Nome</label>
                            <input type="text" id="novoNome" class="form-control" placeholder="Digite o novo nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="novaNatureza" class="form-label">Nova Natureza</label>
                            <select id="novaNatureza" class="form-select" required>
                                <option value="PRÓPRIO">Próprio</option>
                                <option value="LOCADO">Locado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="atualizaSubmit">Salvar</button>
                    </div>
                </div>
            </div>
        </div>


      HTML;
              
                break;
            case 'disponiveis':

                if ($filtro && $valor) {

                    $itens_filtrados = Item::getItensDisponiveis( $filtro, $valor);
            
                    $itens_disponiveis = $itens_filtrados['dados'];
                }

              $html.= <<<HTML
        <!-- TABELA -->
        <div class="container mt-4" id="containerFerramentas" style="display: block;">
            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Header com filtro -->
                    <div class="header-with-filter">
                        <h3><b><p class="text-primary">Itens Disponiveis</p></b></h3>
                        <div class="filter-container">
                            <label for="filtro_principal" class="form-label visually-hidden">Filtro Principal</label>
                            <select id="filtro_principal" class="form-select form-select-sm filter-select" required>
                                <option value="">Escolha um filtro</option>
                                <option value="id_familia">Família</option>
                                <option value="natureza">Natureza</option>
                            </select>
        
                            <!-- Div para Natureza -->
                            <div id="filtro_natureza" style="display: none; margin-left: 10px;">
                                <select id="filtro_natureza_select" class="form-select form-select-sm">
                                    <option value="">Escolha</option>
                                    <option value="proprio">Próprio</option>
                                    <option value="locado">Locado</option>
                                </select>
                            </div>

                            <!-- Div para Família -->
                            <div id="filtro_familia" style="display: none; margin-left: 10px;">
                                <input type="text" id="filtro_familia_input" class="form-control form-control-sm" placeholder="Digite o nome da família">
                                <div id="filtro_familia_suggestions" class="list-group mt-1" style="max-width:11.5%; max-height: 200px; overflow-y: auto; display: none;">
                                    <!-- As sugestões serão inseridas aqui pelo JavaScript -->
                                </div>
                            </div>
                        </div>
                        </div>
HTML;

                if ($filtro && $valor) {
                    $html .= <<<HTML
                    <!-- Identificador de Filtro -->
                    <div id="filtro_alert" class="alert alert-info">
                        <strong>Filtro aplicado:</strong> <span id="filtro_texto">
HTML;
                    if ($filtro === 'id_familia') {
                        $familiaNome = array_filter($familia, function($f) use ($valor) {
                            return $f['id_familia'] == $valor;
                        });
                        $familiaNome = reset($familiaNome);
                        $html .= "Família: " . $familiaNome['ds_familia'];
                    } else {
                        $html .= ucfirst($filtro) . ": " . $valor;
                    }
                    $html .= <<<HTML
                        </span>
                    </div>
HTML;
                }

                $html .= <<<HTML
                    <!-- Tabela de Itens -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Família</th>
                                <th>Modelo</th>
                                <th>Natureza</th>
                                
                            </tr>
                        </thead>
                        <tbody id="itens">
                            <tr>

      HTML;

      
      foreach ($itens_disponiveis as $item):
          $nm_familia = Item::getFamiliaNome($item['id_familia']);
          $html .="<td>".$item['cod_patrimonio']."</td>";
          $html .="<td>".$nm_familia."</td>";
          $html .="<td>".$item['ds_item']."</td>";
          $html .="<td>".$item['natureza']."</td>";
          $html .="</tr>";
      endforeach;
                 
      $html.= <<<HTML

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      HTML;


                break;
            case 'emuso':
                


                if ($filtro && $valor) {

                    $itens_filtrados = Item::getItensLocados( $filtro, $valor);
            
                    $itens_locados = $itens_filtrados['dados'];
                }



              $html.= <<<HTML
              <!-- TABELA -->
              <div class="container mt-4" id="containerFerramentas" style="display: block;">
                  <div class="row mt-4">
                      <div class="col-md-12">
                          <!-- Header com filtro -->
                          <div class="header-with-filter">
                              <h3><b><p class="text-primary">Todos os Itens em Uso</p></b></h3>
                              <div class="filter-container">
                            <label for="filtro_principal" class="form-label visually-hidden">Filtro Principal</label>
                            <select id="filtro_principal" class="form-select form-select-sm filter-select" required>
                                <option value="">Escolha um filtro</option>
                                <option value="id_familia">Família</option>
                                <option value="natureza">Natureza</option>
                            </select>
        
                            <!-- Div para Natureza -->
                            <div id="filtro_natureza" style="display: none; margin-left: 10px;">
                                <select id="filtro_natureza_select" class="form-select form-select-sm">
                                    <option value="">Escolha</option>
                                    <option value="proprio">Próprio</option>
                                    <option value="locado">Locado</option>
                                </select>
                            </div>

                            <!-- Div para Família -->
                            <div id="filtro_familia" style="display: none; margin-left: 10px;">
                                <input type="text" id="filtro_familia_input" class="form-control form-control-sm" placeholder="Digite o nome da família">
                                <div id="filtro_familia_suggestions" class="list-group mt-1" style="max-width:11.5%; max-height: 200px; overflow-y: auto; display: none;">
                                    <!-- As sugestões serão inseridas aqui pelo JavaScript -->
                                </div>
                            </div>
                        </div>
                        </div>
HTML;

                if ($filtro && $valor) {
                    $html .= <<<HTML
                    <!-- Identificador de Filtro -->
                    <div id="filtro_alert" class="alert alert-info">
                        <strong>Filtro aplicado:</strong> <span id="filtro_texto">
HTML;
                    if ($filtro === 'id_familia') {
                        $familiaNome = array_filter($familia, function($f) use ($valor) {
                            return $f['id_familia'] == $valor;
                        });
                        $familiaNome = reset($familiaNome);
                        $html .= "Família: " . $familiaNome['ds_familia'];
                    } else {
                        $html .= ucfirst($filtro) . ": " . $valor;
                    }
                    $html .= <<<HTML
                        </span>
                    </div>
HTML;
                }

                $html .= <<<HTML
                          <!-- Tabela de Itens -->
                          <table class="table table-striped">
                              <thead>
                                  <tr>
                                      <th>ID</th>
                                      <th>Família</th>
                                      <th>Modelo</th>
                                      <th>Natureza</th>
                                      
                                  </tr>
                              </thead>
                              <tbody id="itens">
                                  <tr>
      
            HTML;
      
            
            foreach ($itens_locados as $item):
                $nm_familia = Item::getFamiliaNome($item['id_familia']);
                $html .="<td>".$item['cod_patrimonio']."</td>";
                $html .="<td>".$nm_familia."</td>";
                $html .="<td>".$item['ds_item']."</td>";
                $html .="<td>".$item['natureza']."</td>";
              
                $html .="</tr>";
            endforeach;
                       
            $html.= <<<HTML
      
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
            HTML;
      
                
            break;
            case 'quebrados':
                


                if ($filtro && $valor) {

                    $itens_filtrados = Item::getItensQuebrados( $filtro, $valor);
            
                    $itens_quebrados = $itens_filtrados['dados'];
                }



              $html.= <<<HTML
              <!-- TABELA -->
              <div class="container mt-4" id="containerFerramentas" style="display: block;">
                  <div class="row mt-4">
                      <div class="col-md-12">
                          <!-- Header com filtro -->
                          <div class="header-with-filter">
                              <h3><b><p class="text-primary">Todos os Itens Quebrados</p></b></h3>
                              <div class="filter-container">
                            <label for="filtro_principal" class="form-label visually-hidden">Filtro Principal</label>
                            <select id="filtro_principal" class="form-select form-select-sm filter-select" required>
                                <option value="">Escolha um filtro</option>
                                <option value="id_familia">Família</option>
                                <option value="natureza">Natureza</option>
                            </select>
        
                            <!-- Div para Natureza -->
                            <div id="filtro_natureza" style="display: none; margin-left: 10px;">
                                <select id="filtro_natureza_select" class="form-select form-select-sm">
                                    <option value="">Escolha</option>
                                    <option value="proprio">Próprio</option>
                                    <option value="locado">Locado</option>
                                </select>
                            </div>

                            <!-- Div para Família -->
                            <div id="filtro_familia" style="display: none; margin-left: 10px;">
                                <input type="text" id="filtro_familia_input" class="form-control form-control-sm" placeholder="Digite o nome da família">
                                <div id="filtro_familia_suggestions" class="list-group mt-1" style="max-width:11.5%; max-height: 200px; overflow-y: auto; display: none;">
                                    <!-- As sugestões serão inseridas aqui pelo JavaScript -->
                                </div>
                            </div>
                        </div>
                        </div>
HTML;

                if ($filtro && $valor) {
                    $html .= <<<HTML
                    <!-- Identificador de Filtro -->
                    <div id="filtro_alert" class="alert alert-info">
                        <strong>Filtro aplicado:</strong> <span id="filtro_texto">
HTML;
                    if ($filtro === 'id_familia') {
                        $familiaNome = array_filter($familia, function($f) use ($valor) {
                            return $f['id_familia'] == $valor;
                        });
                        $familiaNome = reset($familiaNome);
                        $html .= "Família: " . $familiaNome['ds_familia'];
                    } else {
                        $html .= ucfirst($filtro) . ": " . $valor;
                    }
                    $html .= <<<HTML
                        </span>
                    </div>
HTML;
                }

                $html .= <<<HTML
                          <!-- Tabela de Itens -->
                          <table class="table table-striped">
                              <thead>
                                  <tr>
                                      <th>ID</th>
                                      <th>Família</th>
                                      <th>Modelo</th>
                                      <th>Natureza</th>
                                      
                                  </tr>
                              </thead>
                              <tbody id="itens">
                                  <tr>
      
            HTML;
      
            
            foreach ($itens_quebrados as $item):
                $nm_familia = Item::getFamiliaNome($item['id_familia']);
                $html .="<td>".$item['cod_patrimonio']."</td>";
                $html .="<td>".$nm_familia."</td>";
                $html .="<td>".$item['ds_item']."</td>";
                $html .="<td>".$item['natureza']."</td>";
              
                $html .="</tr>";
            endforeach;
                       
            $html.= <<<HTML
      
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
            HTML;
      
                
                break;
            default:
                header('Location: ?pagina=itens');
                break;
        }
    }
      
      $html.= <<<HTML

       
        </main>

        HTML;
        if(isset($_SESSION['msg'])){
        $html.= "<script>alert('".$_SESSION['msg']."');</script>";
        unset($_SESSION['msg']);
        }
      $html.= <<<HTML

        </body>
        <script src="src/script.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filtroPrincipal = document.getElementById('filtro_principal');
            const filtroNatureza = document.getElementById('filtro_natureza');
            const filtroFamilia = document.getElementById('filtro_familia');
            
            const filtroNaturezaSelect = document.getElementById('filtro_natureza_select');
            const filtroFamiliaInput = document.getElementById('filtro_familia_input');
            const filtroFamiliaSuggestions = document.getElementById('filtro_familia_suggestions');
        HTML;
        $html .= "const familias = " . json_encode($familia) . ";";
        $html.= <<<HTML

            filtroPrincipal.addEventListener('change', function() {
                const filtro = filtroPrincipal.value;
                filtroNatureza.style.display = 'none';
                filtroFamilia.style.display = 'none';

                if (filtro === 'natureza') {
                    filtroNatureza.style.display = 'inline-block';
                } else if (filtro === 'id_familia') {
                    filtroFamilia.style.display = 'inline-block';
                }
            });

            filtroNaturezaSelect.addEventListener('change', function() {
                const filtro = filtroPrincipal.value;
                const valor = filtroNaturezaSelect.value;
                atualizarURL(filtro, valor);
            });

            filtroFamiliaInput.addEventListener('input', function() {
                const query = filtroFamiliaInput.value.toLowerCase();
                filtroFamiliaSuggestions.innerHTML = ''; // Limpa as sugestões anteriores

                if (query.length > 0) {
                    const filteredFamilias = familias.filter(familia => familia.ds_familia.toLowerCase().includes(query));
                    filteredFamilias.forEach(familia => {
                        const suggestionItem = document.createElement('a');
                        suggestionItem.href = '#';
                        suggestionItem.className = 'list-group-item list-group-item-action';
                        suggestionItem.textContent = familia.ds_familia;
                        suggestionItem.dataset.id = familia.id_familia;

                        suggestionItem.addEventListener('click', function(e) {
                            e.preventDefault();
                            filtroFamiliaInput.value = familia.ds_familia;
                            filtroFamiliaSuggestions.style.display = 'none';
                            atualizarURL('id_familia', familia.id_familia);
                        });

                        filtroFamiliaSuggestions.appendChild(suggestionItem);
                    });

                    filtroFamiliaSuggestions.style.display = 'block';
                } else {
                    filtroFamiliaSuggestions.style.display = 'none';
                }
            });

            // Fecha a lista de sugestões se o usuário clicar fora dela
            document.addEventListener('click', function(e) {
                if (!filtroFamiliaInput.contains(e.target) && !filtroFamiliaSuggestions.contains(e.target)) {
                    filtroFamiliaSuggestions.style.display = 'none';
                }
            });

            function atualizarURL(filtro, valor) {
                const url = new URL(window.location.href);
                url.searchParams.set('filtro', filtro);
                url.searchParams.set('valor', valor);
                window.location.href = url.toString();
            }
        });
        </script>
        </html>
      HTML;   

        return($html);
  }

}
?>
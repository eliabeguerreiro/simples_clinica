<?php
  
class ContentPainelUser
{

  public function renderHeader(){
 
    $html = <<<HTML
      <!DOCTYPE html>
      <html lang="pt-br">
      <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>GesQuip - Funcionários</title>
          <link rel="icon" type="image/png" href="src/img/favicon.png">
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
          <link rel="stylesheet" href="src/style.css">
      </head>

    HTML;   

    return($html);
}

    public function renderBody($pagina, $usuarios){

        //var_dump($itens);
        $nome = $_SESSION['data_user']['nm_usuario'];
        
        // Verifica se os parâmetros GET estão definidos
        $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : null;
        $valor = isset($_GET['valor']) ? $_GET['valor'] : null;



        function buildUrlUsers($newParams = []) {
            $queryParams = $_GET;
            foreach ($newParams as $key => $value) {
                if ($value === null) {
                    unset($queryParams[$key]);
                } else {
                    $queryParams[$key] = $value;
                }
            }
            /* Remove os parâmetros 'filtro' e 'v' se a página for alterada
            if (isset($newParams['pagina'])) {
                unset($queryParams['filtro']);
                unset($queryPdarams['valor']);
            }
            */
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
                                <a class="nav-link dropdown-toggle"  href="../lobby">Obras</a>
                            </li>

                            <!--GESTÃO DE USUARIOS-->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" id="usuario">Funcionários</a>
                                <ul class="dropdown-menu">

        HTML;                              
                                    
                            $html.="<li><a class='dropdown-item' href='" . buildUrlUsers(['pagina' => 'cadastro']) . "'>Cadastrar Funcionários</a></li>";
                            $html.="<li><a class='dropdown-item' href='" . buildUrlUsers(['pagina' => 'usuarios']) . "'>Funcionários Cadastrados</a></li>";

        $html.= <<<HTML

                                </ul>
                            </li>
                            
                        </ul>
                    </div>
                    <div class="d-flex ms-auto">
                        <a href="?sair=1" class="btn btn-danger btn-sm">Sair</a>
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

    /*
      if ($filtro && $valor) {

        $itens_filtrados = Users::getFuncionarios(null);

        $itens = $itens_filtrados['dados'];
      }
    */
              



      if (isset($pagina)) {
        switch ($pagina) {
           case 'cadastro':
            
            $html.= <<<HTML
              <div class="main-content" id="mainContent">
              <div class="container mt-4" id="novoItem" style="display: block;">
                <div class="row">
                  <div class="col-md-12">                    
                      <h3><b><p class="text-primary">Novo Funcionário</p></b></h3>
                      <form method='POST' action='' id="formNovoItem">
                        <div class="mb-3">
                            <label for="nm_usuario" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nm_usuario" placeholder="Nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" required>
                        </div>
                        <div class="mb-3">
                            <label for="nr_contato" class="form-label">Contato</label>
                            <input type="text" class="form-control" id="nr_contato" name="nr_contato" placeholder="DDD+9+0000-0000" required>
                        </div>
                        <div class="mb-3">
                            <label for="matricula" class="form-label">Matricula</label>
                            <input type="number" class="form-control" id="matricula" name="matricula" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_obra" class="form-label">Obra</label>
                            <select class="form-select" id="id_obra" name="id_obra" required>
                                <option value="">Selecione uma obra</option>
                                <option value="1">Obra 1</option>
                                <option value="2">Obra 2</option>
                            </select>
                        </div>
                                                <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nível de permissão de acesso</label>
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="nv_permissao_1" name="nv_permissao" value="1" required>
                                            <label class="form-check-label" for="nv_permissao_1">Nível 1</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="nv_permissao_2" name="nv_permissao" value="2" required>
                                            <label class="form-check-label" for="nv_permissao_2">Nível 2</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="nv_permissao_3" name="nv_permissao" value="3" required>
                                            <label class="form-check-label" for="nv_permissao_3">Nível 3</label>
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
            case 'usuarios':



              $html.= <<<HTML
        <!-- TABELA -->
        <div class="container mt-4" id="containerFerramentas" style="display: block;">
            <div class="row mt-4">
                <div class="col-md-12">
                    <!-- Header com filtro -->
                    <div class="header-with-filter">
                        <h3><b><p class="text-primary">Todos os Funcionários</p></b></h3>

                    </div>
HTML;

                if ($filtro && $valor) {
                    $html .= <<<HTML
                    <!-- Identificador de Filtro -->
                    <div id="filtro_alert" class="alert alert-info">
                        <strong>Filtro aplicado:</strong> <span id="filtro_texto">
HTML;
                    
                    $html .= ucfirst($filtro) . ": " . $valor;
                    
                    $html .= <<<HTML
                        </span>
                    </div>
HTML;
                }

                $html .= <<<HTML
                            <!-- Tabela de Equipamentos -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Contato</th>
                                <th>Nível</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="itens">
                            <tr>

      HTML;

      
      foreach ($usuarios as $funcionario):

          
          $html .="<td>".$funcionario['id_usuario']."</td>";
          $html .="<td>".$funcionario['nm_usuario']."</td>";
          $html .="<td>".$funcionario['nr_contato']."</td>";
          $html .="<td>".$funcionario['nv_permissao']."</td>";
          $html .= "<td><button class='btn btn-success btn-sm atualiza-button' data-bs-toggle='modal' data-bs-target='#atualizaModal' data-id='".$funcionario['id_usuario']."'>Editar</button>   ";
          $html .="<a href='apagar.php?id=".$funcionario['id_usuario']."' class='btn btn-danger btn-sm' >Apagar</a></td>";
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
                        <h5 class="modal-title" id="atualizaModalLabel">Atualizar Modelo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="novoNome" class="form-label">Nome do Funcionário</label>
                        <input type="text" id="novoNome" class="form-control" placeholder="digite o novo nome" required>
                    </div>
                    <div class="modal-body">
                        <label for="novoContato" class="form-label">Contato</label>
                        <input type="text" id="novoContato" class="form-control" placeholder="digite o novo contato" required>
                    </div>
                    <div class="modal-body">
                        <label for="nv_permissao" class="form-label">Nível de permissão de acesso</label>
                        <select>
                            <option value="1">Nível 1</option>
                            <option value="2">Nível 2</option>
                            <option value="3">Nível 3</option>
                        </select>
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
            default:
                header('Location: ?pagina=cadatro');
                break;
        }
    }
      
      $html.= <<<HTML

       
        </main>

        </body>
        <script src="src/script.js"></script>
        <script>
        
        </script>
        </html>
      HTML;   

        return $html;
  }

}

<?php
include_once"../../classes/painel.class.php";

// if(Usuarios::verificar($conn, $headers)){  }  
class Item
{

    public static function getItemNome($id_item)
{
    if ($id_item) {
        $id_item = (int)$id_item;
        $db = DB::connect();
        $rs = $db->prepare("SELECT ds_item FROM item WHERE id_item = :id_item ");
        $rs->bindParam(':id_item', $id_item, PDO::PARAM_INT);
        $rs->execute();
        $resultado = $rs->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado['ds_item']; // Retorna o nome da família
        }
    }

    return null; 
}
    public static function getFamiliaNome($id_familia)
{
    if ($id_familia) {
        $id_familia = (int)$id_familia;
        $db = DB::connect();
        $rs = $db->prepare("SELECT ds_familia FROM familia WHERE id_familia = :id_familia");
        $rs->bindParam(':id_familia', $id_familia, PDO::PARAM_INT);
        $rs->execute();
        $resultado = $rs->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado['ds_familia']; // Retorna o nome da família
        }
    }

    return null; 
}

    public static function reservaItem($id, $mov){    
        $db = DB::connect();

        $rs = $db->prepare("SELECT id_responsavel FROM movimentacao WHERE id_movimentacao = $mov");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        $id_responsavel = ($resultado[0]['id_responsavel']);

        $rs = $db->prepare(query: "SELECT nv_permissao FROM usuarios WHERE id_usuario = $id_responsavel");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        $nv_perm_user = ($resultado[0]['nv_permissao']);

        $rs = $db->prepare("SELECT nv_permissao FROM item WHERE id_item = $id");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        $nv_perm_item = ($resultado[0]['nv_permissao']);

        if($nv_perm_item > $nv_perm_user){
            $_SESSION['msg'] = "<div  class='container mt-4'><div class='msg error' ><i class='fas fa-exclamation-circle'></i>Usuário de Nivel baixo.</div></div>";
        }

        $rs = $db->prepare("UPDATE item SET nr_disponibilidade = $mov WHERE id_item = $id");
        $rs->execute();
        $rows = $rs->rowCount();
        if ($rows > 0){
            return true;
        }
        
    }




    public static function devolverItem($id_item, $id_movimentacao, $user){
        $dt = date('Y-m-d H:i:s');
        $db = DB::connect();
        
        // primeiro eu indentifico o id_item_movimentacao que está associado ao item e a movimentação
        
        $rs = $db->prepare("SELECT id_item_movimentacao FROM item_movimentacao WHERE id_item = $id_item and id_movimentacao = $id_movimentacao");
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        $id_item_mov = $resultado[0]['id_item_movimentacao'];
        

        // depois eu atualizo a data de devolução do item_movimentacao
        $rs = $db->prepare("UPDATE item_movimentacao SET dt_devolucao = '$dt', id_autor_final = $user WHERE id_item_movimentacao = $id_item_mov");
        $rs->execute();
        $rows = $rs->rowCount();

        if($rows > 0){ 
            echo '<script>';
            echo 'console.log("Tabela item-movimento atualizada");';
            echo '</script>';
                       
            // depois eu atualizo a disponibilidade do item

            $rs = $db->prepare("UPDATE item SET nr_disponibilidade = 1 WHERE id_item = $id_item");
            $rs->execute();
            $rows = $rs->rowCount();

            if($rows > 0){  

                echo '<script>';
                echo 'console.log("Disponibilidade atualizada");';
                echo '</script>';
                return true;
            }
        }

    }


    public static function getItensReservados($id_moviment) {
        $obra = intval($_SESSION['obra_atual']);

        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM item WHERE nr_disponibilidade = :id_moviment and desativado is NULL and id_obra = $obra ORDER BY id_item DESC");
        $rs->bindParam(':id_moviment', $id_moviment, PDO::PARAM_INT);
        $rs->execute();
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getItensDevolvidos($id ){
        
        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM item_movimentacao WHERE id_movimentacao = $id and dt_devolucao IS NOT NULL ");
        $rs->execute();
        $rows = $rs->rowCount();

        if($rows > 0){ 
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }else{
            return ["dados" => null];

        }
        
    }

    public static function getItens($id = null, $nm_filtro = null, $filtro = null){

        $obra = intval($_SESSION['obra_atual']);

        if($id){


            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM item WHERE id_item = $id and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }elseif($nm_filtro){
            //echo("SELECT * FROM item WHERE $nm_filtro = '$filtro'");
            $db = DB::connect(); 
            $rs = $db->prepare("SELECT * FROM item WHERE $nm_filtro = '$filtro' and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];
        
        
        }else{


            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM item WHERE desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];
        }
    }

    public static function getItensDisponiveis($nm_filtro = null, $filtro = null){
        $obra = intval($_SESSION['obra_atual']);
        
        if($nm_filtro){
            //echo("SELECT * FROM item WHERE $nm_filtro = '$filtro'");
            $db = DB::connect(); 
            $rs = $db->prepare("SELECT * FROM item WHERE nr_disponibilidade = 1 and $nm_filtro = '$filtro' and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }  else{            
            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM item WHERE nr_disponibilidade = 1 and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];
        }

    }
    public static function getItensQuebrados($nm_filtro = null, $filtro = null){
        $obra = intval($_SESSION['obra_atual']);

        if($nm_filtro){
            //echo("SELECT * FROM item WHERE $nm_filtro = '$filtro'");
            $db = DB::connect(); 
            $rs = $db->prepare("SELECT * FROM item WHERE nr_disponibilidade = 999999999 and $nm_filtro = '$filtro' and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }  else{            
            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM item WHERE nr_disponibilidade = 999999999 and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];
        }
      

    }


    public static function getItensLocados($nm_filtro = null, $filtro = null){
        $obra = intval($_SESSION['obra_atual']);
        
        if($nm_filtro){
            //echo("SELECT * FROM item WHERE $nm_filtro = '$filtro'");
            $db = DB::connect(); 
            $rs = $db->prepare("SELECT * FROM item WHERE nr_disponibilidade = 0 and $nm_filtro = '$filtro' and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }  else{            
            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM item WHERE nr_disponibilidade = 0 and desativado is NULL and id_obra = $obra order by id_item desc");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];
        }
      

    }

    public static function setItem($data)
{
    // Validação básica dos dados recebidos
    if (
        !isset($data['familia']) || empty($data['familia']) ||
        !isset($data['nome']) || empty($data['nome']) ||
        !isset($data['natureza']) || empty($data['natureza']) ||
        !isset($data['nv_permissao']) || empty($data['nv_permissao']) ||
        !isset($data['tipo']) || empty($data['tipo'])
    ) {
        $_SESSION['error'] = 'Todos os campos são obrigatórios!';
        return false;
    }

    // Conexão com o banco de dados
    $db = DB::connect();

    // Preparação da query SQL para inserir o item
    $sql = "INSERT INTO item (id_familia, ds_item, natureza, nv_permissao, tipo, id_obra) 
            VALUES (:familia, :nome, :natureza, :nv_permissao, :tipo, :obra)";
    $stmt = $db->prepare($sql);

    // Associação dos parâmetros para evitar injeção SQL
    $stmt->bindParam(':familia', $data['familia'], PDO::PARAM_INT);
    $stmt->bindParam(':nome', $data['nome'], PDO::PARAM_STR);
    $stmt->bindParam(':natureza', $data['natureza'], PDO::PARAM_STR);
    $stmt->bindParam(':nv_permissao', $data['nv_permissao'], PDO::PARAM_INT);
    $stmt->bindParam(':tipo', $data['tipo'], PDO::PARAM_STR);
    $stmt->bindParam(':obra', $_SESSION['obra_atual'], PDO::PARAM_STR);

    // Execução da query
    if ($stmt->execute()) {
        // Obter o ID do item recém-inserido
        $id_item = $db->lastInsertId();

        // Gerar o novo código de patrimônio
        $cod_patrimonio = "I" . $id_item . "F" . $data['familia'] . $data['tipo'];

        // Atualizar o item com o novo código de patrimônio
        $updateSql = "UPDATE item SET cod_patrimonio = :cod_patrimonio WHERE id_item = :id_item";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->bindParam(':cod_patrimonio', $cod_patrimonio, PDO::PARAM_STR);
        $updateStmt->bindParam(':id_item', $id_item, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            $_SESSION['msg'] = "<div class='container mt-4'><div class='msg success'><i class='fas fa-check-circle'></i>Item cadastrado com sucesso!</div></div>";
            return true;
        } else {
            $_SESSION['msg'] = "<div class='container mt-4'><div class='msg error'><i class='fas fa-exclamation-circle'></i>Erro ao atualizar o código de patrimônio.</div></div>";
            return false;
        }
    } else {
        $_SESSION['msg'] = "<div class='container mt-4'><div class='msg error'><i class='fas fa-exclamation-circle'></i>Erro ao cadastrar o item.</div></div>";
        return false;
    }
}

public static function desativarItem($id)
{
    // Check if the item is available for deactivation
    $db = DB::connect();
    $rs = $db->prepare("SELECT nr_disponibilidade FROM item WHERE id_item = :id");
    $rs->bindParam(':id', $id, PDO::PARAM_INT);
    $rs->execute();
    $result = $rs->fetch(PDO::FETCH_ASSOC);

    if ($result['nr_disponibilidade'] != 1) {
        // Item is not available, redirect with error message
        $_SESSION['msg'] = "<div class='container mt-4'><div class='msg error'><i class='fas fa-exclamation-circle'></i>Erro: O item não pode ser desativado pois está em movimentação.</div></div>";
        header('Location: index.php');
        exit;
    }

    // Deactivate the item
    $updateStmt = $db->prepare("UPDATE item SET desativado = 1 WHERE id_item = :id");
    $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($updateStmt->execute()) {
        $_SESSION['msg'] = "<div class='container mt-4'><div class='msg success'><i class='fas fa-check-circle'></i>Item desativado com sucesso!</div></div>";
        header('Location: index.php?pagina=itens');
        exit;
    } else {
        $_SESSION['msg'] = "<div class='container mt-4'><div class='msg error'><i class='fas fa-exclamation-circle'></i>Erro ao desativar o item.</div></div>";
        header('Location: index.php?pagina=itens');
        exit;
    }
}

    public static function updateItem($id, $data)
{
    if (!$id || empty($data)) {
        $_SESSION['msg'] = "<div class='container mt-4'><div class='msg error'><i class='fas fa-exclamation-circle'></i>Erro: Dados inválidos para atualização.</div></div>";
        return false;
    }

    // Conexão com o banco de dados
    $db = DB::connect();

    // Montagem dinâmica da query UPDATE
    $campos = [];
    $valores = [];
    foreach ($data as $campo => $valor) {
        $campos[] = "$campo = :$campo";
        $valores[":$campo"] = $valor;
    }

    $query = "UPDATE item SET " . implode(", ", $campos) . " WHERE id_item = :id_item";
    $stmt = $db->prepare($query);

    // Bind dos parâmetros
    $valores[':id_item'] = $id;
    foreach ($valores as $param => $valor) {
        $stmt->bindValue($param, $valor);
    }

    // Execução da query
    if ($stmt->execute()) {
        $_SESSION['msg'] = "<div class='container mt-4'><div class='msg success'><i class='fas fa-check-circle'></i>Item atualizado com sucesso!</div></div>";
        return true;
    } else {
        $_SESSION['msg'] = "<div class='container mt-4'><div class='msg error'><i class='fas fa-exclamation-circle'></i>Erro ao atualizar o item.</div></div>";
        return false;
    }
}
    public static function getFamilia($id = null)
    {

        if($id){

            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM familia WHERE id_familia = $id");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }else{
            
            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM familia ");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }

    }
  
}

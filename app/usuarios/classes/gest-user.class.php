<?php
include_once"../../classes/painel.class.php";

class User
{

    public static function getFuncionarioNome($id_func)
{
    if ($id_func) {
        $id_func = (int)$id_func;
        $db = DB::connect();
        $rs = $db->prepare("SELECT nm_usuario FROM usuarios WHERE id_usuario = :id_usuario");
        $rs->bindParam(':id_usuario', $id_func, PDO::PARAM_INT);
        $rs->execute();
        $resultado = $rs->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado['nm_usuario']; // Retorna o nome da família
        }
    }

    return null; 
}
    public static function getFuncionarios($id = null)
    {
        if($id){
            
            $db = DB::connect();
            $rs = $db->prepare("SELECT id_usuario, cpf, matricula, nm_usuario, nr_contato, id_empresa, tp_usuario, nv_permissao, dt_cadastro FROM usuarios where id_usuario = $id");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }else{
            $db = DB::connect();
            $rs = $db->prepare("SELECT id_usuario, cpf, matricula, nm_usuario, nr_contato, id_empresa, tp_usuario, nv_permissao, dt_cadastro FROM usuarios ");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];


        }
        

    }


    public static function updateUsuario($data)
    {
        $db = DB::connect();
        $id = intval($data['id_usuario']);
        $nome = $data['nm_usuario'];
        $contato = $data['nr_contato'];
        $permissao = $data['nv_permissao'];
       
        $rs = $db->prepare("UPDATE usuarios SET nm_usuario = :nm_usuario, nr_contato = :nr_contato, nv_permissao = :permissao WHERE id_usuario = :id_usuario");
        $rs->bindParam(':id_usuario', $id, PDO::PARAM_INT); 
        $rs->bindParam(':nm_usuario', $nome, PDO::PARAM_STR);
        $rs->bindParam(':nr_contato', $contato, PDO::PARAM_STR);
        $rs->bindParam(':permissao', $permissao, PDO::PARAM_STR);
        $rs->execute();

        $rows = $rs->rowCount();
        if ($rows > 0){
            $_SESSION['msg'] = "<div  class='container mt-4'><div class='msg success'><i class='fas fa-check-circle'></i>Usuário atualizado com sucesso!</div></div>";
            return true;
        } else {
            $_SESSION['msg'] = "<div  class='container mt-4'><div class='msg error' ><i class='fas fa-exclamation-circle'></i> Erro ao atualizar usuário.</div></div>";
        }   
    }

    public static function deleteUsuario($id){

        $db = DB::connect();
        $rs = $db->prepare("DELETE FROM usuarios WHERE id_usuario = $id");
        $rs->execute();
        $rows = $rs->rowCount();
        if ($rows > 0){
            $_SESSION['mag'] = 'Item cadastrado com Sucesso!';
            return true;
        }   
      
    }


    public static function getUsuarios($id){
        if($id){

            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM usuarios WHERE id_usuario = $id");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }else{

            $db = DB::connect();
            $rs = $db->prepare("SELECT * FROM usuarios ");
            $rs->execute();
            $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];

        }
    }

    public static function setUsuario($data)
    {

        $password = password_hash($data['matricula'], PASSWORD_DEFAULT);

        $db = DB::connect();
        $rs = $db->prepare("INSERT INTO usuarios (login , senha, nm_usuario, nr_contato, id_empresa, tp_usuario, nv_permissao, matricula, cpf)
         VALUES('".$_SESSION['data_user']['login']."','".$password."','".$data['nm_usuario']."','".$data['nr_contato']."', 1, 'user', ".$data['nv_permissao'].", '".$data['matricula']."', '".$data['cpf']."')");
        $rs->execute();
        $rows = $rs->rowCount();
        if ($rows > 0){
            $id_usuario = $db->lastInsertId();

            $db = DB::connect();
            $rs = $db->prepare("INSERT INTO usuario_obra (id_usuario , id_obra, id_empresa)
             VALUES(".$id_usuario.",".$data['id_obra'].", ".$_SESSION['data_user']['id_empresa'].")");
            $rs->execute();
            $rows = $rs->rowCount();


            $_SESSION['msg'] = "<div  class='container mt-4'><div class='msg success'><i class='fas fa-check-circle'></i>Funcionário criado com sucesso!</div></div>";
            return true;
        } else {
            $_SESSION['msg'] = "<div  class='container mt-4'><div class='msg error' ><i class='fas fa-exclamation-circle'></i> Erro ao criar Funcionário.</div></div>";
        }   
    }
        
       
    

  
}
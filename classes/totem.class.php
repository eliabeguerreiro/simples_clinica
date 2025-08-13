<?php

class Totem
{
    public static function GetSenhas($status){

        $db = DB::connect();
        $rs = $db->prepare("SELECT * FROM totem_senha WHERE status = :status ORDER BY id DESC");
        $rs->bindParam(':status', $status, PDO::PARAM_STR);
        $rs->execute();
        $resultado = $rs->fetchAll(PDO::FETCH_ASSOC);
        return ["dados" => $resultado];

    }

   public static function SetSenha($tipo, $clinica_id){

        $db = DB::connect();
        $rs = $db->prepare("INSERT INTO totem_senha (clinica_id, tipo_senha, status, senha_numero) VALUES (:clinica_id, :tipo_senha, :status, :senha_numero)");
        $rs->bindParam(':tipo_senha', $tipo, PDO::PARAM_STR);
        $status = 'esperando';
        $rs->bindParam(':status', $status, PDO::PARAM_STR);
        $rs->bindParam(':clinica_id', $clinica_id, PDO::PARAM_INT);

        $valor_unico = "$tipo".uniqid();
        $rs->bindParam(':senha_numero', $valor_unico, PDO::PARAM_STR);

        if ($rs->execute()) {
            $id = $db->lastInsertId();
            $_SESSION['msg'] = "Senha ".$id." gerada com sucesso.";
            return true;
        } else {
            $_SESSION['msg'] = "Erro ao gerar senha.";
            return false;
        }
    }

    public static function GetSenha ($id = null){

        $db = DB::connect();

        if ($id) {
            $rs = $db->prepare("SELECT * FROM totem_senha WHERE id = :id");
            $rs->bindParam(':id', $id, PDO::PARAM_INT);
            $rs->execute();
            $resultado = $rs->fetch(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];
        }else{
            $rs = $db->prepare("SELECT * FROM totem_senha ORDER BY id DESC LIMIT 1");
            $rs->execute();
            $resultado = $rs->fetch(PDO::FETCH_ASSOC);
            return ["dados" => $resultado];
        }
    }

}

    

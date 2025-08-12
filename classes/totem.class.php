<?php

class Painel
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
        $rs = $db->prepare("INSERT INTO totem_senha (clinica_id, tipo_senha, status) VALUES (:clinica_id, :tipo_senha, :status)");
        $rs->bindParam(':tipo_senha', $tipo, PDO::PARAM_STR);
        $status = 'Pendente';
        $rs->bindParam(':status', $status, PDO::PARAM_STR);
        $rs->bindParam(':clinica_id', $clinica_id, PDO::PARAM_INT);
        if ($rs->execute()) {
            $id = $db->lastInsertId();
            $_SESSION['msg'] = "Senha ".$id." gerada com sucesso.";
            return true;
        } else {
            $_SESSION['msg'] = "Erro ao gerar senha.";
            return false;
        }
    }
}
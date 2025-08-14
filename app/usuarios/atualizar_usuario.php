<?php
session_start();
include_once "classes/gest-user.class.php";
include_once "classes/db.class.php";

// Verifica se os dados necessários foram enviados via POST
if ($_POST){

$Update =  User::updateUsuario($_POST);
    
        if($Update){
            echo "Usuário atualizado com sucesso";
        } else {
            echo "Erro ao atualizar usuário";
        }

} else {
    echo "Erro na requisição";
}
?>
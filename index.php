<?php
session_start();
include_once "classes/totem.class.php";
include_once "classes/db.class.php";


$senha = Totem::GetSenha();

//var_dump($senha);

$empresa = 1;
$tp_senha = 'comum';
$status = 'esperando';


$snhas = Totem::GetSenha();

var_dump($snhas['dados']['id']);
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if($novaSenha = Totem::SetSenha($tp_senha, $empresa)) {
    var_dump($_SESSION['msg']);
 } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

  <form action="" method="post">
    <button type="submit">Tirar senha</button>
  </form>

</body>
</html>
*/

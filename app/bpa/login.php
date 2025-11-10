<?php
session_start(); // Sempre iniciar a sessão no início
include "../../classes/index.class.php";
include "../../classes/db.class.php";
var_dump($_POST);
echo"<br><br><br><br>";
var_dump($_SESSION);



/* 
$db = DB::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo"opa";
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Buscar usuário pelo login ou cpf
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE login = ? OR cpf = ?");
    $stmt->execute([$username, $username]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['senha'])) {
        // Salvar dados na sessão
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'login' => $usuario['login'],
            'nm_usuario' => $usuario['nm_usuario']
        ];

        // Redirecionar para a página app
        header("Location: ./app");
        exit();
    } else {
        // Login inválido
        echo "<script>alert('Login ou senha incorretos'); window.location.href='index.php';</script>";
        exit();
    }
}
*/
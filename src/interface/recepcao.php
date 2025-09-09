<?php
require_once __DIR__ . '/../database/connection.db.php'; // Inclui a classe de conexão com o banco de dados 
require_once __DIR__ . '/../service/tokenService.php'; // Inclui a classe do service 

// Variavel para a mensagem de sucesso e de erro
$mensagem = '';
    if(isset($_GET['sucesso'])) {
        $mensagem = htmlspecialchars(urldecode($_GET['sucesso']));
    }
    if(isset($_GET['erro'])) {
        $mensagem = htmlspecialchars(urldecode($_GET['erro']));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome_paciente = $_POST['nome_paciente'] ?? '';
        $telefone_paciente = $_POST['telefone_paciente'] ?? '';
        $email_paciente = $_POST['email_paciente'] ?? '';
        $data_nascimento = $_POST['data_nascimento'] ?? '';

    if (!empty($nome_paciente)) { // verifica se a variavel esta vazia
        $conn = ConnectionDB::getConnection();
        $resultado = tokenService::gerarSenha($conn, $nome_paciente, $telefone_paciente, $email_paciente, $data_nascimento);

        if ($resultado['sucesso']) {
            header('Location: recepcao.php?sucesso=' . urlencode("Senha do paciente: {$resultado['nome']}: {$resultado['token']}"));
            exit();
        }
    } else {
        header('Location: recepcao.php?erro=' . urlencode($resultado['erro']));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Painel da Recepção</title>
</head>
<body onload="exibirMensagem()">

    <script> 
        function exibirMensagem() {
            const mensagem = "<?php echo $mensagem; ?>";
                if(mensagem !== "") {
                    alert(mensagem);
                }
        }
    </script>

    <div class="form-container"> 
        <h1>Novo Paciente</h1>

        <form method="POST" action="recepcao.php">
            <label for="nome_paciente">Nome do Paciente:</label>
            <input type="text" id="nome_paciente" name="nome_paciente" required>

            <label for="telefone_paciente">Telefone do Paciente:</label>
            <input type="tel" id="telefone_paciente" name="telefone_paciente" required>

            <label for="email_paciente">Email do Paciente:</label>
            <input type="text" id="email_paciente" name="email_paciente" required>

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" required>

            <button type="submit">Gerar Senha</button>
        </form>
    </div>

        <div class="chamar-container">
            <h1>Chamar Paciente</h1>
            <form action="chamar.php" method="POST">
                <button type="submit">Chamar Próximo Paciente</button>
            </form>
        </div>

        <div class="finalizar-container">
            <h1>Finalizar Atendimento</h1>
            <form action="finalizar.php" method="POST">
                <button type="submit">Finalizar Atendimento</button>
            </form>
        </div>
</body>
</html>
<?php
require_once __DIR__ . '/../database/connection.db.php';
$conn = ConnectionDB::getConnection();

// Busca o token "Em atendimento" se houver e armazena no $token_atendimento
$stmt_atendimento = $conn->prepare("SELECT numero_token FROM tokens WHERE status = 'Em atendimento' ORDER BY data_criacao DESC LIMIT 1");
$stmt_atendimento->execute();
$token_atendimento = $stmt_atendimento->fetch(PDO::FETCH_ASSOC);

// Busca o token "Em espera" e armazena no $tokens_espera
$stmt_espera = $conn->prepare("SELECT numero_token FROM tokens WHERE status = 'Em espera' ORDER BY data_criacao ASC LIMIT 10");
$stmt_espera->execute();
$tokens_espera = $stmt_espera->fetchall(PDO::FETCH_COLUMN, 0);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Painel de Atendimento</title>
    <meta http-equiv="refresh" content="5;url=recepcao.php">
</head>
<body>
    <h1>Próximo a ser atendido(a):</h1>
        <?php 
            if ($token_atendimento): ?>
                <div class="atendimento">
                    <?php echo htmlspecialchars($token_atendimento['numero_token']); ?>
                </div>
        <?php else: ?>
            <div>Nenhum paciente em atendimento</div>
        <?php endif; ?>

    <h2>Em espera:</h2>
        <ul class="lista_espera">
            <?php 
                foreach ($tokens_espera as $token): ?>
                    <li>
                        <?php echo htmlspecialchars($token); ?>
                    </li>
            <?php endforeach; ?>
        </ul>
</body>
</html>
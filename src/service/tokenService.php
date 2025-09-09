<?php
require_once __DIR__ . '/../database/connection.db.php';

class TokenService { 
    // Classe publica de gerar senha
    public static function GerarSenha($conn, $nome_paciente, $telefone_paciente, $email_paciente, $data_nascimento) {
        $conn = ConnectionDB::getConnection();

        try {
            $conn->beginTransaction(); // Inicia uma transação 
            $stmt_paciente = $conn->prepare("INSERT INTO pacientes (nome_paciente, telefone_paciente, email_paciente, data_nascimento, data_cadastro) VALUES (?, ?, ?, ?, NOW())");
            $stmt_paciente->execute([$nome_paciente, $telefone_paciente, $email_paciente, $data_nascimento]);

            $id_paciente = $conn->lastInsertId(); //Pega o ultimo id inserido
            $numero_token = 'P-' . $id_paciente; 

            $stmt_token = $conn->prepare("INSERT INTO tokens (numero_token, id_paciente, status, data_criacao) VALUES (?,?,?, NOW())");
            $stmt_token->execute([$numero_token, $id_paciente, 'Em espera']);

            $conn->commit(); //Quando todas as operações são concluidas com sucesso
            return ["sucesso" => true, "token" => $numero_token, "nome" => $nome_paciente];

        } 
        catch (PDOExceptio $e) {
            $conn->rollBack(); //Quando nenhuma delas é concluida
            return ["sucesso" => false, "erro" => "Erro ao gerar senha: " . $e->getMessage()];
        }
    }

    public static function chamarProximo() { //Cria uma função publica para chamar a proxima senha
        $conn = ConnectionDB::getConnection();

        try {
            $conn->beginTransaction();
            $stmt_select = $conn->prepare("SELECT numero_token FROM tokens WHERE status = 'Em espera' ORDER BY data_criacao ASC LIMIT 1");
            $stmt_select->execute();
            $token_a_chamar = $stmt_select->fetch(PDO::FETCH_ASSOC);

                if ($token_a_chamar) { //se for true
                        $numero_token = $token_a_chamar['numero_token'];
                        $stmt_update = $conn->prepare("UPDATE tokens SET status = 'Em atendimento' WHERE numero_token = ?");
                        $stmt_update->execute([$numero_token]);

                        $conn->commit();//QUando todas as operações são concluidas
                        return ["sucesso" => true];
                    } else { //se for false
                        return ["sucesso" => false, "erro" => "Nenhum paciente na fila de espera."];
                    }
        } catch (PDOException $e) {
            $conn->rollBack();
            return ["sucesso" => false, "erro" => "Erro ao chamar o paciente: " . $e->getMessage()];
        }
    }

    public static function finalizarAtendimento() { ///Cria uma função de finalizar atendimento
        $conn = ConnectionDB::getConnection();

        try {
            $conn->beginTransaction();
            $stmt_select = $conn->prepare("SELECT numero_token FROM tokens WHERE status = 'Em atendimento' ORDER BY data_criacao ASC LIMIT 1");
            $stmt_select->execute();
            $token_a_finalizar = $stmt_select->fetch(PDO::FETCH_ASSOC);

                if ($token_a_finalizar) {
                        $numero_token = $token_a_finalizar['numero_token'];
                        $stmt_update = $conn->prepare("UPDATE tokens SET status = 'Atendido' WHERE numero_token = ?");
                        $stmt_update->execute([$numero_token]);

                        $conn->commit();
                        return ["sucesso" => true, "token" => $numero_token];
                    } else {
                        return ["sucesso" => false, "erro" => "Nenhum paciente em atendimento"];
                    }
        } catch (PDOException $e) {
            $conn->callBack();
            return ["sucesso" => false, "erro" => "Erro ao finalizar o atendimento: " . $e->getMessage()];
        }
    }
}
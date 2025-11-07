<?php

/*
class DB
{
    public static function connect()
    {

        $host = '127.0.0.1:3333';
        $user = 'root';
        $pass = 'hants12';
        $base = 'vivenciar';

        return new PDO("mysql:host={$host};dbname={$base};charset=UTF8;", $user, $pass);
    }
}



class DB
{
    public static function connect()
    {

        $host = '127.0.0.1:3333';
        $user = 'root';
        $pass = 'hants12';
        $base = 'vivenciar';

        return new PDO("mysql:host={$host};dbname={$base};charset=UTF8;", $user, $pass);
    }
}



/*
class DB
{
    public static function connect()
    {

        $host = '127.0.0.1:3333';
        $user = 'root';
        $pass = 'hants12';
        $base = 'vivenciar';

        return new PDO("mysql:host={$host};dbname={$base};charset=UTF8;", $user, $pass);
    }
}
*/

class DB
{
    public static function connect()
    {
        // 🔧 AMBIENTE DE TESTES (RECOMENDADO PARA DESENVOLVIMENTO)
        $host = '127.0.0.1';          // MySQL local (dentro do túnel SSH)
        $port = 3306;                 // Porta padrão do MySQL
        $user = 'teste_user';         // Usuário dedicado ao ambiente de testes
        $pass = 'Teste@Senha2025!';   // Senha do usuário MySQL (ajuste se necessário)
        $base = 'ambiente_teste_db';  // Nome do banco de testes

        try {
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$base};charset=UTF8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }
}

?>
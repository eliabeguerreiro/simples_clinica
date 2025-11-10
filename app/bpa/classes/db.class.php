<?php

/**
 * Classe de conexão com o banco de dados.
 * Centraliza a conexão PDO para todo o sistema.
 */

/* Outras conexões:


conexão pra usar no laragon:


 $host = '127.0.0.1';          // MySQL local
        $port = 3306;                 // Porta padrão
        $user = 'teste_user';         // Usuário dedicado
        $pass = 'Teste@Senha2025!';   // Senha
        $base = 'ambiente_teste_db';  // Nome do banco



*/





class DB
{
    /**
     * Retorna uma instância de PDO conectada ao banco.
     *
     * @return PDO
     * @throws PDOException Se houver erro na conexão.
     */
    public static function connect()
    {

        $host = '127.0.0.1';
        $port = 3333; 
        $user = 'root';
        $pass = 'hants12';
        $base = 'vivenciar';

        try {
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$base};charset=UTF8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }
}
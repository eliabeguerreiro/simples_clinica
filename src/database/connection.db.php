<?php

class ConnectionDB 
{
    private static $connection = null;

    public static function getConnection() 
    {
        if (self::$connection === null) {
            $host = 'localhost';
            $user = 'root';
            $pass = '1234';
            $base = 'token';
        
            try {  //tenta se conectar com o banco de dados!
                self::$connection = new PDO ("mysql:host={$host}; dbname={$base}; charset=UTF8;", $user, $pass);
                self::$connection-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) { //Se não conectar, mensagem de erro
                die("Erro na conexão: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
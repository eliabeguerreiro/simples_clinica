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

*/

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


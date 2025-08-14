<?php
class DB
{
    public static function connect()
    {

        $host = 'gesquip_tst.vpshost11463.mysql.dbaas.com.br:3306';
        $user = 'gesquip_tst';
        $pass = 'Passelithis@1';
        $base = 'gesquip_tst';

        return new PDO("mysql:host={$host};dbname={$base};charset=UTF8;", $user, $pass);
    }
}

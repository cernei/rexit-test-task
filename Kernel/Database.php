<?php

namespace Kernel;

use PDO;

class Database
{
    private static ?PDO $instance = NULL;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(): ?PDO
    {
        if (!isset(self::$instance)) {
            $config = [
                'host' => 'db',
                'dbname' => 'app_db',
                'username' => 'app_user',
                'password' => 'helloworld',
            ];
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            self::$instance = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password'], $pdo_options);
        }

        return self::$instance;
    }

}
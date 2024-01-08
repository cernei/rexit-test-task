<?php

namespace src\Migrations;

use Kernel\QueryBuilder;

class UsersMigration {

    const TABLE = 'users';

    function run() {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->createTable(self::TABLE, [
                'category' => 'varchar(255)',
                'firstname' => 'varchar(255)',
                'lastname' => 'varchar(255)',
                'email' => 'varchar(255)',
                'gender' => 'varchar(255)',
                'birthDate' => 'varchar(255)',
            ]
        );

    }

}
<?php

namespace src\Controllers;

use Kernel\QueryBuilder;

class DictionariesController {

    const TABLE = 'users';

    function _getSingleDictionary($name, $select): array
    {
        $queryBuilder = new QueryBuilder();

        $categories = $queryBuilder->table(self::TABLE)
            ->select($select)
            ->get();

        $result = [];
        foreach($categories as $value) {
            $result[] = $value[$name];
        }

        return $result;
    }

    function getAll() {
        return json_encode(['data' => [
            'category' => $this->_getSingleDictionary('category', 'DISTINCT(category)'),
            'gender' => ['male', 'female'],
            'age' => range('18', '90')
        ]]);

    }
}
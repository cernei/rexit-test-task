<?php

namespace src\Controllers;

use Kernel\QueryBuilder;
use src\Migrations\UsersMigration;

class UsersController {

    const TABLE = 'users';

    public function upload() {

        $queryBuilder = new QueryBuilder();

        if (!$queryBuilder->tableExists(self::TABLE)) {
            $migration = new UsersMigration();
            $migration->run();
        }

        if (isset($_FILES["dataset"]) && $_FILES["dataset"]["error"] == UPLOAD_ERR_OK) {

            $data = csvGenerator($_FILES["dataset"]["tmp_name"]);
            $batch = [];
            $i = 0;
            $fields = [
                'category',
                'firstname',
                'lastname',
                'email',
                'gender',
                'birthDate',
            ];
            $batchExecutionTimes = [];
            foreach ($data as $value) {
                // exclude header
                if ($i > 0) {
                    $batch[] = $value;
                }
                if (count($batch) >= 1000) {
                    $timeStart = microtime(1);
                    $queryBuilder->table(self::TABLE)->insertBatch($fields, $batch);
                    $batchExecutionTimes[] = microtime(1) - $timeStart;

                    $batch = [];
                }
                $i++;
            }
            // add the rest
            if (count($batch)) {
                $timeStart = microtime(1);
                $queryBuilder->table(self::TABLE)->insertBatch($fields, $batch);
                $batchExecutionTimes[] = microtime(1) - $timeStart;
            }

            return jsonResponse(['message' => 'OK', 'batchExecutionTimes' => $batchExecutionTimes]);
        } else {
            return jsonResponse(['message' => $_FILES["dataset"]["error"]]);
        }
    }

    private function _handleFilters($statement, $filters) {
        if ($filters['category'] ?? null) {
            $statement->addWhere('category', '=', $filters['category']);
        }
        if ($filters['gender'] ?? null) {
            $statement->addWhere('gender', '=', $filters['gender']);
        }
        if ($filters['birthDate'] ?? null) {
            $statement->addWhere('birthDate', '=', $filters['birthDate']);
        }
        if ($filters['ageFrom'] ?? null) {
            $dateFrom = getDateByYearsPastFromNow($filters['ageFrom']);
            $statement->addWhere('birthDate', '<=', $dateFrom);
        }
        if ($filters['ageTo'] ?? null) {
            $dateTo = getDateByYearsPastFromNow($filters['ageTo']);
            $statement->addWhere('birthDate', '>=', $dateTo);
        }

        return $statement;
    }

    public function paginate(array $requestParams = []) {
        $queryBuilder = new QueryBuilder();

        $filters = $requestParams['filters'] ?? [];

        if ($queryBuilder->tableExists(self::TABLE)) {
            $statement = $queryBuilder->table(self::TABLE);
            $statement = $this->_handleFilters($statement, $filters);

            $result = $statement->paginate($requestParams['page'], $requestParams['perPage']);

            return jsonResponse($result);
        } else {
            return jsonResponse(['message' => 'Table doesnt exist'], 400);
        }

    }


    public function exportCsv(array $requestParams = []) {
        $queryBuilder = new QueryBuilder();

        $filters = $requestParams['filters'] ?? [];

        $statement = $queryBuilder->table(self::TABLE);
        $statement = $this->_handleFilters($statement, $filters);

        $result = $statement->get();

        outputCsv('users_export', $result);
    }

    public function truncate() {
        $queryBuilder = new QueryBuilder();

        $queryBuilder->table(self::TABLE)->truncate();

        return jsonResponse(['message' => 'OK']);
    }
}
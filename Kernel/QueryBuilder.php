<?php

namespace Kernel;

use Exception;
use PDO;

class QueryBuilder
{
    private ?PDO $db;
    private string $table;
    private string $select = '*';
    private array $where = [];

    public function __construct(
    ) {
        $this->db = Database::getInstance();
    }

    public function table(string $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function addWhere(string $param, string $operator, string $value): static
    {
        $this->where[] = $param . $operator . "'" . $value . "'";

        return $this;
    }

    public function select(string $select): static
    {
        $this->select = $select;

        return $this;
    }

    function tableExists($table): bool
    {
        try {
            $result = $this->db->query("SELECT 1 FROM {$table} LIMIT 1");
        } catch (Exception $e) {
            return FALSE;
        }

        return $result !== FALSE;
    }

    public function insertBatch($keys, $valuesBatch): void
    {
        $statementKeys = implode(', ', $keys);
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $sql = "INSERT INTO ". $this->table ." (" . $statementKeys . ") VALUES (" . $placeholders . ")";

        $statement = $this->db->prepare($sql);
        if ($valuesBatch) {
            $this->db->beginTransaction();
            foreach ($valuesBatch as $row) {
                $statement->execute($row);
            }
            $this->db->commit();
        }
    }

    public function createTable($table, $fields): void
    {
        if ($fields) {
            $arr = [];
            foreach($fields as $fieldName => $fieldType) {
                $arr[] = $fieldName . ' '. $fieldType;
            }
            $fieldsStatement = implode(', ', $arr);
            $sql = "CREATE TABLE " . $table . " (" . $fieldsStatement. ");";

            $statement = $this->db->prepare($sql);
            $statement->execute();
        }

    }

    public function get(): bool|array
    {
        $sql = 'SELECT ' . $this->select . ' FROM ' . $this->table;
        if (count($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }
//        echo $sql;

        $req = $this->db->query($sql);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paginate($page = 1, $perPage = 10): array
    {
        $sql = 'SELECT ' . $this->select . ' FROM ' . $this->table;
        $sqlCount = 'SELECT COUNT(*) as total FROM ' . $this->table;
        if (count($this->where)) {
            $whereStatement = ' WHERE ' . implode(' AND ', $this->where);
            $sql .= $whereStatement;
            $sqlCount .= $whereStatement;
        }
        $sql .= ' LIMIT '. (($page - 1)  * $perPage) . ',' . $perPage;
//        echo $sql;

        $request = $this->db->query($sql);
        $data = $request->fetchAll(PDO::FETCH_ASSOC);

        $requestCount = $this->db->query($sqlCount);
        $total = $requestCount->fetch(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total['total'],
            'totalPages' => ceil($total['total'] / $perPage),
            'currentPage' => $page,
            'perPage' => $perPage
        ];
    }

    public function truncate(): void
    {
        $sql = "TRUNCATE TABLE " . $this->table;

        $statement = $this->db->prepare($sql);
        $statement->execute();
    }

}
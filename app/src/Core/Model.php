<?php

namespace Core;

class Model
{
    public function __construct(
        private \PDO $pdo,
        private string $table,
        private string $primaryKey,
        private string $className = '\stdClass',
        private array $constructorArgs = []
    ) {}

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :id'
        );
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
        return $stmt->fetch();
    }

    public function findByColumn(string $column, string $value, $orderBy = null, int $limit = 0)
    {
        $query = 'SELECT * FROM `' . $this->table . '` WHERE `' . $column . '` = :value';

        if ($orderBy != null) {
            $query .= ' ORDER BY ' . $orderBy;
        }

        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['value' => $value]);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
    }

    public function findAll($orderBy = null, int $limit = 0, int $offset = 0)
    {
        $query = 'SELECT * FROM ' . $this->table;

        if ($orderBy != null) {
            $query .= ' ORDER BY ' . $orderBy;
        }

        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset > 0) {
            $query .= ' OFFSET ' . $offset;
        }


        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
    }

    public function total()
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM `' . $this->table . '`');
        $stmt->execute();
        $row = $stmt->fetch();
        return $row[0];
    }

    public function save($record)
    {
        $entity = new $this->className(...$this->constructorArgs);
        try {
            if (empty($record[$this->primaryKey])) {
                unset($record[$this->primaryKey]);
            }
            $insertId = $this->insert($record);

            $entity->{$this->primaryKey} = $insertId;
        } catch (\PDOException $e) {
            $this->update($record);
        }

        foreach ($record as $key => $value) {
            if (!empty($value)) {
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                }
                $entity->$key = $value;
            }
        }
        return $entity;
    }

    private function update($values)
    {
        $query = ' UPDATE `' . $this->table . '` SET ';

        foreach ($values as $key => $value) {
            $query .= '`' . $key . '` = :' . $key . ',';
        }

        $query = rtrim($query, ',');

        $query .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';

        // Set the :primaryKey variable
        $values['primaryKey'] = $values['id'];

        $values = $this->processDates($values);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);
    }

    private function insert($values)
    {
        $query = 'INSERT INTO `' . $this->table . '` (';

        foreach ($values as $key => $value) {
            $query .= '`' . $key . '`,';
        }

        $query = rtrim($query, ',');

        $query .= ') VALUES (';

        foreach ($values as $key => $value) {
            $query .= ':' . $key . ',';
        }

        $query = rtrim($query, ',');

        $query .= ')';

        $values = $this->processDates($values);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        return $this->pdo->lastInsertId();
    }

    public function delete($field, $value)
    {
        $values = [':value' => $value];

        $stmt = $this->pdo->prepare('DELETE FROM `' . $this->table . '` WHERE `' . $field . '` = :value');

        $stmt->execute($values);
    }

    private function processDates($values)
    {
        foreach ($values as $key => $value) {
            if ($value instanceof \DateTime) {
                $values[$key] = $value->format('Y-m-d');
            }
        }

        return $values;
    }

    public function findMultiple(array $conditions)
    {
        $query = 'SELECT * FROM `' . $this->table . '` WHERE ';
        $whereClauses = [];

        foreach ($conditions as $column => $value) {
            $whereClauses[] = '`' . $column . '` = :' . $column;
        }

        $query .= implode(' AND ', $whereClauses);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($conditions);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
    }
}

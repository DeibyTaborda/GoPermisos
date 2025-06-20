<?php
require_once __DIR__ . "/../interfaces/RepositoryInterface.php";
class RepositoryGeneric implements RepositoryInterface {
    public $pdo;
    public $table;

    public function __construct(PDO $pdo, TableInterface $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function save(array $data): bool|string {
        $all_columns = $this->table->getAttributes();
        $result = array_intersect_key($data, array_flip($all_columns));
        $columns = array_keys($result);
        $string = implode(',', $columns);
        $columns_values = $columns;

        foreach ($columns_values as &$value) {
            $value = ':' . $value;
        }

        $columns_value_string = implode(',', $columns_values);
        
        $sql = "INSERT INTO {$this->table->getTableName()}($string) VALUES($columns_value_string)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach($columns as $column) {
            $stmt->bindValue(':' . $column, $data[$column]);
        }

        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
    }

    public function update(int $id, array $data): bool{
        $all_columns = $this->table->getAttributes();
        $result = array_intersect_key($data, array_flip($all_columns));
        $columns = array_keys($result);
        $columns_values= [];
        
        foreach ($columns as $column) {
            $columns_values[] = "$column = :" . $column;
        }

        $columns_values_string = implode(',', $columns_values);

        $sql = "UPDATE {$this->table->getTableName()} SET $columns_values_string WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        foreach($columns as $column) {
            $stmt->bindValue(":" . $column, $data[$column]);
        }
        $stmt->bindValue(':id', $id);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getById(int $id): array {
        $sql = "SELECT * FROM {$this->table->getTableName()} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function get(): array {
        $sql = "SELECT * FROM {$this->table->getTableName()}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table->getTableName()} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":id", $id);
        return $stmt->execute();
    }
}
<?php
require_once __DIR__ . "/../interfaces/IRecordCounter.php";
class RecordCounter implements IRecordCounter {
    private $dbh;
    private $table;

    public function __construct($dbh, TableInterface $table) {
        $this->dbh = $dbh;
        $this->table = $table;
    }

    public function countRecords($conditions = []):int {
        $sql = "SELECT COUNT(*) as total FROM {$this->table->getTableName()}";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", array_map(function($key) {
                return "$key = :$key";
            }, array_keys($conditions)));
        }
        
        $stmt = $this->dbh->prepare($sql);
        
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
}
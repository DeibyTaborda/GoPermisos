<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";

class LeaveTypeRepository extends RepositoryGeneric {
    public $leaveType;
    public function __construct(PDO $pdo, TableInterface $leaveType) {
        $this->leaveType = $leaveType;
        parent::__construct($pdo, $this->leaveType);
    }
    public function getleaveTypes(array $filter = []) {
        $sql = "SELECT 
                l.*,
                s.id AS id_status, s.status
                FROM {$this->leaveType->getTableName()} l
                INNER JOIN tblstatus s ON l.Status = s.id";
    
        $whereClauses = [];
        $params = [];
    
        foreach ($filter as $column => $value) {
            $whereClauses[] = "u.$column = :$column";
            $params[$column] = $value;
        }
    
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    

    public function getByIdleaveType(int $id) {
        $sql = "SELECT 
                l.*,
                s.id AS id_status, s.status
                FROM {$this->leaveType->getTableName()} l
                INNER JOIN tblstatus s ON l.Status = s.id
                WHERE u.id = :id ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
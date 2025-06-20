<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";

class DepartmentRepository extends RepositoryGeneric {
    public $department;
    public function __construct(PDO $pdo, TableInterface $department) {
        parent::__construct($pdo, $department);
        $this->department = $department;
    }

    public function getDepartments() {
        $sql = "SELECT
                    d.*,
                    s.id AS id_status, s.status
                FROM {$this->department->getTableName()} d
                INNER JOIN tblstatus s ON d.StatusId = s.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getByIdDepartment($id) {
        $sql = "SELECT
                    d.*,
                    s.id AS id_status, s.status
                FROM {$this->department->getTableName()} d
                INNER JOIN tblstatus s ON d.StatusId = s.id
                WHERE id = :id;
                ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function countDepartments() {
        $sql = "SELECT COUNT(*) as total FROM tbldepartments";
        $query = $this->pdo->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ)->total;
    }
    
    public function getDepartmentsPaginated($offset, $limit) {
        $sql = "SELECT * FROM tbldepartments ORDER BY id LIMIT :offset, :limit";
        $query = $this->pdo->prepare($sql);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}

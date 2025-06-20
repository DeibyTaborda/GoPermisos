<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";
class SedeRepository extends RepositoryGeneric {
    private $sede;

    public function __construct(PDO $pdo, TableInterface $sede) {
        parent::__construct($pdo, $sede);
        $this->sede = $sede;
    }

    public function getSedesWithDepartments() {
        $sql = "SELECT s.*, ds.department_id, st.status, 
        GROUP_CONCAT(d.DepartmentName ORDER BY d.DepartmentName SEPARATOR ', ') AS departamentos FROM {$this->sede->getTableName()} s
        JOIN departments_sede ds ON s.id = sede_id
        JOIN tbldepartments d ON ds.department_id = d.id
        JOIN tblstatus st ON s.status_id = st.id
        GROUP BY s.id, s.sede;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDepartmentsBySede($sede_id) {
        $sql = "SELECT s.*, d.DepartmentName , ds.department_id FROM sede s
                LEFT JOIN departments_sede ds ON s.id = ds.sede_id 
                LEFT JOIN tbldepartments d ON ds.department_id  = d.id
                WHERE d.StatusId = 1 AND s.id = :sede_id ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':sede_id', $sede_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>
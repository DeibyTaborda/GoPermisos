<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";

class DepartmentsSedeRepo extends RepositoryGeneric {
    public $table;

    public function __construct(PDO $pdo, TableInterface $table) {
        $this->table = $table;
        parent::__construct($pdo, $table);
    }

    public function getDepartmentsBySedeId($sedeId) {
        $sql = "SELECT * FROM {$this->table->getTableName()} WHERE sede_id = :sede_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':sede_id', $sedeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartmentsIdBySede($id_sede) {
        $sql = "SELECT department_id FROM {$this->table->getTableName()} WHERE sede_id = :sede_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':sede_id', $id_sede, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $linkedDepartmentIds = [];

        foreach($result as $linkedDepartment) {
            $linkedDepartmentIds[] = $linkedDepartment['department_id'];
        }

        return $linkedDepartmentIds;
    }

    public function unlinkDepartmentsFromSede($id_sede, array $department_Ids) {
        $sql = "DELETE FROM {$this->table->getTableName()} WHERE sede_id = :id_sede AND department_id = :id_department";
        $stmt = $this->pdo->prepare($sql);
        foreach($department_Ids as $department_id) {
            $stmt->bindValue(':id_sede', $id_sede);
            $stmt->bindValue(':id_department', $department_id);
            $stmt->execute();
        }

        return true;
    }

    public function linkDepartmentToSede($id_sede, $department_ids) {
        $sql = "INSERT INTO {$this->table->getTableName()} (sede_id, department_id) VALUES(:sede_id, :department_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach($department_ids as $department_id) {
            $stmt->bindValue(':sede_id', $id_sede);
            $stmt->bindValue(':department_id', $department_id);
            $stmt->execute();
        }

        return true;
    }
}
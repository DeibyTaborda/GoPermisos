<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";
class UserDepartmentRepository extends RepositoryGeneric {
    public $userDepartment;
    public function __construct(PDO $pdo, UserDepartment $userDepartment) {
        $this->userDepartment = $userDepartment;
        parent::__construct($pdo,$userDepartment);
    }
    
    public function getUserDepartment($filter = []) {
        $sql = "SELECT 
                ud.*,
                d.DepartmentName,
                u.FirstName,
                u.LastName
                FROM {$this->userDepartment->getTableName()} ud
                LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
                LEFT JOIN tblusers u ON ud.UserID = u.id";

        $whereClauses = [];
        $params = [];

        foreach ($filter as $column => $value) {
            $whereClauses[] = "ud.$column = :$column";
            $params[$column] = $value;
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getByIdUserDepartments($id) {
        $sql = "SELECT d.id as department_id,
                        d.DepartmentName
                        FROM user_departments ud 
                        LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
                        WHERE ud.UserID = :id;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateDepartmentUser($id, $data) {
        $sql = "UPDATE {$this->userDepartment->getTableName()} SET DepartmentID = :DepartmentID WHERE UserID = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':DepartmentID', $data['DepartmentID']);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function deleteRelactions(int $idUser, array $departmentsIds) {
        $placeholders = implode(',', array_fill(0, count($departmentsIds), '?'));

        $sql = "
            DELETE FROM {$this->userDepartment->getTableName()}
            WHERE UserID = ? AND DepartmentID IN ($placeholders)
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_merge([$idUser], $departmentsIds));
    }

    public function addRelations(int $idUser, array $departmentsIds) {
        $sql = "
                INSERT INTO {$this->userDepartment->getTableName()} (UserID, DepartmentID)
                VALUES (?, ?)
            ";

        foreach ($departmentsIds as $depId) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idUser, $depId]);
        }

        return true;
    }

    public function getIdsDepartmens($idUser) {
        $sql = "
            SELECT DepartmentID FROM {$this->userDepartment->getTableName()}
            WHERE UserID = :UserID;
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":UserID", $idUser);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $idsDepartments = [];

        foreach($results as $key => $value) {
            $idsDepartments[] = $value['DepartmentID'];
        }

        return $idsDepartments;
    }

    public function updateDepartmentsUser($userId, $newDepartments) {
        try {

            $currentDepartments = $this->getIdsDepartmens($userId);

            $toAdd = array_diff($newDepartments, $currentDepartments);
            $toDelete = array_diff($currentDepartments, $newDepartments);

            if (!empty($toDelete)) {
                $isDeletedDepartments = $this->deleteRelactions($userId, $toDelete);
            }

            if (isset($isDeletedDepartments) && !$isDeletedDepartments) {
                return ['success' => false, "message" => 'Error en eliminar los departamentos'];
            }
            
            if (!empty($toAdd)) {
                $isAddDepartments = $this->addRelations($userId, $toAdd);
            }

            if (isset($isAddDepartments) && !$isAddDepartments) {
                return ['success' => false, "message" => 'Error en agregar los nuevos departamentos'];
            }
          
            return ['success' => true, 'message' => 'Los departamentos se actualizaron correctamente'];
    
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return $e;
        }
    }

    public function getDepartmentsUser($user_id) {
        $sql = "SELECT d.DepartmentName FROM {$this->userDepartment->getTableName()} ud
                    LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
                    WHERE ud.UserID = :user_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":user_id", $user_id);
        $stmt->execute();
        $department_user = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $department_user;
    }
    
}
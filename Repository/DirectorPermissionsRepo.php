<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";

class DirectorPermissionsRepo {
    private $directorPermissions;
    private $pdo;

    function __construct(PDO $pdo, TableInterface $table) {
        $this->directorPermissions = $table;
        $this->pdo = $pdo;
    }

    public function getDepartmentsAndRoles($id_director) {
        $sql = "SELECT DepartmentID, ManagedRoleID FROM {$this->directorPermissions->getTableName()} where DirectorID = :id_director";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_director', $id_director);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function changeRoleToManage($id_director, $data) {
        $sql = "UPDATE {$this->directorPermissions->getTableName()} 
        SET ManagedRoleID = :managed_role_id WHERE DepartmentID = :id_department AND DirectorID = :id_director";

        $stmt = $this->pdo->prepare($sql);

        foreach($data as $permission) {
            $stmt->bindValue(':managed_role_id', $permission['ManagedRoleID']);
            $stmt->bindValue(':id_department', $permission['DepartmentID']);
            $stmt->bindValue(':id_director', $id_director);
            $stmt->execute();
        }

        return true;
    }

    public function deletePermissionsDirector($id_director, $departments) {
        $to_delete = implode(',', $departments);

        $sql = "DELETE FROM {$this->directorPermissions->getTableName()} WHERE DirectorID = :id_director AND DepartmentID IN($to_delete)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_director', $id_director);
        return $stmt->execute();
    }

    public function addPermissionsDirector($id_director, $departments) {
        $sql = "INSERT INTO {$this->directorPermissions->getTableName()} (DirectorID, DepartmentID, ManagedRoleID)
        VALUES(:id_director, :id_department, :id_managed_role)";

        foreach($departments as $key => $value) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id_director', $id_director);
            $stmt->bindValue(':id_department', $key);
            $stmt->bindValue(':id_managed_role', $value);

            $stmt->execute();
        }

        return true;
    }

    public function updatePermissionsDirector($id_director, $data) {
        try {
            $departmentsAndRoles = $this->getDepartmentsAndRoles($id_director);

            $currentDepartments = [];

            foreach ($departmentsAndRoles as $permission) {
                $currentDepartments[] = $permission['DepartmentID'];
            }

            $newDepartments = array_keys($data);

            $toAdd = array_diff($newDepartments, $currentDepartments);
            $toDelete = array_diff($currentDepartments, $newDepartments);

            $departmentsWithRoleChange = [];

            foreach ($departmentsAndRoles as $permission) {
                $departmentId = $permission['DepartmentID'];
                $managedRoleId = $permission['ManagedRoleID'];

                if (isset($data[$departmentId]) && $data[$departmentId] != $managedRoleId) {
                    $departmentsWithRoleChange[] = ['DepartmentID' => $departmentId, 'ManagedRoleID' => $data[$departmentId]];
                }
            }

            if (!empty($toDelete)) {
                $isDeletedPermissions = $this->deletePermissionsDirector($id_director, $toDelete);
            }

            if (isset($isDeletedPermissions) && !$isDeletedPermissions) {
                return ['success' => false, "message" => 'Error en eliminar los permisos'];
            }

            if (!empty($toAdd)) {
                foreach ($toAdd as $clave) {
                    if (array_key_exists($clave, $data)) {
                        $resultado[$clave] = $data[$clave];
                    }
                }

                $isAddPermissions = $this->addPermissionsDirector($id_director, $resultado);
            }

            if (isset($isAddPermissions) && !$isAddPermissions) {
                return ['success' => false, "message" => 'Error en agregar los nuevos departamentos'];
            }

            if (!empty($departmentsWithRoleChange)) {
                $isChangeRole = $this->changeRoleToManage($id_director, $departmentsWithRoleChange);
            }

            if (isset($isChangeRole) && !$isChangeRole) {
                return ['success' =>  'error', 'message' => 'Error en asignar nuevo rol'];
            }

            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return $e;
        }
    }

    public function getPermissionsDir($id_director) {
        $sql = "SELECT dp.*, d.DepartmentName, r.rol FROM {$this->directorPermissions->getTableName()} dp 
                LEFT JOIN tblusers u ON dp.DirectorID = u.id
                LEFT JOIN tblrol r ON dp.ManagedRoleID = r.id
                LEFT JOIN tbldepartments d ON dp.DepartmentID = d.id
                where DirectorID = :id_director;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_director', $id_director);
        $stmt->execute();
        $permissionsDir = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $permissionsDir;
    }
}
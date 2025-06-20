<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";

class UserPermissionsRepository extends RepositoryGeneric {
    public $pdo;
    public $table;
    public function __construct(PDO $pdo, $table) {
        $this->pdo = $pdo;
        $this->table = $table;
        parent::__construct($pdo, $table);
    }

    public function getPermissionsByUserId(int $userId): array {
        $sql = "SELECT * FROM user_permissions WHERE UserID = :userId;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissionsByUserId2(int $userId): array {
        $sql = "SELECT up.id, d.DepartmentName, r.rol, s.sede FROM user_permissions up 
        JOIN tbldepartments d ON up.DepartmentID = d.id
        JOIN tblrol r ON up.RoleID = r.id
        JOIN sede s ON up.SedeID = s.id
        JOIN tblusers u ON up.UserID = u.id
        WHERE u.id = :userId;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updatePermissions(int $userId, array $permissions) {
        $currentPermissions = $this->getPermissionsByUserId($userId);

        $permissions_delete = array_diff(
    array_column($currentPermissions, 'id'),
    array_column($permissions, 'id')
        );

        if (!empty($permissions_delete)) {
            foreach ($permissions_delete as $id) {
                $this->delete($id);
            }
        }

        $permissions_insert = array_filter($permissions, function($item) {
            return !array_key_exists('id', $item);
        });

        if (!empty($permissions_insert)) {
            foreach ($permissions_insert as $permission) {
                $this->save([
                    'UserID' => $userId,
                    'DepartmentID' => $permission['DepartmentID'],
                    'RoleID' => $permission['RoleID'],
                    'SedeID' => $permission['SedeID']
                ]);
            }
        }

        $permissions_update = [];

        foreach ($currentPermissions as $currentPermission) {
            foreach ($permissions as $permission) {
                // Verificamos que ambos tengan el mismo ID
                if (isset($permission['id']) && $currentPermission['id'] === $permission['id']) {
                    if (
                        $currentPermission['DepartmentID'] !== $permission['DepartmentID'] ||
                        $currentPermission['RoleID'] !== $permission['RoleID'] ||
                        $currentPermission['SedeID'] !== $permission['SedeID']
                    ) {
                        $permissions_update[] = $permission;
                    }
                }
            }
        }

        if (!empty($permissions_update)) {
            foreach ($permissions_update as $permission) {
                $this->update($permission['id'], [
                    'UserID' => $userId,
                    'DepartmentID' => $permission['DepartmentID'],
                    'RoleID' => $permission['RoleID'],
                    'SedeID' => $permission['SedeID']
                ]);
            }
        }

        return true;
    }
}

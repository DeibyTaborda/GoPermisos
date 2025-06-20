<?php
require_once __DIR__ . "/../interfaces/IUserDepartmentManager.php";
class UserDepartmentManager implements IUserDepartmentManager {
    private $pdo;
    public $repositoryUser;
    public $repositoryUserDepartment;
    public $userPermissions;

    public function __construct(PDO $pdo, $repositoryUser, $repositoryUserDepartment, $userPermissions) {
        $this->pdo = $pdo;
        $this->repositoryUser = $repositoryUser;
        $this->repositoryUserDepartment = $repositoryUserDepartment;
        $this->userPermissions = $userPermissions;
    }

    public function createUser(array $userData, array  $departmentsID, $user_permissions = null): array {
        $rol = $userData['RolID'] ?? null;

        if (empty($rol)) {
            return ['success' => false, 'message' => 'Debes asignar un rol para el usuario'];
        }

        if (empty($departmentsID)) {
            return ['success' => false, 'message' => 'No se ingresaron departamentos'];
        }

        $existDoc = $this->repositoryUser->getUsers(['EmpId' => $userData['EmpId']]);
        if (!empty($existDoc)) {
            return ['success' => false, 'message' => 'El documento ya existe'];
        }

        $existEmail = $this->repositoryUser->getUsers(['EmailId' => $userData['EmailId']]);
        if (!empty($existEmail)) {
            return ['success' => false, 'message' => 'El correo ya existe'];
        }

        $password = $userData['Password'] ?? null;
        $confirmPassword = $userData['confirmpassword'] ?? null;

        if ($password && $confirmPassword) {
            if ($password === $confirmPassword) {
                $userData['Password'] = password_hash($password, PASSWORD_DEFAULT);
            } else {
                return [
                    'success' => false,
                    'message' => 'Las contraseñas no coinciden'
                ];
            }
        } else {
            return ['success' => false, 'message' => 'Ingresa tanto la contraseña como la contraseña de confirmación'];
        }

        $this->repositoryUser->pdo->beginTransaction();

        $idUser = $this->repositoryUser->save($userData);

        if (!$idUser) {
            $this->repositoryUser->pdo->rollBack();
            return ['sucess' => false, 'message' => 'Error al crar el usuario'];
        }

        foreach ($departmentsID as $departmentID) {
            $id_assignment = $this->repositoryUserDepartment->save(['UserID' => $idUser, 'DepartmentID' => $departmentID]);
        }

        if (!$id_assignment) {
            return ['sucess' => false, 'message' => 'Error en asignar los departmantos'];
        }

        if (!empty($rol) && $user_permissions && ($rol == 2 || $rol == 3)) {
            foreach($user_permissions as $permission) {
                $existPermissions = $this->userPermissions->save([
                    'UserID' => $idUser,
                    'DepartmentID' => $permission['DepartmentID'],
                    'RoleID' => $permission['RoleID'],
                    'SedeID' => $permission['SedeID']
                ]);
            }
        }

        if (isset($existPermissions) && !$existPermissions) {
            $this->repositoryUser->pdo->rollBack();
            return ['sucess' => 'error', 'message' => 'Error en asignar permisos'];
        }

        $this->repositoryUser->pdo->commit();
        return ['success' => true, 'message' => 'Usuario creado correctamente', 'id_registro' => $idUser];
    }

    public function updateUser(int $idUser, array $userData, array $departmentsID, $user_permissions = null): array {
        
        if (!isset($userData['RolID']) || empty($userData['RolID'])) {
            return ['success' => false, 'message' => 'Debes asignar un rol para el usuario'];
        }

        if (empty($departmentsID)) {
            return ['success' => false, 'message' => 'No se ingresaron departamentos'];
        }

        $existDoc = $this->repositoryUser->getUsers(['EmpId' => $userData['EmpId']]);
        if (!empty($existDoc) && $existDoc[0]->id !== $idUser) {
            return ['success' => false, 'message' => 'El correo ya existe para otro usuario'];
        }

        $existEmail = $this->repositoryUser->getUsers(['EmailId' => $userData['EmailId']]);
        if (!empty($existEmail) && $existEmail[0]->id !== $idUser) {
            return ['success' => false, 'message' => 'El correo ya existe para otro usuario'];
        }

        $password = $userData['Password'] ?? null;
        $confirmPassword = $userData['confirmpassword'] ?? null;

        $this->repositoryUser->pdo->beginTransaction();

        $user = $this->repositoryUser->getById($idUser);
        $data_filter = [];

        if ($password && $confirmPassword) {
            if ($password != $user[0]->Password) {
                if ($password === $confirmPassword) {
                    $userData['Password'] = password_hash($password, PASSWORD_DEFAULT);
                } else {
                    return [
                        'success' => false,
                        'message' => 'Las contraseñas no coinciden'
                    ];
                }
            }
        } else {
            return ['success' => false, 'message' => 'Ingresa tanto la contraseña como la contraseña de confirmación'];
        }

        foreach ($userData as $key => $value) {
            if ($key !== 'confirmpassword' && $value != $user[0]->$key) {
                $data_filter[$key] = $value;
            }
        }

        if (empty($data_filter) && empty($departmentsID)) {
            $this->repositoryUser->pdo->rollBack();
            return ['success' => false, 'message' => 'No se realizaron cambios', 'type' => 'info'];
        }

        if (!empty($data_filter)) {
            $isUserUpdated = $this->repositoryUser->update($idUser, $data_filter);
        }

        if (isset($isUserUpdated) && !$isUserUpdated) {
            $this->repositoryUser->pdo->rollBack();
            return ['success' => false, 'message' => 'Error al actualizar el usuario'];
        }

        if (!empty($departmentsID)) {
            $isDepartmentsUpdated = $this->repositoryUserDepartment->updateDepartmentsUser($idUser, $departmentsID);
        }

        if (!empty($user_permissions)) {
            $updated_permissions_user = $this->userPermissions->updatePermissions($idUser, $user_permissions);
        }

        if (isset($data_filterupdated_permissions_user) && !$updated_permissions_user) {
            $this->repositoryUser->pdo->rollBack();
            return ['success' => false, 'message' => 'Error al actualizar los permisos del usuario'];
        }

        $this->repositoryUser->pdo->commit();

        return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
    }
}
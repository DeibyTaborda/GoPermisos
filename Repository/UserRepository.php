<?php
require_once __DIR__ . "/../generic/RepositoryGeneric.php";

class UserRepository extends RepositoryGeneric {
    public $user;
    public function __construct(PDO $pdo, TableInterface $user) {
        $this->user = $user;
        parent::__construct($pdo, $this->user);
    }
    public function getUsers(array $filter = []) {
        $sql = "SELECT u.*, r.rol, s.status, d.DepartmentName, sd.sede
                        FROM {$this->table->getTableName()} u 
                        LEFT JOIN tblrol r ON u.RolID = r.id 
                        LEFT JOIN tblstatus s ON u.Status = s.id
                        LEFT JOIN user_departments ud ON u.id = ud.UserID
                        LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
                        LEFT JOIN sede sd ON u.SedeID = sd.id";
    
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
    
    public function getByIdUser(int $id) {
        $sql = "SELECT u.*, r.rol, s.status, sd.sede
                        FROM tblusers u 
                        LEFT JOIN tblrol r ON u.RolID = r.id 
                        LEFT JOIN tblstatus s ON u.Status = s.id 
                        LEFT JOIN sede sd ON u.SedeID = sd.id
                        WHERE u.id = :id;";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getUserByEmail(string $email): array {
        $sql = "SELECT u.*, r.rol, s.status 
                        FROM {$this->table->getTableName()} u 
                        LEFT JOIN tblrol r ON u.RolID = r.id 
                        LEFT JOIN tblstatus s ON u.Status = s.id 
                        WHERE u.EmailId = :email;";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getPermissionManagersEmails($department_id, $sede_id, $rol_id) {
        $sql = "SELECT DISTINCT u.EmailID, u.FirstName from tblusers u
            INNER JOIN tblrol r ON u.RolID = r.id
            INNER JOIN sede s ON u.SedeID = s.id
            INNER JOIN user_departments ud ON u.id = ud.UserID
            INNER JOIN tbldepartments d ON ud.DepartmentID = d.id
            INNER JOIN user_permissions up ON (
                up.UserID = u.id AND
                up.DepartmentID = :department_id AND
                up.RoleID = :rol_id AND
                up.SedeID = :sede_id
        )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':department_id', $department_id);
        $stmt->bindValue(':sede_id', $sede_id);
        $stmt->bindValue(':rol_id', $rol_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
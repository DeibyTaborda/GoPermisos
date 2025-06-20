<?php
require_once __DIR__ . "/../interfaces/AuthenticateI.php";
class Auth implements AuthenticateI {
    private $dbh;

    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }

    public function authenticate(string $email, string $password): bool|array {
        $sql = "SELECT u.*,
                r.id as rol_id
                FROM tblusers u
                LEFT JOIN tblrol r ON u.RolID = r.id
                WHERE u.EmailId = :email";
        
        $query = $this->dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_OBJ);
    
        if (!empty($results) && password_verify($password, $results->Password)) {
            if ($results->Status == 2) {
                return ['error' => 'Cuenta inactiva, por favor contacte al administrador'];
            }
    
            return true;
        } else {
            return ['error' => 'Credenciales inválidas'];
        }
    }
}
?>

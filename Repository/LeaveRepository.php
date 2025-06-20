<?php
require_once __DIR__ ."/../generic/RepositoryGeneric.php";

class LeaveRepository extends RepositoryGeneric{
    public $pdo;
    public $leave;

    public function __construct(PDO $pdo, TableInterface $leave) {
        $this->leave = $leave;
        parent::__construct($pdo, $this->leave);
    }

    public function getLeavesAsLeader($leaderId, $conditions = []) {
        $sql = "
            SELECT 
                l.*,
                u.FirstName,
                u.LastName,
                u.EmailId,
                u.EmpId,
                u.RolID,
                d.DepartmentName,
                lt.LeaveType
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
            JOIN user_departments ud1 ON u.id = ud1.UserID
            JOIN user_departments ud2 ON ud2.UserID = :leaderId
            JOIN tbldepartments d ON ud1.DepartmentID = d.id
            WHERE ud1.DepartmentID = ud2.DepartmentID
                AND u.RolID = 1
              AND u.id != :leaderId
        ";

        $clauses = [];

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $clauses[] = "l.$key = :$key";
            }
        }

        if (!empty($clauses)) {
            $sql .= ' AND ' . implode(' AND ', $clauses);
        }
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':leaderId', $leaderId);

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    

    public function getLeavesAsDirector($directorId, $conditions = []) {
        $sql = "
            SELECT 
                l.*,
                u.FirstName,
                u.LastName,
                u.EmailId,
                u.EmpId,
                u.RolID,
                d.DepartmentName,
                lt.LeaveType
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN tblrol r ON u.RolID = r.id
            JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
            JOIN user_departments ud1 ON u.id = ud1.UserID
            JOIN user_departments ud2 ON ud1.DepartmentID = ud2.DepartmentID
            JOIN tbldepartments d ON ud1.DepartmentID = d.id
            WHERE ud2.UserID = :directorId
              AND r.id = 2 -- ID del rol 'Líder'
        ";
    
        $clauses = [];

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $clauses[] = "l.$key = :$key";
            }
        }

        if (!empty($clauses)) {
            $sql .= ' AND ' . implode(' AND ', $clauses);
        }
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':directorId', $directorId);

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getLeaves($filter = []) {
            $sql = "
                SELECT 
                    l.*,
                    u.FirstName,
                    u.LastName,
                    u.EmailId,
                    u.EmpId,
                    u.RolID,
                    d.DepartmentName,
                    lt.LeaveType
                FROM tblleaves l
                LEFT JOIN tblusers u ON l.empid = u.id
                LEFT JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
                LEFT JOIN user_departments ud ON u.id = ud.UserID
                LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
            ";

            $clauses = [];

            if (!empty($filter)) {
                foreach ($filter as $key => $value) {
                    $clauses[] = "l.$key = :$key";
                }
            }

            if (!empty($clauses)) {
                $sql .= ' WHERE ' . implode(' AND ', $clauses);
            }

            $sql .= " GROUP BY l.id ORDER BY l.PostingDate DESC";
    
            $stmt = $this->pdo->prepare($sql);

            if (!empty($filter)) {
                foreach ($filter as $key => $value) {
                    $stmt->bindValue(":$key", $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    

    public function getLeaveById($leaveId) {
        $query = "
            SELECT
                l.*,
                u.FirstName,
                u.LastName,
                u.EmailId,
                u.EmpId,
                u.RolID,
                u.Phonenumber,
                u.Gender,
                d.DepartmentName,
                d.id as department_id,
                lt.LeaveType
            FROM tblleaves l
            LEFT JOIN tblusers u ON l.empid = u.id
            LEFT JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
            LEFT JOIN user_departments ud ON u.id = ud.UserID
            LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
            WHERE l.id = :leaveId
        ";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['leaveId' => $leaveId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
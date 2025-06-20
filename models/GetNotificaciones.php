<?php
require_once __DIR__ . "/../interfaces/IGetNotifications.php";
class GetNotifications implements IGetNotifications {
   private $pdo;

   public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
   }

   public function getNotificationsColaborador(int $user_id): ?array {
    $sql = " SELECT 
                l.*,
                u.FirstName,
                u.LastName,
                u.EmailId,
                u.EmpId,
                u.RolID,
                d.DepartmentName,
                lt.LeaveType,
                s.status as estado
            FROM tblleaves l
            LEFT JOIN tblusers u ON l.empid = u.id
            JOIN tblstatus s ON l.Status = s.id
            LEFT JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
            LEFT JOIN user_departments ud ON u.id = ud.UserID
            LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
            WHERE l.IsRead = 1 AND l.Status != 3 AND l.empid = $user_id
            GROUP BY l.id";
   
   $stmt = $this->pdo->prepare($sql);
   $stmt->execute();
   $notifications = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $notifications;
   }

   public function getNotificationsDirector(int $user_id): ?array {

        $sql = " SELECT 
                    l.*,
                    u.FirstName,
                    u.LastName,
                    u.EmailId,
                    u.EmpId,
                    u.RolID,
                    d.DepartmentName,
                    lt.LeaveType,
                    s.status as estado
                FROM tblleaves l
                JOIN tblusers u ON l.empid = u.id
                JOIN tblstatus s ON l.Status = s.id
                JOIN tblrol r ON u.RolID = r.id
                JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
                JOIN user_departments ud1 ON u.id = ud1.UserID
                JOIN user_departments ud2 ON ud1.DepartmentID = ud2.DepartmentID
                JOIN tbldepartments d ON ud1.DepartmentID = d.id
                WHERE ud2.UserID = :directorId
                AND r.id = 2 AND l.Status = :id_status AND l.IsRead = :is_read
            ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':directorId', $user_id);
        $stmt->bindValue(':id_status', 3);
        $stmt->bindValue(':is_read', 2);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $notifications;
   }

   public function getNotificationsLeader(int $user_id): ?array {
        
        $sql = " SELECT 
                    l.*,
                    u.FirstName,
                    u.LastName,
                    u.EmailId,
                    u.EmpId,
                    u.RolID,
                    d.DepartmentName,
                    lt.LeaveType,
                    s.status as estado
                FROM tblleaves l
                JOIN tblusers u ON l.empid = u.id
                JOIN tblstatus s ON l.Status = s.id
                JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
                JOIN user_departments ud1 ON u.id = ud1.UserID
                JOIN user_departments ud2 ON ud2.UserID = :leaderId
                JOIN tbldepartments d ON ud1.DepartmentID = d.id
                WHERE ud1.DepartmentID = ud2.DepartmentID
                    AND u.RolID = 1
                AND u.id != :leaderId AND l.Status = :id_status AND l.IsRead = :is_read
            ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':leaderId', $user_id);
        $stmt->bindValue(':id_status', 3);
        $stmt->bindValue(':is_read', 2);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_OBJ);


    return $notifications;
   }
}
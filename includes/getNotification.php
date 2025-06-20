<?php
include('./config.php');

function getNotificationById($dbh, $notification_id) {
    $sql = "
        SELECT 
            tblleaves.id AS lid,
            tblusers.FirstName,
            tblusers.LastName,
            tblusers.EmpId,
            DATE_FORMAT(tblleaves.AdminRemarkDate, '%d/%m/%Y %h:%i %p') AS AdminRemarkDate,
            DATE_FORMAT(tblleaves.ToDate, '%d/%m/%Y %h:%i %p') AS ToDate,
            DATE_FORMAT(tblleaves.FromDate, '%d/%m/%Y %h:%i %p') AS FromDate,
            tblleaves.Description,
            tblleaves.AdminRemark,
            tblleaves.Status
        FROM tblleaves
        JOIN tblusers 
            ON tblleaves.empid = tblusers.id
        WHERE tblleaves.id = :notification_id
    ";

    $query = $dbh->prepare($sql);
    $query->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
    $query->execute();
    $notification = $query->fetch(PDO::FETCH_OBJ);

    $isRead = 2;

    $sql = 'UPDATE tblleaves SET IsRead = :isRead WHERE id = :notification_id';
    $query = $dbh->prepare($sql);
    $query->bindParam(':isRead', $isRead, PDO::PARAM_INT);
    $query->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
    $query->execute();

    return $notification;
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if (isset($_POST['notification_id'])) {
        $notificationId = $_POST['notification_id'];
        $selectedNotification = getNotificationById($dbh, $notificationId);

        if ($selectedNotification) {
            echo json_encode($selectedNotification);
        } else {
            echo json_encode(["error" => "No se encontró la notificación"]);
        }
    } else {
        echo json_encode(["error" => "No se recibió notification_id"]);
    }
}
?>

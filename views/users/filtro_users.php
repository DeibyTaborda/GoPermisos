<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../utils/utils.php";

$orderColumnIndex = $_POST['order'][0]['column'];
$orderDirection = $_POST['order'][0]['dir'];

$columns = ['id', 'EmpId', 'FirstName', 'LastName', 'departments', 'Status', 'rol'];

$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'id';

$limit = $_POST['length'] ?? 10;
$offset = $_POST['start'];
$search = $_POST['search']['value'] ?? '';

$sql = "SELECT u.*, r.rol, d.DepartmentName
        FROM tblusers u 
    LEFT JOIN tblrol r ON u.RolID = r.id
    LEFT JOIN user_departments ud ON u.id = ud.UserID 
    LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id";

if (!empty($search)) {
    $sql .= ' WHERE u.EmpId LIKE :search OR u.FirstName LIKE :search OR u.LastName LIKE :search OR r.rol LIKE :search OR d.DepartmentName LIKE :search';
}

$sql .= " ORDER BY $orderColumn $orderDirection";

$sql .= " LIMIT $limit OFFSET $offset";

$stmt = $dbh->prepare($sql);

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_OBJ);

if (!empty($search)) {
    $sql2 = "SELECT COUNT(DISTINCT u.id) as total
            FROM tblusers u 
        LEFT JOIN tblrol r ON u.RolID = r.id
        LEFT JOIN user_departments ud ON u.id = ud.UserID 
        LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
        WHERE u.EmpId LIKE :search OR u.FirstName LIKE :search OR u.LastName LIKE :search OR r.rol LIKE :search OR d.DepartmentName LIKE :search";
} else {
    $sql2 = "SELECT COUNT(DISTINCT u.id) as total FROM tblusers u";
}

$stmt2 = $dbh->prepare($sql2);

if (!empty($search)) {
    $stmt2->bindValue(':search', "%$search%");
}

$stmt2->execute();
$total = $stmt2->fetchColumn();

$usersWithDepartment = [];

if (!empty($users)) {
    foreach ($users as $user) {
        $id_user = $user->id;

        if (!isset($usersWithDepartment[$id_user])) {
            $usersWithDepartment[$id_user] = [
                'id' => $user->id,
                'EmpId' => $user->EmpId,
                'FirstName' => $user->FirstName,
                'LastName' => $user->LastName,
                'departments' => [],
                'Status' => $user->Status == 1 ? '<span class="status-badge status-active">Activo</span>' : '<span class="status-badge status-inactive">Inactivo</span>',
                'rol' => $user->rol,
                'actions' => getActionButtonsUser($user)
            ];
        }

        if (!empty($user->DepartmentName)) {
            $usersWithDepartment[$id_user]['departments'][] = $user->DepartmentName;
        }
    }
}

$finalUsers = array_map(function($user) {
    $user['departments'] = implode(', ', $user['departments']);
    return $user;
}, array_values($usersWithDepartment));

$response = [
    'draw' => $_POST['draw'],
    'recordsTotal' => $total,
    'recordsFiltered' => $total,
    'data' => $finalUsers
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

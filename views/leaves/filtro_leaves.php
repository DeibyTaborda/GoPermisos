<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../models/Leave.php";
require_once __DIR__ . "/../../repository/LeaveRepository.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

session_start();
$leave = new Leave();
$leaveRepository = new LeaveRepository($dbh, $leave);
$ManagerSession = new ManagerSession();

$limit = $_POST['length'] ?? 10;
$offset = $_POST['start'] ?? 0;
$search = $_POST['search']['value'] ?? '';
$estado = $_POST['estado'] ?? '';

$rol = $ManagerSession->getSession('rol_id');
$super_admin = $ManagerSession->getSession('super_admin');
$id_user = $ManagerSession->getSession('user_id');

$orderColumnIndex = $_POST['order'][0]['column'] ?? '';
$orderDirection = $_POST['order'][0]['dir'] ?? '';

$columns = [
    'l.id',
    'u.EmpId',
    'u.FirstName',
    'u.LastName',
    'd.DepartmentName',
    'lt.LeaveType',
    'l.PostingDate',
    's.status'
];

$orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'l.id';

$sql = "SELECT DISTINCT
            l.*,
            u.FirstName,
            u.LastName,
            u.EmailId,
            u.EmpId AS documento,
            u.RolID,
            d.DepartmentName,
            lt.LeaveType,
            s.status,
            sd.sede
        FROM tblleaves l";

// Definir JOINs según el rol
if ($super_admin) {
    $join = " LEFT JOIN tblusers u ON l.empid = u.id
        LEFT JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
        LEFT JOIN user_departments ud ON u.id = ud.UserID
        LEFT JOIN tbldepartments d ON ud.DepartmentID = d.id
        LEFT JOIN sede sd ON u.SedeID = sd.id
        LEFT JOIN tblstatus s ON l.Status = s.id";
    
    $whereConditions = [];
    
} else if ($rol === 2 || $rol === 3) {
    // Rol 2 y 3: Ambos usan la misma lógica basada en user_permissions
    $join = " INNER JOIN tblusers u ON l.empid = u.id
        INNER JOIN user_departments ud ON u.id = ud.UserID
        INNER JOIN user_permissions up ON (
            up.DepartmentID = ud.DepartmentID 
            AND up.SedeID = u.SedeID
            AND up.RoleID = u.RolID
            AND up.UserID = :id_user
        )
        INNER JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
        INNER JOIN tbldepartments d ON ud.DepartmentID = d.id
        INNER JOIN sede sd ON u.SedeID = sd.id
        INNER JOIN tblstatus s ON l.Status = s.id
        INNER JOIN tblrol r ON up.RoleID = r.id";
    
    $whereConditions = [
        "up.UserID = :id_user", // Usuario tiene permisos
        "u.id != :id_user" // Excluir propias solicitudes
    ];
} else {
    // Para otros roles, usar lógica básica o denegar acceso
    $join = " INNER JOIN tblusers u ON l.empid = u.id
        INNER JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
        INNER JOIN user_departments ud ON u.id = ud.UserID
        INNER JOIN tbldepartments d ON ud.DepartmentID = d.id
        INNER JOIN sede sd ON u.SedeID = sd.id
        INNER JOIN tblstatus s ON l.Status = s.id";
    
    $whereConditions = [
        "u.id = :id_user" // Solo sus propias solicitudes
    ];
}

$sql .= $join;

// Construir la cláusula WHERE
if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
    $hasWhere = true;
} else {
    $hasWhere = false;
}

// SQL para contar registros
$sqlcount = "SELECT COUNT(DISTINCT l.id) FROM tblleaves l $join";
if (!empty($whereConditions)) {
    $sqlcount .= " WHERE " . implode(" AND ", $whereConditions);
}

// Filtro adicional por sede para usuarios específicos (excepto excluidos)
$excludedUserIds = [120];
if (!in_array($id_user, $excludedUserIds) && !$super_admin && ($rol === 2 || $rol === 3)) {
    // Para roles 2 y 3, los filtros de sede ya están incluidos en user_permissions
    // No necesitamos agregar filtros adicionales aquí
}

// Filtro de búsqueda
if (!empty($search)) {
    $searchCondition = "(u.FirstName LIKE :search OR u.LastName LIKE :search OR s.status LIKE :search OR u.EmpId LIKE :search)";
    
    if ($hasWhere) {
        $sql .= " AND " . $searchCondition;
        $sqlcount .= " AND " . $searchCondition;
    } else {
        $sql .= " WHERE " . $searchCondition;
        $sqlcount .= " WHERE " . $searchCondition;
        $hasWhere = true;
    }
}

// Filtro por estado
if (!empty($estado)) {
    $statusCondition = "l.Status = :estado";
    
    if ($hasWhere) {
        $sql .= " AND " . $statusCondition;
        $sqlcount .= " AND " . $statusCondition;
    } else {
        $sql .= " WHERE " . $statusCondition;
        $sqlcount .= " WHERE " . $statusCondition;
    }
}

// Agregar GROUP BY, ORDER BY y LIMIT
$sql .= " GROUP BY l.id ORDER BY $orderBy $orderDirection LIMIT $limit OFFSET $offset";

// Preparar y ejecutar consulta principal
$stmt = $dbh->prepare($sql);

// Bind de parámetros
if (!$super_admin) {
    $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
}

if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

if (!empty($estado)) {
    $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
}

$stmt->execute();
$leaves = $stmt->fetchAll(PDO::FETCH_OBJ);

// Agregar acciones a cada registro
foreach($leaves as $leave) {
    $leave->actions = '<a target="_blank" href="leave-details.php?leaveid='.$leave->id.'" class="action-btn btn-view"><i class="fas fa-eye"></i></a>';
}

// Preparar y ejecutar consulta de conteo
$stmt2 = $dbh->prepare($sqlcount);

if (!$super_admin) {
    $stmt2->bindValue(':id_user', $id_user, PDO::PARAM_INT);
}

if (!empty($search)) {
    $stmt2->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

if (!empty($estado)) {
    $stmt2->bindValue(':estado', $estado, PDO::PARAM_INT);
}

$stmt2->execute();
$total = $stmt2->fetchColumn();

$response = [
    'draw' => $_POST['draw'],
    'recordsTotal' => $total,
    'recordsFiltered' => $total,
    'data' => $leaves
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
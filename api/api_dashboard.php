<?php
// Configuración de encabezados para permitir solicitudes AJAX
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once __DIR__ . "/../includes/config.php";


$conn = $dbh;

// Función para obtener parámetros de fecha
function getDateFilters() {
    $year = isset($_GET['year']) ? intval($_GET['year']) : null;
    $month = isset($_GET['month']) ? intval($_GET['month']) : null;
    $day = isset($_GET['day']) ? intval($_GET['day']) : null;
    $sede = isset($_GET['sede']) ? intval($_GET['sede']) : null;
    $department = isset($_GET['department']) ? intval($_GET['department']) : null;
    
    $whereClause = "";
    $params = [];
    
    if ($year !== null) {
        $whereClause .= " AND YEAR(PostingDate) = :year";
        $params[':year'] = $year;
    }
    
    if ($month !== null) {
        $whereClause .= " AND MONTH(PostingDate) = :month";
        $params[':month'] = $month;
    }
    
    if ($day !== null) {
        $whereClause .= " AND DAY(PostingDate) = :day";
        $params[':day'] = $day;
    }

    if ($sede !== null) {
        $whereClause .= " AND u.SedeID = :sede";
        $params[':sede'] = $sede;
    }

    if ($department !== null) {
        $whereClause .= " AND ud.DepartmentID = :department";
        $params[':department'] = $department;
    }
    
    
    return ['whereClause' => $whereClause, 'params' => $params];
}

// Endpoint principal que determina qué datos obtener
$action = isset($_GET['action']) ? $_GET['action'] : '';
$response = [];

switch ($action) {
    case 'solicitudesPorDepartamento':
        $response = getSolicitudesPorDepartamento($conn);
        break;
    case 'solicitudesPorEstado':
        $response = getSolicitudesPorEstado($conn);
        break;
    case 'topEmpleadosPorDepartamento':
        $response = getTopEmpleadosPorDepartamento($conn);
        break;
    case 'topDepartamentosConMasPermisos':
        $response = getTopDepartamentosConMasPermisos($conn);
        break;
    case 'topDepartamentosConMasDias':
        $response = getTopDepartamentosConMasDias($conn);
        break;
    case 'promedioDiasPorDepartamento':
        $response = getPromedioDiasPorDepartamento($conn);
        break;
    case 'tendenciasPorTiempo':
        $response = getTendenciasPorTiempo($conn);
        break;
    case 'tipologiaPermisos':
        $response = getTipologiaPermisos($conn);
        break;
    case 'distribucionRolPorPermisos':
        $response = getDistribucionRolPorPermisos($conn);
        break;
    case 'estadoDashboard':
        $response = getEstadoDashboard($conn);
        break;
    default:
        $response = ["error" => "Acción no válida"];
}

echo json_encode($response);
exit();

// 1. Total de solicitudes por departamento
function getSolicitudesPorDepartamento($conn) {
    $dateFilters = getDateFilters();
    
    $query = "SELECT d.DepartmentName, COUNT(l.id) as total_solicitudes
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN user_departments ud ON u.id = ud.UserID
            JOIN tbldepartments d ON ud.DepartmentID = d.id
            JOIN sede sd ON u.SedeID = sd.id
            WHERE 1=1 " . $dateFilters['whereClause'] . "
            GROUP BY d.DepartmentName
            ORDER BY total_solicitudes DESC";
            error_log(date('Y-m-d H:i:s') . " - " . $query . PHP_EOL, 3, __DIR__ . '/consulta.log');
    
    $stmt = $conn->prepare($query);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 2. Total de solicitudes por estado
function getSolicitudesPorEstado($conn) {
    $dateFilters = getDateFilters();
    
    $query = "SELECT s.status, COUNT(DISTINCT l.id) as total
            FROM tblleaves l
            JOIN tblstatus s ON l.Status = s.id
            JOIN tblusers u ON l.empid = u.id
            JOIN sede sd ON u.SedeID = sd.id
            JOIN user_departments ud ON u.id = ud.UserID
            WHERE 1=1 " . $dateFilters['whereClause'] . "
            GROUP BY s.status";
    
    $stmt = $conn->prepare($query);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 3. Top de empleados por departamento
function getTopEmpleadosPorDepartamento($conn) {
    $dateFilters = getDateFilters();
    $departmentId = isset($_GET['department']) ? intval($_GET['department']) : null;
    
    $whereClause = $dateFilters['whereClause'];
    $params = $dateFilters['params'];
    
    if ($departmentId !== null) {
        $whereClause .= " AND d.id = :departmentId";
        $params[':departmentId'] = $departmentId;
    }
    
    $query = "SELECT CONCAT_WS(' ', u.FirstName, u.LastName) as employee_name, 
                    d.DepartmentName,
                    COUNT(l.id) as total_leaves
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN user_departments ud ON u.id = ud.UserID
            JOIN tbldepartments d ON ud.DepartmentID = d.id
            WHERE 1=1 " . $whereClause . "
            GROUP BY u.id, d.DepartmentName
            ORDER BY d.DepartmentName, total_leaves DESC";
    
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 4. Top 5 departamentos con más permisos
function getTopDepartamentosConMasPermisos($conn) {
    $dateFilters = getDateFilters();
    
    $query = "SELECT d.DepartmentName, COUNT(l.id) as total_permisos
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN user_departments ud ON u.id = ud.UserID
            JOIN tbldepartments d ON ud.DepartmentID = d.id
            WHERE 1=1 " . $dateFilters['whereClause'] . "
            GROUP BY d.DepartmentName
            ORDER BY total_permisos DESC
            LIMIT 5";
    
    $stmt = $conn->prepare($query);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 5. Top 5 departamentos con más días acumulados
function getTopDepartamentosConMasDias($conn) {
    $dateFilters = getDateFilters();
    
    $query = "SELECT d.DepartmentName, 
                    SUM(TIMESTAMPDIFF(DAY, l.FromDate, l.ToDate) + 1) as total_dias
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN user_departments ud ON u.id = ud.UserID
            JOIN tbldepartments d ON ud.DepartmentID = d.id
            WHERE 1=1 " . $dateFilters['whereClause'] . "
            GROUP BY d.DepartmentName
            ORDER BY total_dias DESC
            LIMIT 5";
    
    $stmt = $conn->prepare($query);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 6. Promedio de días por departamento
function getPromedioDiasPorDepartamento($conn) {
    $dateFilters = getDateFilters();
    
    $query = "SELECT d.DepartmentName, 
                    AVG(TIMESTAMPDIFF(DAY, l.FromDate, l.ToDate) + 1) as promedio_dias
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN user_departments ud ON u.id = ud.UserID
            JOIN tbldepartments d ON ud.DepartmentID = d.id
            WHERE 1=1 " . $dateFilters['whereClause'] . "
            GROUP BY d.DepartmentName
            ORDER BY promedio_dias DESC";
    
    $stmt = $conn->prepare($query);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 7. Tendencias por tiempo (mes, trimestre, año)
function getTendenciasPorTiempo($conn) {
    $groupBy = isset($_GET['groupBy']) ? $_GET['groupBy'] : 'month';
    $year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
    $sede = isset($_GET['sede']) ? intval($_GET['sede']) : null;
    $department = isset($_GET['department']) ? intval($_GET['department']) : null;

    $where = "WHERE YEAR(l.PostingDate) = :year";
    $params = [':year' => $year];

    // Agregar filtros opcionales
    if ($sede !== null) {
        $where .= " AND u.SedeID = :sede";
        $params[':sede'] = $sede;
    }

    if ($department !== null) {
        $where .= " AND ud.DepartmentID = :department";
        $params[':department'] = $department;
    }

    // Armar la consulta dependiendo de la agrupación
    switch ($groupBy) {
        case 'month':
            $query = "SELECT 
                        MONTH(l.PostingDate) as time_period,
                        COUNT(l.id) as total_solicitudes
                    FROM tblleaves l
                    JOIN tblusers u ON l.empid = u.id
                    JOIN user_departments ud ON u.id = ud.UserID
                    $where
                    GROUP BY MONTH(l.PostingDate)
                    ORDER BY MONTH(l.PostingDate)";
            break;
        case 'quarter':
            $query = "SELECT 
                        QUARTER(l.PostingDate) as time_period,
                        COUNT(l.id) as total_solicitudes
                    FROM tblleaves l
                    JOIN tblusers u ON l.empid = u.id
                    JOIN user_departments ud ON u.id = ud.UserID
                    $where
                    GROUP BY QUARTER(l.PostingDate)
                    ORDER BY QUARTER(l.PostingDate)";
            break;
        case 'year':
            $query = "SELECT 
                        YEAR(l.PostingDate) as time_period,
                        COUNT(l.id) as total_solicitudes
                    FROM tblleaves l
                    JOIN tblusers u ON l.empid = u.id
                    JOIN user_departments ud ON u.id = ud.UserID
                    GROUP BY YEAR(l.PostingDate)
                    ORDER BY YEAR(l.PostingDate)";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        default:
            return ["error" => "Agrupación no válida"];
    }

    // Ejecutar consulta
    $stmt = $conn->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 8. Distribución por tipo de permiso
function getTipologiaPermisos($conn) {
    $dateFilters = getDateFilters();
    
    $query = "SELECT lt.LeaveType, COUNT(l.id) as total
            FROM tblleaves l
            JOIN tblleavetype lt ON l.LeaveTypeID = lt.id
            JOIN tblusers u ON l.empid = u.id
            JOIN sede sd ON u.SedeID = sd.id
            JOIN user_departments ud ON u.id = ud.UserID
            WHERE 1=1 " . $dateFilters['whereClause'] . "
            GROUP BY lt.LeaveType
            ORDER BY total DESC";
    
    $stmt = $conn->prepare($query);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 9. Distribución de permisos por rol
function getDistribucionRolPorPermisos($conn) {
    $dateFilters = getDateFilters();
    
    $query = "SELECT r.rol, COUNT(l.id) as total_permisos
            FROM tblleaves l
            JOIN tblusers u ON l.empid = u.id
            JOIN tblrol r ON u.RolID = r.id
            JOIN sede sd ON u.SedeID = sd.id
            JOIN user_departments ud ON u.id = ud.UserID
            WHERE 1=1 " . $dateFilters['whereClause'] . "
            GROUP BY r.rol
            ORDER BY total_permisos DESC";
    
    $stmt = $conn->prepare($query);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// 10. Estadísticas generales para el dashboard
function getEstadoDashboard($conn) {
    $dateFilters = getDateFilters();
    
    // Total solicitudes
    $query1 = "SELECT COUNT(DISTINCT l.id) as total_solicitudes FROM tblleaves l             
            JOIN tblusers u ON l.empid = u.id
            JOIN sede sd ON u.SedeID = sd.id
            JOIN user_departments ud ON u.id = ud.UserID
             WHERE 1=1 " . $dateFilters['whereClause'];

    $stmt1 = $conn->prepare($query1);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt1->bindValue($key, $value);
    }
    $stmt1->execute();
    $totalSolicitudes = $stmt1->fetch(PDO::FETCH_ASSOC)['total_solicitudes'];
    
    // Total días solicitados
    $query2 = "SELECT SUM(TIMESTAMPDIFF(DAY, l.FromDate, l.ToDate) + 1) as total_dias 
                FROM tblleaves l            
                JOIN tblusers u ON l.empid = u.id
                JOIN sede sd ON u.SedeID = sd.id
                JOIN user_departments ud ON u.id = ud.UserID 
                WHERE 1=1 " . $dateFilters['whereClause'];

    $stmt2 = $conn->prepare($query2);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt2->bindValue($key, $value);
    }
    $stmt2->execute();
    $totalDias = $stmt2->fetch(PDO::FETCH_ASSOC)['total_dias'];
    
    // Solicitudes por estado
    $query3 = "SELECT s.status, COUNT(DISTINCT l.id) as total
                FROM tblleaves l
                JOIN tblstatus s ON l.Status = s.id
                JOIN tblusers u ON l.empid = u.id
                JOIN sede sd ON u.SedeID = sd.id
                JOIN user_departments ud ON u.id = ud.UserID
                WHERE 1=1 " . $dateFilters['whereClause'] . "
                GROUP BY s.status";
    $stmt3 = $conn->prepare($query3);
    foreach ($dateFilters['params'] as $key => $value) {
        $stmt3->bindValue($key, $value);
    }
    $stmt3->execute();
    $porEstado = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
    // Resultado final
    return [
        "total_solicitudes" => $totalSolicitudes,
        "total_dias" => $totalDias,
        "por_estado" => $porEstado
    ];
}
?>
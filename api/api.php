<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . '/../models/Sede.php';
require_once __DIR__ . "/../repository/SedeRepository.php";

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../repository/UserRepository.php";

require_once __DIR__ . "/../models/Department.php";
require_once __DIR__ . "/../Repository/DepartmentRepository.php";

// Obtener parámetros desde la URL (GET)
$sede_id = isset($_GET['sede_id']) ? $_GET['sede_id'] : null;
$id_user = isset($_GET['id_user']) ? $_GET['id_user'] : null;

if ($sede_id !== null) {
    if ($sede_id != 0) {
        $sede = new Sede();
        $sedeRepository = new SedeRepository($dbh, $sede);
        $departments = $sedeRepository->getDepartmentsBySede($sede_id);
        echo json_encode($departments);
        exit;
    } else {
        $department = new Department();
        $departmentRepository = new DepartmentRepository($dbh, $department);
        $departments = $departmentRepository->get();

        foreach ($departments as $department) {
            $department->department_id = $department->id;
            unset($department->id);
        }

        echo json_encode($departments);
        exit;
    }
}

if ($id_user !== null) {
    $user = new User();
    $userRepository = new UserRepository($dbh, $user);
    $userToEdit = $userRepository->getByIdUser($id_user);
    echo json_encode($userToEdit);
    exit;
}
?>
<?php
session_start();
include("includes/config.php");
require_once __DIR__ . "/models/Auth.php";
require_once __DIR__ . "/models/ManagerSession.php";
require_once __DIR__ . "/models/User.php";
require_once __DIR__ . "/Repository/UserRepository.php";
require_once __DIR__ . "/models/UserDepartment.php";
require_once __DIR__ . "/Repository/UserDepartmentRepository.php";

if (isset($_POST['auth'])) {
    $email = $_POST['correo'];
    $password = $_POST['contrasena'];

    $Auth = new Auth($dbh);
    $ManagerSession = new ManagerSession();
    $results = $Auth->authenticate($email, $password);

    $user = new User();
    $userRepository = new UserRepository($dbh, $user);

    $userDepartment = new UserDepartment();
    $userDepartmentRepository = new UserDepartmentRepository($dbh, $userDepartment);

    if ($results === true) {
        $usuario = $userRepository->getUserByEmail($email);
        $departmentsUser = $userDepartmentRepository->getUserDepartment(['UserID' => $usuario[0]->id]);
        $ManagerSession->setSession('user_id', $usuario[0]->id);
        $ManagerSession->setSession('rol_id', $usuario[0]->RolID);
        
        foreach ($departmentsUser as $department) {
            if ($department->DepartmentID === 4) {
                $ManagerSession->setSession('super_admin', true);
            } else {
                $ManagerSession->setSession('super_admin', false);
            }
        }

        $rol_id = $ManagerSession->getSession('rol_id');
        $super_admin = $ManagerSession->getSession('super_admin');

        if ($rol_id && ($rol_id === 2 || $rol_id === 3 || $super_admin)) {
            echo "<script type='text/javascript'> document.location = 'views/leaves/leaves.php'; </script>";
        } else {
            echo "<script type='text/javascript'> document.location = 'views/apply-leave/apply-leave.php'; </script>";
        }

    } else {
        $error = $results['error'] ?? 'Error de autenticación';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        :root {
            --primary-color: <?php echo $_SERVER['PROJECT_NAME'] === 'GoPermisos' ? 'red' : '#1976D2' ?>;
            --primary-dark: <?php echo $_SERVER['PROJECT_NAME'] === 'GoPermisos' ? '#c0392b' : '#115293' ?>;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@600&display=swap" rel="stylesheet">
</head> 
<body>
    <div id="container-general">
        <h1>Sistema de Gestión de Permisos</h1>
        <form action="" method="post">
            <label for="correo">Correo</label>
            <input type="text" id="correo" name="correo" required>
            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required>
            <div class="error-message">
                <?php echo $error ?? '' ?>
            </div>
            <input type="submit" name="auth" value="Ingresar">
        </form>
    </div>
</body>
</html>
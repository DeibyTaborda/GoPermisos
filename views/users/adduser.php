<?php
session_start();
error_reporting(0);
include('../../includes/config.php');
require_once __DIR__ . "/../../utils/alerts.php";
require_once __DIR__ . '/../../models/Rol.php';
require_once __DIR__ . '/../../models/Department.php';
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../models/UserDepartment.php";
require_once __DIR__ . '/../../generic/RepositoryGeneric.php';
require_once __DIR__ . "/../../Repository/UserRepository.php";
require_once __DIR__ . "/../../Repository/UserDepartmentRepository.php";
require_once __DIR__ . "/../../Repository/UserDepartmentManager.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$rol = new Rol();
$department = new Department();
$user = new User();
$userDepartment = new UserDepartment();
$rolRepositoryGeneric = new RepositoryGeneric($dbh, $rol);
$departmentRepositoryGeneric = new RepositoryGeneric($dbh, $department);
$userRepository = new UserRepository($dbh, $user);
$userDepartmentRepository = new UserDepartmentRepository($dbh, $userDepartment);
$userDepartmentManager = new UserDepartmentManager($dbh, $userRepository, $userDepartmentRepository);

$ManagerSession = new ManagerSession();
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

if (!$super_admin) {
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
    exit;
}

    $action = $_GET['action'] ?? null;
    $idUser = $_GET['id_user'] ?? null;

    $roles = $rolRepositoryGeneric->get();
    $departments = $departmentRepositoryGeneric->get();
    $assignedDepartmentIDs = [];

    if (isset($_POST['add']) || isset($_POST['edit'])) {
        $dataUser = [
            'EmpId' => $_POST['EmpId'] ?? null,
            'FirstName' => $_POST['FirstName'] ?? null,
            'LastName' => $_POST['LastName'] ?? null,
            'Password' => $_POST['Password'] ?? null,
            'EmailId' => $_POST['EmailId'] ?? null,
            'confirmpassword' => $_POST['confirmpassword'] ?? null,
            'Gender' => $_POST['Gender'] ?? null,
            'Dob' => $_POST['Dob'] ?? null,
            'Country' => $_POST['Country'] ?? null,
            'City' => $_POST['City'] ?? null,
            'Address' => $_POST['Address'] ?? null,
            'Phonenumber' => $_POST['Phonenumber'] ?? null,
            'RolID' => $_POST['RolID'] ?? null
        ];

        if (isset($_POST['DepartmentID'])) {
            $selectedDepartments = [$_POST['DepartmentID']];
        } elseif (isset($_POST['DepartmentsID'])) {
            $selectedDepartments = $_POST['DepartmentsID'];
        } else {
            $selectedDepartments = [];
        }
        
        if (empty($selectedDepartments)) {
            showToast('error', 'No se ingresaron departamentos');
        }

        if (isset($_POST['add'])) {
            $request = $userDepartmentManager->createUser($dataUser, $selectedDepartments);
        } else if(isset($_POST['edit'])) {
            $request = $userDepartmentManager->updateUser($idUser, $dataUser, $selectedDepartments);
        }

        if (isset($request['success']) && $request['success']) {
            showToast('success',  $request['message']);
        } else if(isset($request['type']) && $request['type'] === 'info') {
            showToast('info',  $request['message']);
        } else if (isset($request['success']) && !$request['success']) {
            showToast('error',  $request['message']);
        }
    }

    if ($idUser) {
        $userToEdit = $userRepository->getByIdUser($idUser);
        $departmentsUser = $userDepartmentRepository->getByIdUserDepartments($idUser);
        $numberDepartments = count($departmentsUser);
        $assignedDepartmentIDs = array_map(fn($department) => $department->id, $departmentsUser);
    }

    $rol = new Rol();
    $department = new Department();
    $rolRepositoryGeneric = new RepositoryGeneric($dbh, $rol);
    $departmentRepositoryGeneric = new RepositoryGeneric($dbh, $department);
    
    $roles = $rolRepositoryGeneric->get();
    $departments = $departmentRepositoryGeneric->get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Usuario</title>
    <link rel="stylesheet" href="../../assets/css/header.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link rel="stylesheet" href="../../assets/css/pages/adduser.css">
    <link rel="stylesheet" href="../../assets/css/general.css">
    <link rel="stylesheet" href="../../assets/css/alerts/alerts.css?v=1.0.0">
</head>
<body>
    <?php include('../../components/header.php'); ?>

    <main style="display: flex; width: 100%;">
        <?php include('../../components/sidebar.php'); ?>
        <div id="container-principal">
            <div class="container-content">
                <h1>Agregar usuario</h1>
                <form method="post" id="form-user">
                    <div class="column">
                        <input type="number" id="EmpId" name="EmpId" placeholder=" " value="<?php echo $userToEdit->EmpId ?? '' ?>" required>
                        <label for="EmpId">Documento</label>
                    </div>
                    <div class="column">
                        <input type="text" id="FirstName" name="FirstName" placeholder=" "  value="<?php echo $userToEdit->FirstName ?? '' ?>"  required>
                        <label for="FirstName">Nombre</label>
                    </div>
                    <div class="column">
                        <input type="text" id="LastName" name="LastName" placeholder=" " value="<?php echo $userToEdit->LastName ?? '' ?>">
                        <label for="LastName">Apellido</label>
                    </div>
                    <div class="column">
                       <select name="RolID" id="RolID">
                            <?php if ($action === 'edit'): ?>
                                <option value="<?= $userToEdit->RolID ?>"><?= $userToEdit->rol ?></option>
                            <?php else: ?>
                                <option disabled selected>Rol...</option>
                            <?php endif; ?>
                            <?php
                                foreach($roles as $rol) {
                                    echo "<option value='$rol->id'>$rol->rol</option>";
                                }
                            ?>
                       </select>
                    </div>
                    <select name="DepartmentID" id="DepartmentID">
                            <?php if ($action !== 'edit') : ?>
                            <option disabled selected>Departamento...</option>
                        <?php endif ?>   
                        <?php
                            foreach($departments as $department) {
                                if ($department->StatusId === 1) {
                                    $selected = in_array($department->id, $assignedDepartmentIDs) ? 'selected' : '';
                                    echo "<option value='$department->id' $selected>$department->DepartmentName</option>";
                                }
                            }
                        ?>
                    </select>
                    <div id="container-checkboxes">
                        <?php
                            foreach($departments as $department) {
                                if ($department->StatusId === 1) {
                                    $isChecked = in_array($department->id, $assignedDepartmentIDs) ? 'checked' : '';
                                    echo "<label class='label-checkbox'><input type='checkbox' name='DepartmentsID[]' value='$department->id' $isChecked>$department->DepartmentName</label>";
                                }
                            }
                        ?>
                    </div>
                    <div class="column">
                        <input type="email" id="EmailId" name="EmailId" placeholder=" " value="<?php echo $userToEdit->EmailId ?? '' ?>" required>
                        <label for="EmailId">Correo</label>
                    </div>
                    <div class="column">
                        <input type="password" id="Password" name="Password" placeholder=" " value="<?php echo $userToEdit->Password ?? '' ?>" required>
                        <label for="Password">Contraseña</label>
                    </div>
                    <div class="column">
                        <input type="password" id="confirmpassword" name="confirmpassword" placeholder=" " value="<?php echo $userToEdit->Password ?? '' ?>" required>
                        <label for="confirmpassword">Confirmar contraseña</label>
                    </div>
                    <div class="column">
                        <select name="Gender">
                            <?php if ($action === 'edit'): ?>
                                <option value="<?= $userToEdit->Gender ?>"><?= $userToEdit->Gender ?></option>
                            <?php else: ?>
                                <option disabled selected>Genero...</option>
                            <?php endif; ?>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="column">
                        <input type="text" id="Dob" name="Dob" placeholder=" " value="<?php echo $userToEdit->Dob ?? '' ?>">
                        <label for="Dob">Fecha de nacimiento</label>
                    </div>
                    <div class="column">
                        <input type="text" id="Address" name="Address" placeholder=" " value="<?php echo $userToEdit->Address ?? '' ?>">
                        <label for="Address">Dirección</label>
                    </div>
                    <div class="column">
                        <input type="text" id="Country" name="Country" placeholder=" " value="<?php echo $userToEdit->Country ?? '' ?>">
                        <label for="Country">País</label>
                    </div>
                    <div class="column">
                        <input type="text" id="City" name="City" placeholder=" " value="<?php echo $userToEdit->City ?? '' ?>">
                        <label for="City">Ciudad</label>
                    </div>
                    <div class="column">
                        <input type="number" id="Phonenumber" name="Phonenumber" placeholder=" " value="<?php echo $userToEdit->Phonenumber ?? '' ?>">
                        <label for="Phonenumber">Número de teléfono</label>
                    </div>
                    <input type="submit" id="button-submit" name="<?php echo $action === 'edit' ? 'edit' : 'add'; ?>" value="<?php echo $action === 'edit' ? 'ACTUALIZAR' : 'AGREGAR'; ?> EMPLEADO">
                </form>
            </div>
        </div>
    </main>
    <div
        id="dataUserToEdit"
        data-action="<?php echo $action ?>"
        data-numberdepartments="<?php echo $numberDepartments ?? 0 ?>"
        data-rol="<?php echo $userToEdit->RolID ?? null ?>"
        >
    </div>
    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/pages/formuser.js"></script>
</body>
</html>
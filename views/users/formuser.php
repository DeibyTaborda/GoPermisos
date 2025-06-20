<?php
session_start();
// error_reporting(0);
include('../../includes/config.php');
require_once __DIR__ . "/../../utils/alerts.php";
require_once __DIR__ . '/../../models/Rol.php';
require_once __DIR__ . '/../../models/Department.php';
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../models/Sede.php";
require_once __DIR__ . "/../../models/UserDepartment.php";
require_once __DIR__ . '/../../generic/RepositoryGeneric.php';
require_once __DIR__ . "/../../Repository/UserRepository.php";
require_once __DIR__ . "/../../Repository/UserDepartmentRepository.php";
require_once __DIR__ . "/../../Repository/UserDepartmentManager.php";
require_once __DIR__ . "/../../models/ManagerSession.php";
require_once __DIR__ . "/../../Repository/SedeRepository.php";
require_once __DIR__. "/../../models/UserPermissions.php";
require_once __DIR__ . "/../../repository/UserPermissionsRepository.php";

$rol = new Rol();
$department = new Department();
$user = new User();
$userDepartment = new UserDepartment();
$sede = new Sede();
$userPermissions = new UserPermissions();
$rolRepositoryGeneric = new RepositoryGeneric($dbh, $rol);
$departmentRepositoryGeneric = new RepositoryGeneric($dbh, $department);
$userRepository = new UserRepository($dbh, $user);
$userDepartmentRepository = new UserDepartmentRepository($dbh, $userDepartment);
$userPermissionsRepository = new UserPermissionsRepository($dbh, $userPermissions);
$userDepartmentManager = new UserDepartmentManager($dbh, $userRepository, $userDepartmentRepository, $userPermissionsRepository);
$sedeRepositoryGeneric = new RepositoryGeneric($dbh, $sede);


$ManagerSession = new ManagerSession();
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

if (!$super_admin) {
    echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    exit;
}

    $action = $_GET['action'] ?? null;
    $idUser = $_GET['id_user'] ?? null;

    $roles = $rolRepositoryGeneric->get();
    $departments = $departmentRepositoryGeneric->get();
    $assignedDepartmentIDs = [];
    $permissions_user = [];

    if (isset($_POST['form_action'])) {
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
            'RolID' => $_POST['RolID'] ?? null,
            'SedeID' => $_POST['SedeID'] ?? null,
        ];

        if (isset($_POST['DepartmentID'])) {
            $selectedDepartments = [$_POST['DepartmentID']];
        } elseif (isset($_POST['user_permissions'])) {
            $selectedDepartments = array_unique(array_column($_POST['user_permissions'], 'DepartmentID'));
        } else {
            $selectedDepartments = [];
        }

        if (empty($selectedDepartments)) {
            showToast('error', 'No se ingresaron departamentos');
        }

        if (isset($_POST['user_permissions'])) {
            $permissions_user = $_POST['user_permissions'];
        }

        if ($_POST['form_action'] === 'add') {
            $request = $userDepartmentManager->createUser($dataUser, $selectedDepartments, $permissions_user);
        } else if($_POST['form_action'] === 'edit') {
            $request = $userDepartmentManager->updateUser($idUser, $dataUser, $selectedDepartments, $permissions_user);
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

        if ($userToEdit->RolID === 3 || $userToEdit->RolID === 2) {
            $user_permissions = $userPermissionsRepository->getPermissionsByUserId($idUser);
        }

        if ($userToEdit->RolID === 1 || $userToEdit->RolID === 2) {
            $collaboratorDepartment = array_map(fn($department) => $department->department_id, $departmentsUser);
        }
    }

    $rol = new Rol();
    $department = new Department();
    $rolRepositoryGeneric = new RepositoryGeneric($dbh, $rol);
    $departmentRepositoryGeneric = new RepositoryGeneric($dbh, $department);
    
    $roles = $rolRepositoryGeneric->get();
    $departments = $departmentRepositoryGeneric->get();
    $sedes = $sedeRepositoryGeneric->get();

    require_once __DIR__ . "/../../components/header.php";
    require_once __DIR__ . "/../../components/sidebar.php";
?>
    <script>
        const permissionsDir = <?php echo isset($permissionsDir) ? json_encode($permissionsDir) : json_encode([]); ?>;
        const departmentsUser = <?php echo isset($departmentsUser) ? json_encode($departmentsUser) : json_encode([]); ?>;
        const collaboratorDepartment = <?php echo isset($collaboratorDepartment) ? json_encode($collaboratorDepartment) : json_encode([])  ?>;
        const departments = <?php echo json_encode($departments); ?>;
        const sedes = <?php echo json_encode($sedes); ?>;
        const roles = <?php echo json_encode($roles); ?>;
        const existingPermissions = <?php echo json_encode($user_permissions ?? []); ?>;
    </script>

<main class="app-main">
    <div class="app-content">
        <div class="page-header">
            <h1><?php echo $action === 'edit' ? 'Editar Usuario' : 'Agregar Usuario'; ?></h1>
            <nav class="breadcrumb">
                <a href="../leaves/leaves.php">Inicio</a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <a href="../../views/users/users.php">Usuarios</a>
                <span class="separator"><i class="fas fa-chevron-right"></i></span>
                <span><?php echo $action === 'edit' ? 'Editar' : 'Agregar'; ?></span>
            </nav>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="post" id="userForm" class="modern-form">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="EmpId">Documento</label>
                            <div class="input-with-icon">
                                <i class="fas fa-id-card"></i>
                                <input type="number" id="EmpId" name="EmpId" class="form-control" 
                                       value="<?php echo $userToEdit->EmpId ?? '' ?>" required>
                            </div>
                        </div>
                       
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="FirstName">Nombre</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="FirstName" name="FirstName" class="form-control" 
                                       value="<?php echo $userToEdit->FirstName ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="LastName">Apellido</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="LastName" name="LastName" class="form-control" 
                                       value="<?php echo $userToEdit->LastName ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="EmailId">Correo electrónico</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="EmailId" name="EmailId" class="form-control" 
                                       value="<?php echo $userToEdit->EmailId ?? '' ?>" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="Gender">Género</label>
                            <div class="input-with-icon">
                                <i class="fas fa-venus-mars"></i>
                                <select name="Gender" id="Gender" class="form-control">
                                    <?php if ($action === 'edit'): ?>
                                        <option value="<?= $userToEdit->Gender ?>"><?= $userToEdit->Gender ?></option>
                                    <?php else: ?>
                                        <option value="" disabled selected>Seleccione género...</option>
                                    <?php endif; ?>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="Password">Contraseña</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="Password" name="Password" class="form-control" 
                                       value="<?php echo $userToEdit->Password ?? '' ?>" <?= $action === 'edit' ? '' : 'required' ?>>
                                <button type="button" class="toggle-password" data-target="Password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="confirmpassword">Confirmar contraseña</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" 
                                       value="<?php echo $userToEdit->Password ?? '' ?>" <?= $action === 'edit' ? '' : 'required' ?>>
                                <button type="button" class="toggle-password" data-target="confirmpassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                         <div class="form-group col-md-6">
                            <label for="RolID">Rol</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user-tag"></i>
                                <select name="RolID" id="RolID" class="form-control">
                                    <?php if ($action === 'edit'): ?>
                                        <option value="<?= $userToEdit->RolID ?>"><?= $userToEdit->rol ?></option>
                                    <?php else: ?>
                                        <option value="" disabled selected>Seleccione un rol...</option>
                                    <?php endif; ?>
                                    <?php foreach($roles as $rol): ?>
                                        <option value="<?= $rol->id ?>"><?= $rol->rol ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="SedeID">Sede</label>
                            <div class="input-with-icon">
                                <i class="fas fa-building"></i>
                                <select name="SedeID" id="SedeID" class="form-control">
                                    <?php if ($action !== 'edit'): ?>
                                        <option value="" disabled selected>Seleccione sede...</option>
                                    <?php endif; ?>
                                    <?php foreach($sedes as $sede): ?>
                                        <?php if ($sede->status_id === 1): ?>
                                            <?php $selected = (isset($userToEdit) && $userToEdit->SedeID === $sede->id) ? 'selected' : ''; ?>
                                            <option value="<?= $sede->id ?>" <?= $selected ?>>
                                                <?= $sede->sede ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6" id="department">
                            <label for="DepartmentID">Departamento</label>
                            <div class="input-with-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-section">
                        <h3 id="title-permissions-department"></h3>
                        <div id="dynamic-permissions" class="mt-3"></div>
                        <button type="button" id="add-permission-group" class="custom-btn" style="display: none;">
                            Agregar más permisos
                        </button>
                    </div>

                    <div class="form-section">
                        <h3>Información adicional</h3>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="Dob">Fecha de nacimiento</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" id="Dob" name="Dob" class="form-control" 
                                           value="<?php echo $userToEdit->Dob ?? '' ?>">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Phonenumber">Teléfono</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="number" id="Phonenumber" name="Phonenumber" class="form-control" 
                                           value="<?php echo $userToEdit->Phonenumber ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="Country">País</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-globe"></i>
                                    <input type="text" id="Country" name="Country" class="form-control"
                                           value="<?php echo $userToEdit->Country ?? '' ?>">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="City">Ciudad</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-city"></i>
                                    <input type="text" id="City" name="City" class="form-control"
                                           value="<?php echo $userToEdit->City ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Address">Dirección</label>
                            <div class="input-with-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" id="Address" name="Address" class="form-control" 
                                       value="<?php echo $userToEdit->Address ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <input type="hidden" name="form_action" value="<?php echo $action === 'edit' ? 'edit' : 'add'; ?>">
                        <button type="submit" name="<?php echo $action === 'edit' ? 'edit' : 'add'; ?>" 
                                class="btn btn-primary btn-submit">
                            <i class="fas fa-save"></i>
                            <?php echo $action === 'edit' ? 'ACTUALIZAR USUARIO' : 'AGREGAR USUARIO'; ?>
                        </button>
                        <a href="users.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> CANCELAR
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../../assets/js/main/main.js"></script>
<script src="../../assets/js/main/form.js?v=1.5"></script>
<?php
session_start();
// error_reporting(0);
include('../../../includes/config.php');
require_once __DIR__ . "/../../../utils/alerts.php";
require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../../../models/UserDepartment.php";
require_once __DIR__ . "/../../../repository/UserDepartmentRepository.php";
require_once __DIR__ . "/../../../Repository/UserRepository.php";
require_once __DIR__ . "/../../../controllers/UserController.php";
require_once __DIR__ . "/../../../models/ManagerSession.php";

require_once __DIR__ . "/../../../models/UserPermissions.php";
require_once __DIR__ . "/../../../Repository/UserPermissionsRepository.php";

$ManagerSession = new ManagerSession();
$super_admin = $ManagerSession->getSession('super_admin');
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');

if(!empty($user_id)) {
    $user = new User();
    $userRepository = new UserRepository($dbh, $user);
    $userController = new UserController($userRepository);

    $userDepartment = new UserDepartment();
    $userDepartmentRepository = new UserDepartmentRepository($dbh, $userDepartment);

    $userPermissions = new UserPermissions();
    $userPermissionsRepository = new UserPermissionsRepository($dbh, $userPermissions);

    if(isset($_POST['update'])) {
        $data = [
            'EmpId' => $_POST['EmpId'],
            'FirstName' => $_POST['FirstName'],
            'LastName' => $_POST['LastName'],
            'EmailId' => $_POST['EmailId'],
            'Phonenumber' => $_POST['Phonenumber'],
            'Country' => $_POST['Country'],
            'City' => $_POST['City'],
            'Address' => $_POST['Address'],
            'Dob' => $_POST['Dob'],
            'Gender' => $_POST['Gender']
        ];

        $result = $userController->updateUser($user_id, $data);

        if (isset($result['success']) && $result['success'] === true) {
            showToast('success', 'Perfil actualizado correctamente');
        } else if (isset($result['type']) && $result['type'] === 'info') {
            showToast('info', $result['message']);
        }
    }

    $results = $userRepository->getUsers(['id' => $user_id]);
    $usuario = $results[0];
    $departments_user = [];

    if ($usuario->RolID === 3 || $usuario->RolID === 2) {
        $userPermissions = $userPermissionsRepository->getPermissionsByUserId2($user_id);
    }
?>

    <script>
        const profileRol = <?php echo json_encode($rol_id); ?>;
    </script>

    <?php include('../../../components/header.php');?>
    <?php include('../../../components/sidebar.php');?>
   <script>localStorage.setItem('rol', <?php echo $rol_id; ?>);</script>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Actualizar Información de Empleado</h1>
                <nav class="breadcrumb">
                    <a href="../../apply-leave/apply-leave.php">Inicio</a>
                    <!-- <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="../users/users.php">Empleados</a> -->
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Actualizar</span>
                </nav>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Datos del Empleado</h2>
                </div>

                <form method="post" name="updatemp" class="modern-form">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="EmpId">Cédula del Empleado</label>
                            <div class="input-with-icon">
                                <i class="fas fa-id-card"></i>
                                <input type="text" class="form-control readonly" id="EmpId" name="EmpId" 
                                       value="<?php echo htmlentities($usuario->EmpId);?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="FirstName">Nombre</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" class="form-control readonly" id="FirstName" name="FirstName" 
                                       value="<?php echo htmlentities($usuario->FirstName);?>" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="LastName">Apellido</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" class="form-control readonly" id="LastName" name="LastName" 
                                       value="<?php echo htmlentities($usuario->LastName);?>" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6" id="singleDepartmentProfile">
                            <label for="DepartmentID">Departamento</label>
                            <div class="input-with-icon">
                                <i class="fas fa-building"></i>
                                <input type="text" class="form-control readonly" id="DepartmentID" 
                                       value="<?php echo htmlentities($usuario->DepartmentName);?>" readonly>
                            </div>
                        </div>
                       <div class="form-group col-md-12" id="checkboxes-departmens-profile" style="display: none;">
                            <label class="form-label fw-bold mb-3">Departamentos, sedes y roles a gestionar</label>
                            <div class="card p-4 border-0 shadow-sm" style="background-color: #f8f9fa; border-radius: 12px;">
                                <div class="row g-4">
                                    <?php foreach ($userPermissions as $permission): ?>
                                        <div class="col-md-4">
                                            <div class="department-card p-3 h-100 rounded border-0 shadow-sm" 
                                                style="background-color: white; transition: all 0.3s ease;">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="icon-circle me-3" style="background-color: #f0f7ff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-building text-primary"></i>
                                                    </div>
                                                    <h6 class="mb-0 text-dark fw-semibold"><?php echo $permission->DepartmentName; ?></h6>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="icon-circle me-3" style="background-color: #e6ffe6; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-map-pin text-success"></i>
                                                    </div>
                                                    <span class="text-muted"><?php echo isset($permission->sede) ? $permission->sede : ''; ?></span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-circle me-3" style="background-color: #fff0f6; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-user-tag text-danger"></i>
                                                    </div>
                                                    <span class="text-muted"><?php echo $permission->rol; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="Sede">Sede</label>
                            <div class="input-with-icon">
                                <i class="fas fa-map-pin"></i>
                                <input type="text" class="form-control readonly" id="Sede" 
                                    value="<?php echo htmlentities($usuario->sede ?? ''); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="EmailId">Correo Electrónico</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" class="form-control" id="EmailId" name="EmailId" 
                                       value="<?php echo htmlentities($usuario->EmailId);?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="Phonenumber">Teléfono Móvil</label>
                            <div class="input-with-icon">
                                <i class="fas fa-phone"></i>
                                <input type="tel" class="form-control" id="Phonenumber" name="Phonenumber" 
                                       value="<?php echo htmlentities($usuario->Phonenumber);?>" maxlength="10">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="Gender">Género</label>
                            <div class="input-with-icon">
                                <i class="fas fa-venus-mars"></i>
                                <select class="form-control" id="Gender" name="Gender">
                                    <option value="<?php echo htmlentities($usuario->Gender);?>" selected>
                                        <?php echo htmlentities($usuario->Gender);?>
                                    </option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label for="Dob">Fecha de Nacimiento</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-alt"></i>
                                <input type="text" class="form-control" id="Dob" name="Dob" 
                                       value="<?php echo htmlentities($usuario->Dob);?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="Address">Dirección</label>
                            <div class="input-with-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" class="form-control" id="Address" name="Address" 
                                       value="<?php echo htmlentities($usuario->Address);?>">
                            </div>
                        </div>
                        
                        <div class="form-group col-md-3">
                            <label for="City">Ciudad</label>
                            <div class="input-with-icon">
                                <i class="fas fa-city"></i>
                                <input type="text" class="form-control" id="City" name="City" 
                                       value="<?php echo htmlentities($usuario->City);?>">
                            </div>
                        </div>
                        
                        <div class="form-group col-md-3">
                            <label for="Country">País</label>
                            <div class="input-with-icon">
                                <i class="fas fa-globe"></i>
                                <input type="text" class="form-control" id="Country" name="Country" 
                                       value="<?php echo htmlentities($usuario->Country);?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <input type="hidden" name="update">
                        <button type="submit" name="update" class="btn btn-primary btn-submit">
                            <i class="fas fa-save"></i> ACTUALIZAR PERFIL
                        </button>
                        <a href="../../apply-leave/leavehistory.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/js/main/main.js"></script>
    <script src="../../../assets/js/main/form.js?v=1.3"></script>
    <script src="../../../assets/js/pages/myprofile.js?v=1.1"></script>
</html>
<?php } else{
            header('location:index.php');
    } 
?> 
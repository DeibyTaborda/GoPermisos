<?php
session_start();
error_reporting(0);
include('../../admin/includes/config.php');
require_once __DIR__ . "/../../utils/alerts.php";
require_once __DIR__ .'/../../models/Department.php';
require_once __DIR__ . "/../../repository/DepartmentRepository.php";
require_once __DIR__ . "/../../controllers/DepartmentController.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$department = new Department();
$departmentRepository = new DepartmentRepository($dbh, $department);
$departmentController = new DepartmentController($departmentRepository);

$ManagerSession = new ManagerSession();
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

$department_id = $_SESSION['department_id'];

if ($super_admin) { 

    if(isset($_POST['action'])) {
        $data = [
            'DepartmentName' => $_POST['DepartmentName'],
            'DepartmentShortName' => $_POST['DepartmentShortName'],
            'DepartmentCode' => $_POST['DepartmentCode']
        ];

        if ($_POST['action'] === 'edit') {
            $id_department = $_GET['id_department'];
            $request = $departmentController->updateDepartment($id_department, $data);
        } else if ($_POST['action'] === 'add') {
            $request = $departmentController->saveDepartment($data);
        }

        if (isset($request['success']) && $request['success']) {
            showToast('success', $request['message']);
        } else if (isset($request['type']) && $request['type'] === 'info') {
            showToast('info', $request['message']);
        } else if (!$request['success']) {
            showToast('error', $request['message']);
        }
    }

    if (isset($_GET['id_department'])) {
        $id_department = $_GET['id_department'];
        $selectedDepartment = $departmentController->getByIdDepartment($id_department);
    }
?>


    <?php include('./../../components/header.php');?>
    <?php include('./../../components/sidebar.php');?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1><?php echo isset($selectedDepartment) ? 'Editar' : 'Agregar'; ?> Departamento</h1>
                <nav class="breadcrumb">
                    <a href="../leaves/leaves.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="managedepartments.php">Departamentos</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span><?php echo isset($selectedDepartment) ? 'Editar' : 'Agregar'; ?></span>
                </nav>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Información del Departamento</h2>
                </div>

                <form id="form-departments" method="post" class="modern-form">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="DepartmentName">Nombre del Departamento</label>
                            <div class="input-with-icon">
                                <i class="fas fa-building"></i>
                                <input 
                                    type="text" 
                                    id="DepartmentName" 
                                    name="DepartmentName" 
                                    class="form-control" 
                                    value="<?php echo isset($selectedDepartment) ? htmlentities($selectedDepartment[0]->DepartmentName) : ''; ?>" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="DepartmentShortName">Nombre Corto</label>
                            <div class="input-with-icon">
                                <i class="fas fa-abacus"></i>
                                <input 
                                    type="text" 
                                    id="DepartmentShortName" 
                                    name="DepartmentShortName" 
                                    class="form-control" 
                                    value="<?php echo isset($selectedDepartment) ? htmlentities($selectedDepartment[0]->DepartmentShortName) : ''; ?>" 
                                    required
                                >
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="DepartmentCode">Código del Departamento</label>
                            <div class="input-with-icon">
                                <i class="fas fa-code"></i>
                                <input 
                                    type="text" 
                                    id="DepartmentCode" 
                                    name="DepartmentCode" 
                                    class="form-control" 
                                    value="<?php echo isset($selectedDepartment) ? htmlentities($selectedDepartment[0]->DepartmentCode) : ''; ?>" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <input type="hidden" name="action" value="<?php echo isset($selectedDepartment) ? 'edit' : 'add' ?>">
                        <button type="submit" name="add" class="btn btn-primary btn-submit">
                            <i class="fas fa-save"></i>
                            <?php echo isset($selectedDepartment) ? 'ACTUALIZAR DEPARTAMENTO' : 'AGREGAR DEPARTAMENTO'; ?>
                        </button>
                        <a href="managedepartments.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/main/form.js"></script>
   
    <!-- Custom JS -->
    <script src="../../assets/js/main/main.js"></script>


<?php } else {
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
} ?>
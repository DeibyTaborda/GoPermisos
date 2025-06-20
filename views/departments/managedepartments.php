<?php
session_start();
error_reporting(0);
include('../../admin/includes/config.php');
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

if ($super_admin) {       
    if(isset($_GET['inact'])) {
        $departmentController->updateDepartment($_GET['inact'], ['StatusId' => 2]);
    }

    if(isset($_GET['acti'])) {
        $result = $departmentController->updateDepartment($_GET['acti'], ['StatusId' => 1]);
    }

    $departments = $departmentRepository->getDepartments();
?>
    <?php include('./../../components/header.php');?>
    <?php include('./../../components/sidebar.php');?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Gestión de Departamentos</h1>
                <nav class="breadcrumb">
                    <a href="../leaves/leaves.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Departamentos</span>
                </nav>
            </div>

            <div class="data-table-container">
                <div class="table-header">
                    <h2 class="table-title">Listado de Departamentos</h2>
                    <a href="form_department.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Departamento
                    </a>
                </div>

                <table id="departmentTable" class="department-table table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Nombre Corto</th>
                            <th>Código</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($departments as $result): ?>
                        <tr>
                            <td><?= htmlentities($result->id) ?></td>
                            <td><?= htmlentities($result->DepartmentName) ?></td>
                            <td><?= htmlentities($result->DepartmentShortName) ?></td>
                            <td><span class="code-badge"><?= htmlentities($result->DepartmentCode) ?></span></td>
                            <td>
                                <span class="status-badge <?= $result->StatusId == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $result->StatusId == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td><?= htmlentities(DateTime::createFromFormat("Y-m-d H:i:s", $result->CreationDate)->format("d/m/Y h:i A")) ?></td>
                            <td>
                                <?php if ($result->StatusId == 1): ?>
                                    <a href="managedepartments.php?inact=<?= $result->id ?>" 
                                       onclick="return confirm('¿Inactivar este departamento?')"
                                       class="action-btn btn-disable" title="Inactivar">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="managedepartments.php?acti=<?= $result->id ?>" 
                                       onclick="return confirm('¿Activar este departamento?')"
                                       class="action-btn btn-enable" title="Activar">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="form_department.php?id_department=<?= $result->id ?>" 
                                   class="action-btn btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <!-- Custom JS -->
    <script src="../../assets/js/main/main.js"></script>
    <script src="../../assets/js/main/table-config.js"></script>

<?php } else { 
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
} 
?>
<?php
session_start();
error_reporting(0);
include('../../admin/includes/config.php');
require_once __DIR__ .'/../../models/LeaveType.php';
require_once __DIR__ . "/../../repository/LeaveTypeRepository.php";
require_once __DIR__ . "/../../controllers/LeaveTypeController.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$leavetype = new LeaveType();
$leaveTypeRepository = new LeaveTypeRepository($dbh, $leavetype);
$leaveTypeController = new LeaveTypeController($leaveTypeRepository);

$ManagerSession = new ManagerSession();
$super_admin = $ManagerSession->getSession('super_admin');

if ($super_admin) { 
    if (isset($_GET['acti'])) {
        $updateleave = $leaveTypeController->updateLeaveType($_GET['acti'], ['Status' => 1]);
    }

    if (isset($_GET['inact'])) {
        $updateleave = $leaveTypeController->updateLeaveType($_GET['inact'], ['Status' => 2]);
    }

    $leaves = $leaveTypeRepository->getleaveTypes();
?>
    <?php include('./../../components/header.php'); ?>
    <?php include('./../../components/sidebar.php'); ?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Administrar Tipos de Licencias</h1>
                <nav class="breadcrumb">
                    <a href="../leaves/leaves.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Tipos de Licencias</span>
                </nav>
            </div>

            <div class="data-table-container">
                <div class="table-header">
                    <h2 class="table-title">Listado de Tipos de Licencias</h2>
                    <a href="form_leavetype.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Tipo
                    </a>
                </div>

                <table id="leaveTable" class="department-table table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo de Licencia</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($leaves as $leave): ?>
                        <tr>
                            <td><?= htmlentities($leave->id) ?></td>
                            <td><?= htmlentities($leave->LeaveType) ?></td>
                            <td class="description-cell" title="<?= htmlentities($leave->Description) ?>">
                                <?= htmlentities($leave->Description) ?>
                            </td>
                            <td>
                                <span class="status-badge <?= $leave->Status == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $leave->Status == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td><?= htmlentities(DateTime::createFromFormat("Y-m-d H:i:s", $leave->CreationDate)->format("d/m/Y h:i A")) ?></td>
                            <td>
                                <?php if ($leave->Status == 1): ?>
                                    <a href="manageleavetype.php?inact=<?= $leave->id ?>" 
                                       onclick="return confirm('¿Deshabilitar este tipo de licencia?')"
                                       class="action-btn btn-disable" title="Deshabilitar">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="manageleavetype.php?acti=<?= $leave->id ?>" 
                                       onclick="return confirm('¿Activar este tipo de licencia?')"
                                       class="action-btn btn-enable" title="Activar">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="form_leavetype.php?id_leavetype=<?= $leave->id ?>" 
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

        <!-- Custom JS -->
    <script src="../../assets/js/main/main.js"></script>
    <script src="../../assets/js/main/table-config.js"></script>

<?php } else {
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
} ?>
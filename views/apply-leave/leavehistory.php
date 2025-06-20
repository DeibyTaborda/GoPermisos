
<?php
session_start();
error_reporting(0);
include('../../includes/config.php');
require_once __DIR__ .'/../../models/Leave.php';
require_once __DIR__ . "/../../repository/LeaveRepository.php";
require_once __DIR__ . "/../../controllers/LeaveController.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

require_once __DIR__ . "/../../utils/alerts.php";
$ManagerSession = new ManagerSession();
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

if(isset($rol_id)) {
    $leave = new Leave();
    $leaveRepository = new LeaveRepository($dbh, $leave);
    $controllerLeave = new LeaveController($leaveRepository);

    if (isset($_GET['leaveid'])) {
        $result = $controllerLeave->updateLeave($_GET['leaveid'], ['Status' => 6, 'IsRead' => 0]);
        header('location:leavehistory.php');
    }

    $leaves = $leaveRepository->getLeaves(['empid' => $user_id]);
    
        include('../../components/header.php');
        include('../../components/sidebar.php');
    ?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Historial de Permisos</h1>
                <nav class="breadcrumb">
                    <a href="leavehistory.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Permisos</span>
                </nav>
            </div>

            <div class="card">
                <div class="data-table-container">
                    <div class="table-header">
                        <h2 class="table-title">Mis Solicitudes</h2>
                        <a href="apply-leave.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nueva Solicitud
                        </a>
                    </div>
                    
                    <?php if(empty($leaves)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <h5>No hay solicitudes registradas</h5>
                            <p>Aún no has solicitado ningún permiso o licencia.</p>
                            <a href="apply-leave.php" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i>Solicitar Permiso
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="leaveHistoryTable" class="department-table table table-striped table-hover" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Desde</th>
                                        <th>Hasta</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($leaves as $leave): ?>
                                    <tr>
                                        <td><?= htmlentities($leave->id) ?></td>
                                        <td><?= htmlentities($leave->LeaveType) ?></td>
                                        <td><?= htmlentities(formatDate($leave->FromDate)) ?></td>
                                        <td><?= htmlentities(formatDate($leave->ToDate)) ?></td>
                                        <td>
                                            <?php                                          
                                                $stats = $leave->Status;
                                                switch ($stats) {
                                                    case 3:
                                                        echo '<span class="status-badge status-pending"><i class="fas fa-clock me-1"></i> Pendiente</span>';
                                                        break;
                                                    case 4:
                                                        echo '<span class="status-badge status-approved"><i class="fas fa-check-circle me-1"></i> Aprobado</span>';
                                                        break;
                                                    case 5:
                                                        echo '<span class="status-badge status-rejected"><i class="fas fa-times-circle me-1"></i> Rechazado</span>';
                                                        break;
                                                    case 6:
                                                        echo '<span class="status-badge status-cancelled"><i class="fas fa-ban me-1"></i> Cancelado</span>';
                                                        break;
                                                    case 7:
                                                        echo '<span class="status-badge status-voided"><i class="fas fa-eraser me-1"></i> Anulado</span>';
                                                        break;
                                                }                                               
                                            ?>
                                        </td>
                                        <td>
                                            <a href="leave-details.php?leaveid=<?= htmlentities($leave->id) ?>" 
                                               class="action-btn btn-view" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($leave->Status === 3): ?>
                                            <a href="leavehistory.php?leaveid=<?= htmlentities($leave->id) ?>" 
                                               onclick="return confirm('¿Estás seguro de que deseas cancelar esta solicitud?');"
                                               class="action-btn btn-cancel" 
                                               title="Cancelar solicitud">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="../../assets/js/main/main.js"></script>
    <script src="../../assets/js/main/table-config.js"></script>
<?php } else {
            echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
    } 
?>
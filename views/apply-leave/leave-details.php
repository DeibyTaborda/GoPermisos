<?php
session_start();
error_reporting(0);
include('../../includes/config.php');
include('../../utils/utils.php');
require_once __DIR__ . "/../../files/FileManager.php";
require_once __DIR__ . "/../../models/Leave.php";
require_once __DIR__ . "/../../repository/LeaveRepository.php";
require_once __DIR__ . "/../../controllers/LeaveController.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$uploadProof = new FileManager();
$leave = new Leave();
$leaveRepository = new LeaveRepository($dbh, $leave);
$leaveController = new LeaveController($leaveRepository);

$ManagerSession = new ManagerSession();
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

if(isset($rol_id)) {
    $did = intval($_GET['leaveid']);
    
    $leave = $leaveController->getLeaveById($did);
    $Proofs = $uploadProof->getProofs($leave->EmpId, $did);
    $existdisability = isset($Proofs['files']) ? existFileDisability($Proofs['files'], 'incapacidad') : false;
    $isIncapacityFileUploadEnabled = $leave->Status !== 5 && ($leave->LeaveTypeID === 2 || $leave->LeaveTypeID === 4) && $existdisability === false;

    if (!empty($leave) && $leave->IsRead === 1) {
        $isRead = $leaveController->updateLeave($did, ['IsRead' => 0]);
    }

    date_default_timezone_set('Asia/Kolkata');
    $admremarkdate=date('Y-m-d G:i:s ', strtotime("now"));
    $AdminRemarkDate = date("d-m-Y H:i:s", strtotime($leave->AdminRemarkDate));

    if (!empty($_FILES['images'])) { 
        $uploadResults = $uploadProof->saveImages($leave->EmpId, $did, $_FILES['images']);
        header('Location:leave-details.php?leaveid=' . $did);
    }

    if (!empty($_FILES['incapacidad'])) {
        $uploadResults = $uploadProof->saveImages($leave->EmpId, $did, $_FILES['incapacidad'], 'incapacidad');
        header('Location:leave-details.php?leaveid=' . $did);
    }

    $leavetime = calcularTiempoPermiso($leave->FromDate, $leave->ToDate);

    include('../../components/header.php');
    include('../../components/sidebar.php');
   
?>
    <main class="app-main">
        <div class="row justify-content-center">
            <div class="page-header">
                <h1>Solicitar Permiso o Licencia</h1>
                <nav class="breadcrumb">
                    <a href="/dashboard.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="leavehistory.php">Permisos</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Detalles solicitud</span>
                </nav>
            </div>
            <div class="col-lg-10">
                <div class="card card-detail mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h1 class="card-title">Detalles de la Solicitud de Permiso</h1>
                        <?php if ($leave->Status == 3): ?>
                            <a href="apply-leave.php?id_leave=<?= $did ?>" class="btn btn-light">
                                <i class="fas fa-edit me-2"></i>Editar Solicitud
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($msg)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Éxito!</strong> <?php echo htmlentities($msg); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Tipo de Permiso</div>
                                    <div class="detail-value"><?php echo htmlentities($leave->LeaveType); ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Fecha de Inicio</div>
                                    <div class="detail-value"><?php echo htmlentities(formatDate($leave->FromDate)); ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Fecha de Fin</div>
                                    <div class="detail-value"><?php echo htmlentities(formatDate($leave->ToDate)); ?></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Fecha de Solicitud</div>
                                    <div class="detail-value"><?php echo htmlentities(formatDate($leave->PostingDate)); ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Duración</div>
                                    <div class="detail-value"><?php echo htmlentities($leavetime); ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Estado</div>
                                    <div class="detail-value">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value observations-text"><?php echo htmlentities($leave->Description); ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Observaciones del Líder</div>
                            <div class="detail-value observations-text">
                                <?php echo ($leave->AdminRemark === "") ? '<span class="text-muted">Pendiente de aprobación</span>' : htmlentities($leave->AdminRemark); ?>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Fecha de Revisión</div>
                            <div class="detail-value">
                                <?php echo ($leave->AdminRemarkDate == "") ? 'NA' : htmlentities(date("d/m/Y h:i A", strtotime($leave->AdminRemarkDate))); ?>
                            </div>
                        </div>
                        
                        <!-- Comprobantes -->
                        <h3 class="section-title">Comprobantes Adjuntos</h3>
                        <div class="detail-item">
                            <div class="document-container">
                                <?php
                                if (isset($Proofs['files']) && !empty($Proofs['files'])) {
                                    foreach($Proofs['files'] as $proof) {
                                        if (!strpos($proof, 'incapacidad')) {
                                            $extension = obtenerExtension($proof);
                                            if ($extension === 'pdf') {
                                                echo '<a href="../../'.$proof.'" target="_blank" class="document-item document-pdf">';
                                                echo '<i class="fas fa-file-pdf"></i>';
                                                echo '</a>';
                                            } else {
                                                echo '<a href="../../'.$proof.'" target="_blank" class="document-item">';
                                                echo '<img src="../../'.$proof.'" alt="Comprobante" class="document-img">';
                                                echo '</a>';
                                            }
                                        }
                                    }
                                } else {
                                    echo '<div class="text-muted">No hay comprobantes adjuntos</div>';
                                }
                                ?>
                            </div>
                            
                            <!-- Formulario para subir comprobantes -->
                            <form action="leave-details.php?leaveid=<?= $did ?>" method="post" enctype="multipart/form-data" class="mt-4">
                                <label for="fileInput" class="upload-area">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div>Arrastra archivos aquí o haz clic para seleccionar</div>
                                    <div class="small text-muted mt-2">Formatos aceptados: PDF, JPG, PNG</div>
                                    <input type="file" name="images[]" id="fileInput" class="file-input" multiple accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <div id="fileNames" class="file-name"></div>
                                <button type="submit" class="btn btn-upload mt-3" id="submitButton" style="display: none;">
                                    <i class="fas fa-upload me-2"></i>Subir Comprobantes
                                </button>
                            </form>
                        </div>
                        
                        <!-- Incapacidad (si aplica) -->
                        <?php if ($leave->LeaveTypeID === 2 || $leave->LeaveTypeID === 4): ?>
                        <h3 class="section-title">Documentos de Incapacidad</h3>
                        <div class="detail-item">
                            <div class="document-container">
                                <?php
                                if (isset($Proofs['files']) && !empty($Proofs['files']) && $existdisability) {
                                    foreach($Proofs['files'] as $proof) {
                                        if (strpos($proof, 'incapacidad')) {
                                            $extension = obtenerExtension($proof);
                                            if ($extension === 'pdf') {
                                                echo '<a href="../../'.$proof.'" target="_blank" class="document-item document-pdf">';
                                                echo '<i class="fas fa-file-pdf"></i>';
                                                echo '</a>';
                                            } else {
                                                echo '<a href="../../'.$proof.'" target="_blank" class="document-item">';
                                                echo '<img src="../../'.$proof.'" alt="Incapacidad" class="document-img">';
                                                echo '</a>';
                                            }
                                        }
                                    }
                                } else {
                                    echo '<div class="text-muted">No hay documentos de incapacidad adjuntos</div>';
                                }
                                ?>
                            </div>
                            
                            <?php if($isIncapacityFileUploadEnabled): ?>
                            <!-- Formulario para subir incapacidad -->
                            <form action="leave-details.php?leaveid=<?= $did ?>" method="post" enctype="multipart/form-data" class="mt-4">
                                <label for="fileInput2" class="upload-area">
                                    <div class="upload-icon">
                                        <i class="fas fa-file-medical"></i>
                                    </div>
                                    <div>Subir documento de incapacidad</div>
                                    <div class="small text-muted mt-2">Formatos aceptados: PDF, JPG, PNG</div>
                                    <input type="file" name="incapacidad[]" id="fileInput2" class="file-input" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <div id="fileNames2" class="file-name"></div>
                                <button type="submit" class="btn btn-upload mt-3" id="submitButton2" style="display: none;">
                                    <i class="fas fa-upload me-2"></i>Subir Incapacidad
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/js/main/main.js"></script>
    <script src="../../assets/js/files/files-upload.js"></script>
    
<?php } else{
            echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
    }
?>
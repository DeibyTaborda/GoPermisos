<?php
session_start();
error_reporting(0);
include('../../includes/config.php');
include('../../utils/utils.php');
require_once __DIR__ . "/../../utils/alerts.php";
require_once __DIR__ . "/../../files/FileManager.php";
require_once __DIR__ . "/../../repository/LeaveRepository.php";
require_once __DIR__ . "/../../controllers/LeaveController.php";
require_once __DIR__ . "/../../models/Leave.php";
require_once __DIR__ . "/../../models/Mailer.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$leave = new Leave();
$ManagerSession = new ManagerSession();
$uploadProof = new FileManager();
$repositoryLeave = new LeaveRepository($dbh, $leave);
$controllerLeave = new LeaveController($repositoryLeave);
$Mailer = new Mailer();

$rol_id  = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

if($rol_id === 2 || $rol_id === 3 || $super_admin) {
    $did = intval($_GET['leaveid']);
    
    if (isset($_POST['updateToDate'])) {
        $request = $controllerLeave->updateLeave($did, ['EditToDate' => 1, 'ToDate' => $_POST['ToDate']]);

        if (isset($request['message'])) {
            showToast('success', $request['message']);
        } else if (isset($request['error'])) {
            showToast('error', $request['error']);
        }
    }

    if(isset($_POST['update'])) {
        date_default_timezone_set('America/Bogota');
        $admremarkdate = date('Y-m-d H:i:s');
        $data = [
            'AdminRemarkDate' => $admremarkdate,
            'Status' => $_POST['Status'],
            'AdminRemark' => $_POST['AdminRemark']
        ];

        $update = $controllerLeave->updateLeave($did, $data);
        $leaveUpdated = $controllerLeave->getLeaveById($did);
        
        $datos = [
            'FirstName' => $leaveUpdated->FirstName,
            'LastName' => $leaveUpdated->LastName,
            'EmpId' => $leaveUpdated->EmpId,
            'FromDate' => date('d/m/Y h:i A', strtotime($leaveUpdated->FromDate)),
            'ToDate' => date('d/m/Y h:i A', strtotime($leaveUpdated->ToDate)),
            'AdminRemark' => $leaveUpdated->AdminRemark ?? 'No se han proporcionado comentarios adicionales.',
            'LeaveType' => $leaveUpdated->LeaveType,
            'Description' => $leaveUpdated->Description,
            'DepartmentName' => $leaveUpdated->DepartmentName,
            'PostingDate' => date('d/m/Y h:i A', strtotime($leaveUpdated->PostingDate)),
            'Status' => $leaveUpdated->Status === 4 ? 'Aprobado' : 'No aprobado',
            'nombre_empresa' => $_SERVER['PROJECT_NAME'] === 'GoPermisos' ? 'PORTADA INMOBILIARIA' : 'PORTADA INVERSIONES',
            'color_estado' => $leaveUpdated->Status === 4 ? '#2cd560' : '#fc4a4a'
        ];

        $imagen = "../../logo.png";
        $Mailer->enviarCorreo($leaveUpdated->EmailId, 'Respuesta permiso', '../../plantillas/respuesta_solicitud', $datos, $imagen);

        if ($update['message']) {
            $msg = $update['message'];
        }
    }

    $leave = $controllerLeave->getLeaveById($did);
    $fechaFormateada = date('Y-m-d h:i:s A', strtotime($leave->ToDate));
    $Proofs = $uploadProof->getProofs($leave->EmpId, $did);
    $existdisability = isset($Proofs['files']) ? existFileDisability($Proofs['files'], 'incapacidad'): false;
    $AdminRemarkDate = date("d-m-Y h:i:s A", strtotime($leave->AdminRemarkDate));
    $leavetime = calcularTiempoPermiso($leave->FromDate, $leave->ToDate);

    if (!$super_admin) {
        $controllerLeave->updateLeave($did, ['IsRead' => 1]);
    }
?>

<body>
    <?php include('../../components/header.php');?>
    <?php include('../../components/sidebar.php');?>
    
    <main class="app-main">
        <div class="row justify-content-center">
            <div class="page-header">
                <h1>Detalles de Solicitud de Permiso</h1>
                <nav class="breadcrumb">
                    <a href="/dashboard.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="leaves.php">Solicitudes</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Detalles</span>
                </nav>
            </div>
            <div class="col-lg-10">
                <div class="card card-detail mb-4">
                    <div class="card-header">
                        <h1 class="card-title">Solicitud #<?= htmlentities($leave->id) ?></h1>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($msg)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <strong>Éxito!</strong> <?= htmlentities($msg) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Empleado</div>
                                    <div class="detail-value">
                                        <?php 
                                            $employeeName = htmlentities($leave->FirstName . " " . $leave->LastName);
                                            if ($super_admin) {
                                                echo '<a href="../users/formuser.php?action=edit&id_user=' . htmlentities($leave->empid) . '" target="_blank">' . $employeeName . '</a>';
                                            } else {
                                                echo $employeeName;
                                            }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">ID Empleado</div>
                                    <div class="detail-value"><?= htmlentities($leave->EmpId) ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Departamento</div>
                                    <div class="detail-value"><?= htmlentities($leave->DepartmentName) ?></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Correo</div>
                                    <div class="detail-value"><?= htmlentities($leave->EmailId) ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Teléfono</div>
                                    <div class="detail-value"><?= htmlentities($leave->Phonenumber) ?></div>
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
                                                    echo '<span class="status-badge status-voided"><i class="fas fa-ban me-1"></i> Anulado</span>';
                                                    break;
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Tipo de Permiso</div>
                                    <div class="detail-value"><?= htmlentities($leave->LeaveType) ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Fecha de Inicio</div>
                                    <div class="detail-value"><?= htmlentities(formatDate($leave->FromDate)) ?></div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Fecha de Solicitud</div>
                                    <div class="detail-value"><?= htmlentities(formatDate($leave->PostingDate)) ?></div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Fecha de Fin</div>
                                    <div class="detail-value"><?= htmlentities(formatDate($leave->ToDate)) ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Duración</div>
                            <div class="detail-value"><?= htmlentities($leavetime) ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value observations-text"><?= htmlentities($leave->Description) ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Observaciones</div>
                            <div class="detail-value observations-text"><?= $leave->AdminRemark === "" ? '<span class="text-muted">Pendiente de aprobación</span>' : htmlentities($leave->AdminRemark) ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">Fecha de Decisión</div>
                            <div class="detail-value">
                                <?= $leave->AdminRemarkDate=="" ? '<span class="text-muted">NA</span>' : htmlentities(date("d/m/Y h:i A", strtotime($leave->AdminRemarkDate))) ?>
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
                        </div>
                        <?php endif; ?>
                        
                        <!-- Acciones para líder/director -->
                        <div class="d-flex justify-content-end mt-4 gap-3">
                            <?php if($leave->Status == 3): ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#decisionModal">
                                    <i class="fas fa-gavel me-2"></i>Tomar Decisión
                                </button>
                            <?php elseif ($leave->Status === 4): ?>
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editDateModal">
                                    <i class="fas fa-edit me-2"></i>Editar Fecha
                                </button>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#annulModal">
                                    <i class="fas fa-times-circle me-2"></i>Anular Solicitud
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal de Decisión -->
        <div class="modal fade" id="decisionModal" tabindex="-1" aria-labelledby="decisionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="decisionModalLabel">Tomar Decisión</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form name="adminaction" method="post">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="statusSelect" class="form-label">Decisión</label>
                                <select class="form-select" id="statusSelect" name="Status" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="4">Aprobar</option>
                                    <option value="5">Rechazar</option>
                                    <option value="6">Cancelar</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="adminRemark" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="adminRemark" name="AdminRemark" rows="4" maxlength="500" required></textarea>
                                <div class="form-text">Máximo 500 caracteres</div>
                            </div>
                            <input type="hidden" name="update">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Decisión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para Editar Fecha de Entrada -->
        <div class="modal fade" id="editDateModal" tabindex="-1" aria-labelledby="editDateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDateModalLabel">Editar Fecha de Regreso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form name="editToDateForm" method="post">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Salida</label>
                                    <input type="text" class="form-control" value="<?= htmlentities(formatDate($leave->FromDate)) ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="newToDate" class="form-label">Nueva Fecha de Regreso</label>
                                    <input id="newToDate" type="text" class="form-control" name="ToDate" value="<?= htmlentities(formatDate($leave->ToDate)) ?>">
                                </div>
                                <input type="hidden" name="updateToDate">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="updateToDate" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal para Anular Solicitud -->
        <div class="modal fade" id="annulModal" tabindex="-1" aria-labelledby="annulModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="annulModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>Anular Permiso Aprobado
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form name="annulForm" method="post">
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <div class="d-flex">
                                    <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                                    <div>
                                        <p class="mb-0">Esta anulación será notificada al empleado y registrada en el sistema.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="annulReason" class="form-label fw-bold text-danger">
                                    <i class="fas fa-comment-dots me-2"></i>Motivo de anulación *
                                </label>
                                <textarea class="form-control border-danger" id="annulReason" name="AdminRemark" rows="3" 
                                        required placeholder="Ejemplo: 'El empleado comunicó que ya no requiere el permiso solicitado'"
                                        style="border-width: 2px;"></textarea>
                            </div>
                            <input type="hidden" name="Status" value="7">
                            <input type="hidden" name="update">
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-danger" name="update">
                                <i class="fas fa-ban me-2"></i>Confirmar Anulación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="../../assets/js/main/main.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

            flatpickr("#newToDate", {
        enableTime: true,
        dateFormat: "d/m/Y h:i K",
        minDate: "today",
        locale: "es",
        time_24hr: false,
        minuteIncrement: 15,
        static: true,  // Muestra el calendario en modo estático
        onReady: function() {
            // Foco automático al abrir el modal
            document.getElementById('newToDate').focus();
        }
    });
    </script>
</body>
<?php
} else {
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
}
?>
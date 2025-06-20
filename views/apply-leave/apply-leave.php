<?php
session_start();
date_default_timezone_set('America/Bogota');
// error_reporting(0);
require_once __DIR__ . '/../../utils/alerts.php';
include_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . "/../../files/FileManager.php";
require_once __DIR__ . "/../../models/Mailer.php";
require_once __DIR__ . "/../../models/Leave.php";
require_once __DIR__ . "/../../models/LeaveType.php";
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../repository/LeaveTypeRepository.php"; 
require_once __DIR__ . "/../../repository/LeaveRepository.php";
require_once __DIR__ . "/../../controllers/LeaveController.php";
require_once __DIR__ . "/../../repository/UserRepository.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$uploadProof = new FileManager();
$leaveType = new LeaveType();
$leave = new Leave();
$user = new User();
$leaveTypeRepository = new LeaveTypeRepository($dbh, $leaveType);
$leaveRepository = new LeaveRepository($dbh, $leave);
$controllerLeave = new LeaveController($leaveRepository);
$userRepository = new UserRepository($dbh, $user);
$Mailer = new Mailer();

$ManagerSession = new ManagerSession();
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

if(isset($rol_id)) {
    $leavesTypes = $leaveTypeRepository->getLeaveTypes();
    $id_leave = $_GET['id_leave'] ?? null;
    $action = !empty($id_leave) ? 'edit' : 'add';

    if(isset($_POST['action'])) {
        $currentUser = $userRepository->getByIdUser($user_id);
        $document = $currentUser->EmpId;

        $data = [
            'FromDate' => $_POST['FromDate'],
            'ToDate' => $_POST['ToDate'],
            'LeaveTypeID' => $_POST['LeaveTypeID'],
            'Description' => $_POST['Description'],
            'empid' => $currentUser->id,
            'Status' => 3,
            'PostingDate' => date('Y-m-d H:i:s')
        ];

        if ($action === 'edit') {
            $result = $controllerLeave->updateLeave($id_leave, $data);
            $result['edit'] = true;
        } else if ($action === 'add') {
            $result = $controllerLeave->saveLeave($data);
            $result['edit'] = false;
        }

        $id_new_leave = $result['leaveId'] ?? null;

        if (isset($id_new_leave) && !empty($id_new_leave)) {
            $new_leave = $leaveRepository->getLeaveById($id_new_leave);
            $user_leave = $userRepository->getByIdUser($user_id);

            $data_email = [
                'nombre_gestionador' => '',
                'FirstName' => $user_leave->FirstName,
                'LastName' => $user_leave->LastName,
                'EmpId' => $user_leave->EmpId,
                'DepartmentName' => $new_leave->DepartmentName,
                'Sede' => $user_leave->sede,
                'PostingDate' => $new_leave->PostingDate,
                'LeaveType' => $new_leave->LeaveType,
                'FromDate' => $new_leave->FromDate,
                'ToDate' => $new_leave->ToDate,
                'Description' => $new_leave->Description,
                'color_estado' => '#ffc107',
                'Status' => 'Pendiente',
                'nombre_empresa' => $_SERVER['PROJECT_NAME'] === 'GoPermisos' ? 'PORTADA INMOBILIARIA' : 'PORTADA INVERSIONES',
                'link_a_la_aplicación' => "https://crinmo.portadainmobiliaria.app/{$_SERVER['PROJECT_NAME']}/views/leaves/leave-details.php?leaveid=$id_new_leave"
            ];

            $logo = '../../logo.png';

            if($rol_id === 3) {
                $data_email['DepartmentName'] = '';
                $data_email['nombre_gestionador'] = 'GESTIÓN HUMANA';

                $Mailer->enviarCorreo($_SERVER['EMAIL_GESTION_HUMANA'], 'Solicitud de Permiso', '../../plantillas/solicitud_permiso', $data_email, $logo);
            } else {
                $permissionManagersEmails = $userRepository->getPermissionManagersEmails($new_leave->department_id, $user_leave->SedeID, $rol_id);

                foreach($permissionManagersEmails as $email) {
                    $data_email['nombre_gestionador'] = $email['FirstName'];
                    $Mailer->enviarCorreo($email['EmailID'], 'Solicitud de Permiso', '../../plantillas/solicitud_permiso', $data_email, $logo);
                }
            }
        }

        $files = $_FILES['images'];

        if (!empty($files['name'][0])) { 
            $upload = $uploadProof->saveImages($document, $result['leaveId'], $files);
        }
    }

    if (!empty($id_leave)) {
        $selectedLeave = $leaveRepository->getLeaveById($id_leave);
    }

        include('../../components/header.php');
        include('../../components/sidebar.php');

        if (!empty($result['message'])) {
            showSweetAlert('success', $result['message'], 'leavehistory.php', $result['edit']);
        } elseif (!empty($result['error'])) {
            showSweetAlert('error', $result['error']);
        }  
    ?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Solicitar Permiso o Licencia</h1>
                <nav class="breadcrumb">
                    <a href="leavehistory.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Solicitud</span>
                </nav>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Información de la Solicitud</h2>
                    <p class="form-subtitle">Complete todos los campos requeridos</p>
                </div>

                <form method="post" id="form-apply-leave" enctype="multipart/form-data" class="modern-form">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="LeaveTypeID">Tipo de Permiso/Licencia</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-alt"></i>
                                <select id="LeaveTypeID" name="LeaveTypeID" class="form-control" required>
                                    <option value="">Selecciona un tipo de Permiso o Licencia</option>
                                    <?php
                                        if($leavesTypes) {
                                        foreach($leavesTypes as $result) {
                                            if ($result->Status === 1) {
                                                $selected = (!empty($selectedLeave) && $selectedLeave->LeaveTypeID == $result->id) ? 'selected' : '';
                                    ?>                                            
                                    <option value="<?php echo htmlentities($result->id); ?>" <?php echo $selected; ?>>
                                        <?php echo htmlentities($result->LeaveType); ?>
                                    </option>
                                    <?php }}} ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="FromDate">Fecha de inicio</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-day"></i>
                                <input id="FromDate" name="FromDate" class="form-control" type="text" required
                                value="<?php echo $selectedLeave->FromDate ?? '' ?>">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="ToDate">Fecha de fin</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-day"></i>
                                <input id="ToDate" name="ToDate" class="form-control" type="text" required
                                value="<?php echo $selectedLeave->ToDate ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="Description">Descripción</label>
                            <textarea id="Description" name="Description" class="form-control" maxlength="500" required><?php echo $selectedLeave->Description ?? '' ?></textarea>
                            <div class="char-counter"><span id="charCount">0</span>/500 caracteres</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Comprobantes (Opcional)</label>
                            <div class="file-upload">
                                <button type="button" class="file-upload-btn">
                                    <i class="fas fa-paperclip"></i> Seleccionar Archivos
                                </button>
                                <input type="file" name="images[]" id="fileInput" class="file-upload-input" multiple accept="image/*">
                                <div class="file-upload-label">Se permiten múltiples archivos (máx. 5MB cada uno)</div>
                            </div>
                            <div class="preview-container" id="preview"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <input type="hidden" name="action" value="<?= $action ?>">
                        <a href="leavehistory.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" name="apply" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i><?php echo $action === 'edit' ? 'Editar' : 'Enviar' ?> Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    
    <!-- SweetAlert2 -->


    <!-- Custom JS -->
    <script src="../../assets/js/main/main.js"></script>
    <script src="../../assets/js/main/preview-file.js"></script>

    <script>
        $(document).ready(function() {
            const textarea = $('#Description');
            const charCount = $('#charCount');
            
            charCount.text(textarea.val().length);
            
            textarea.on('input', function() {
                charCount.text($(this).val().length);
            });
        });
    </script>
<?php } else {
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
} ?>
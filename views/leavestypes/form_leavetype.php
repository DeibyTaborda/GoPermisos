<?php
session_start();
error_reporting(0);
include('../../admin/includes/config.php');
require_once __DIR__ . "/../../utils/alerts.php";
require_once __DIR__ .'/../../models/LeaveType.php';
require_once __DIR__ . '/../../Repository/LeaveTypeRepository.php';
require_once __DIR__ . '/../../controllers/LeaveTypeController.php';
require_once __DIR__ . '/../../models/ManagerSession.php';

$leavetype = new LeaveType();
$leaveTypeRepository = new LeaveTypeRepository($dbh, $leavetype);
$leaveTypeController = new LeaveTypeController($leaveTypeRepository);

$ManagerSession = new ManagerSession();
$super_admin = $ManagerSession->getSession('super_admin');

$id_leavetype = $_GET['id_leavetype'] ?? null;

if ($super_admin) {   
    if(isset($_POST['action'])) {
        $data = [
            'LeaveType' => $_POST['LeaveType'],
            'Description' => $_POST['Description']
        ];

        if ($_POST['action'] === 'edit') {
            $request = $leaveTypeController->updateLeaveType($id_leavetype, $data);
        } else if ($_POST['action'] === 'add') {
            $request = $leaveTypeController->saveLeaveType($data);
        }

        if (isset($request['success']) && $request['success']) {
            showToast('success', $request['message']);
        } else if (isset($request['type']) && $request['type'] === 'info') {
            showToast('info', $request['message']);
        } else if (isset($request) && !$request['success']) {
            showToast('error', $request['message']);
        }
    }

    if (!empty($id_leavetype)) {
        $selectedLeaveType = $leaveTypeController->getByIdLeaveType($id_leavetype);
    }
?>
    <?php include('./../../components/header.php'); ?>
    <?php include('./../../components/sidebar.php'); ?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1><?php echo isset($selectedLeaveType) ? 'Editar' : 'Agregar'; ?> Tipo de Licencia</h1>
                <nav class="breadcrumb">
                    <a href="../leaves/leaves.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="manageleavetype.php">Tipos de Licencia</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span><?php echo isset($selectedLeaveType) ? 'Editar' : 'Agregar'; ?></span>
                </nav>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Información del Tipo de Licencia</h2>
                    <p class="form-subtitle">Complete todos los campos requeridos</p>
                </div>

                <form method="post" id="form-leavetype" class="modern-form">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="LeaveType">Tipo de Licencia</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-alt"></i>
                                <input 
                                    type="text" 
                                    id="LeaveType" 
                                    name="LeaveType" 
                                    class="form-control" 
                                    value="<?php echo isset($selectedLeaveType) ? htmlentities($selectedLeaveType[0]->LeaveType) : ''; ?>" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="Description">Descripción</label>
                            <textarea 
                                id="Description" 
                                name="Description" 
                                class="form-control" 
                                maxlength="100"
                            ><?php echo isset($selectedLeaveType) ? htmlentities($selectedLeaveType[0]->Description) : ''; ?></textarea>
                            <div class="char-counter"><span id="charCount">0</span>/100 caracteres</div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <input type="hidden" name="action" value="<?php echo isset($selectedLeaveType) ? 'edit' : 'add' ?>">
                        <button type="submit" name="add" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <?php echo isset($selectedLeaveType) ? 'ACTUALIZAR TIPO' : 'AGREGAR TIPO'; ?>
                        </button>
                        <a href="manageleavetype.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="../../assets/js/main/form.js"></script>
    <script src="../../assets/js/main/main.js"></script>
    <script>
        $(document).ready(function() {
            // Contador de caracteres para la descripción
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
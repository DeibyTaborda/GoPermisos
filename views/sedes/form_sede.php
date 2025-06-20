<?php 
session_start();
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../utils/alerts.php";
require_once __DIR__ . "/../../models/ManagerSession.php";
require_once __DIR__ . "/../../models/Sede.php";
require_once __DIR__ . "/../../Repository/SedeRepository.php";
require_once __DIR__ . "/../../controllers/SedeController.php";
require_once __DIR__ . "/../../models/Department.php";
require_once __DIR__ . "/../../models/DepartmentsSede.php";
require_once __DIR__ . "/../../Repository/DepartmentRepository.php";
require_once __DIR__ . "/../../Repository/DepartmentsSedeRepo.php";
$ManagerSession = new ManagerSession();

$sede = new Sede();
$departmentsSede = new DepartmentsSede();
$sedeRepository = new SedeRepository($dbh, $sede);
$departmentsSedeRepo = new DepartmentsSedeRepo($dbh, $departmentsSede);
$sedeController = new SedeController($sedeRepository, $departmentsSedeRepo);

$department = new Department();
$departmentRepository = new DepartmentRepository($dbh, $department);

$departments = $departmentRepository->get();
$id_sede = $_GET['id_sede'] ?? '';
$linkedDepartmentIds = [];

if (isset($_POST['form_action'])) {

    $data = [
        'sede' => $_POST['sede'] ?? '',
        'departments' =>  $_POST['departments'] ?? []
    ];

    if ($_POST['form_action'] === 'save') {
        $request = $sedeController->save(['sede' => $_POST['sede']] ,$_POST['departments'] ?? []);
    } else if ($_POST['form_action'] === 'edit') {
        $request = $sedeController->update($id_sede, $data);
    }

    if (isset($request['success']) && $request['success']) {
        showToast('success',  $request['message']);
    } else if(isset($request['type']) && $request['type'] === 'info') {
        showToast('info',  $request['message']);
    } else if (isset($request['success']) && !$request['success']) {
        showToast('error',  $request['message']);
    }
}

if (!empty($id_sede)) {
    $sedeToEdit = $sedeRepository->getById($id_sede);
    $linkedDepartmentIds = $departmentsSedeRepo->getDepartmentsIdBySede($id_sede);
}
?>

<body>
    <?php include(__DIR__ . "/../../components/header.php"); ?>
    <?php include(__DIR__ . "/../../components/sidebar.php"); ?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Asignar Departamentos a Sede</h1>
                <nav class="breadcrumb">
                    <a href="/dashboard.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <a href="../sedes/sedes.php">Sedes</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Asignar Departamentos</span>
                </nav>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h2 class="form-title">Selección de Departamentos</h2>
                </div>
                <form method="post" id="form-sede" class="modern-form">
                    <div class="form-group">
                        <label for="sede">Sede</label>
                        <div class="input-with-icon">
                            <i class="fas fa-building"></i>
                            <input type="text" class="form-control" name="sede" value="<?php echo isset($sedeToEdit) ? $sedeToEdit[0]->sede : '' ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Departamentos Disponibles</label>
                        <div class="departments-grid">
                            <?php foreach($departments as $department): ?>
                                <?php $checked = in_array($department->id, $linkedDepartmentIds) ? 'checked' : '' ?>
                            <div class="department-checkbox">
                                <input type="checkbox" id="dep-<?php echo $department->id ?>" 
                                       name="departments[]" value="<?php echo $department->id ?>" <?= $checked ?>>
                                <label for="dep-<?php echo $department->id ?>">
                                    <?php echo htmlentities($department->DepartmentName) ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div id="mensaje-error" style="color: red; display: none; margin-bottom: 10px;">
                    <!-- Aquí aparecerá el mensaje de validación -->
                    </div>
                    <div class="form-actions">
                        <input type="hidden" name="form_action" value="<?php echo empty($id_sede) ? 'save' : 'edit' ?>">
                        <button type="submit" name="save" class="btn btn-primary btn-submit">
                            <i class="fas fa-save"></i> GUARDAR ASIGNACIÓN
                        </button>
                        <a href="../sedes/sedes.php" class="btn btn-secondary">
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
    <script src="../../assets/js/main/main.js"></script>
    <script>
        const formSede = document.getElementById('form-sede');
        const checkboxesDepartmentsSede = document.querySelectorAll('input[type=checkbox');
        const errorMenssage = document.getElementById('mensaje-error');

        formSede.addEventListener('submit', (e) => {
            e.preventDefault();
            let count = [...checkboxesDepartmentsSede].filter(checkbox => checkbox.checked).length;

            if (count === 0) {
                errorMenssage.textContent = "Selecciona por lo menos un departamento";
                errorMenssage.style.display = 'block';
                return;
            }

             formSede.submit();
        })
    </script>
</body>
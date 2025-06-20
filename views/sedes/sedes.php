<?php
session_start();
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../models/Sede.php";
require_once __DIR__ . "/../../Repository/SedeRepository.php";
require_once __DIR__ . "/../../models/ManagerSession.php";
$ManagerSession = new ManagerSession();

$sede = new Sede();
$sedeRepository = new SedeRepository($dbh, $sede);

if (isset($_GET['inact'])) {
    $sedeRepository->update($_GET['inact'], ['status_id' => 2]);
    header('location:sedes.php');
    exit;
}

if (isset($_GET['act'])) {
    $sedeRepository->update($_GET['act'], ['status_id' => 1]);
    header('location:sedes.php');
    exit;
}

$sedes = $sedeRepository->getSedesWithDepartments();

require_once __DIR__ . "/../../components/header.php";
require_once __DIR__ . "/../../components/sidebar.php";
?>

<body>
    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Administrar Sedes</h1>
                <nav class="breadcrumb">
                    <a href="../leaves/leaves.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Sedes</span>
                </nav>
            </div>

            <div class="data-table-container">
                <div class="table-header">
                    <h2 class="table-title">Listado de Sedes</h2>
                    <a href="form_sede.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Sede
                    </a>
                </div>

                <table id="sedeTable" class="department-table table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sede</th>
                            <th>Departamentos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sedes as $sede): 
                            $departamentos = explode(', ', $sede->departamentos);
                        ?>
                        <tr>
                            <td><?= htmlentities($sede->id) ?></td>
                            <td><?= htmlentities($sede->sede) ?></td>
                            <td>
                                <?php foreach($departamentos as $departamento): ?>
                                    <span class="department-badge"><?= htmlentities($departamento) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <span class="status-badge <?= $sede->status_id == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $sede->status_id == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($sede->status_id == 1): ?>
                                    <a href="sedes.php?inact=<?= $sede->id ?>" 
                                       onclick="return confirm('¿Deshabilitar esta sede?')"
                                       class="action-btn btn-disable" title="Deshabilitar">
                                        <i class="fas fa-toggle-on"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="sedes.php?act=<?= $sede->id ?>" 
                                       onclick="return confirm('¿Activar esta sede?')"
                                       class="action-btn btn-enable" title="Activar">
                                        <i class="fas fa-toggle-off"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="form_sede.php?id_sede=<?= $sede->id ?>" 
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

     <script src="../../assets/js/main/main.js"></script>
    <script src="../../assets/js/main/table-config.js"></script>
</body>
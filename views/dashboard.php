
<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../models/ManagerSession.php";
require_once __DIR__ . "/../generic/RepositoryGeneric.php";
require_once __DIR__ . "/../models/Sede.php";

session_start();
$ManagerSession = new ManagerSession();
$super_admin = $ManagerSession->getSession('super_admin');

$sede = new Sede();
$sedeRepositoryGeneric = new RepositoryGeneric($dbh, $sede);

$sedes = $sedeRepositoryGeneric->get();
// var_dump($sedes);
// die();

if (!$super_admin) {
    header("Location: ../index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Permisos</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/pages/dashboard.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-primary text-white p-0">
                <div class="position-sticky pt-3">
                    <div class="text-center py-4">
                        <h4 class="text-white">Sistema de Permisos</h4>
                    </div>
                    <ul class="nav flex-column">
                        <li>
                            <button class="btn btn-category active" onclick="showCategory('general-graficas')">
                                <i class="fas fa-fw fa-tachometer-alt"></i> General
                            </button>
                        </li>
                        <li>
                            <button class="btn btn-category" onclick="showCategory('top-empleados')">
                                <i class="fas fa-fw fa-users"></i> Empleados
                            </button>
                        </li>
                        <li class="mt-4 px-3">
                            <a href="../views/leaves/leaves.php" class="btn btn-outline-light w-100">
                                <i class="fas fa-arrow-left me-2"></i> Volver
                            </a>
                        </li>

                        <!-- <li class="nav-item">
                            <a class="nav-link" href="departments/managedepartments.php">
                                <i class="fas fa-fw fa-building"></i>
                                Departamentos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="departments/managedepartments.php">
                                <i class="fas fa-fw fa-file-alt"></i>
                                Tipo de permisos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="leaves/leaves.php">
                                <i class="fas fa-fw fa-calendar-alt"></i>
                                Solicitudes
                            </a>
                        </li> -->
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>

                    <h1 class="h2">Dashboard de Permisos</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-filter me-1"></i>
                        Filtros
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="yearFilter" class="form-label">Año</label>
                                <select class="form-select" id="yearFilter">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="monthFilter" class="form-label">Mes</label>
                                <select class="form-select" id="monthFilter">
                                    <option value="0">Todos</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dayFilter" class="form-label">Día</label>
                                <select class="form-select" id="dayFilter">
                                    <option value="0">Todos</option>
                                    <!-- Los días se llenarán dinámicamente -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dayFilter" class="form-label">Sede</label>
                                <select class="form-select" id="sedeFilter">
                                    <option value="0">Todas</option>
                                    <?php if(isset($sedes) && !empty($sedes)): ?>
                                            <?php foreach($sedes as $sede): ?>
                                                <?php if($sede->status_id === 1): ?>
                                                    <option value="<?= $sede->id ?>"><?= $sede->sede?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                    <?php endif ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="dayFilter" class="form-label">Departamento</label>
                                <select class="form-select" id="departmentFilter">
                                    <option value="0">Todos</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100" id="applyFilters">
                                    <i class="fas fa-filter me-1"></i> Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graficas generales -->
                <div id="general-graficas" class='content-category active'>
                     <!-- Estadísticas Resumen -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-primary h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Solicitudes
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSolicitudes">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-success h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Días
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDias">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Solicitudes por Estado
                                </div>
                                <div class="card-body">
                                    <div class="row" id="estadosSolicitudes">
                                        <!-- Se llenará dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráficas Principales -->
                    <div class="row mb-4" id="main-charts-container">
                        <div class="col-lg-6 mb-4" id="apps-by-dept">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Solicitudes por Departamento
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="chartSolicitudesDepartamento"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Solicitudes por Estado
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="chartSolicitudesEstado"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Departamentos -->
                    <div class="row mb-4" id="container-top">
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-trophy me-1"></i>
                                    Top 5 Departamentos con Más Permisos
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="chartTopDepartamentosPermisos"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-trophy me-1"></i>
                                    Top 5 Departamentos con Más Días Acumulados
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="chartTopDepartamentosDias"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Penultima Fila de Gráficas -->
                    <div class="row" id="penultima-fila">
                        <div class="col-lg-6 mb-4" id="total-requests-by-type">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Tipología de Permisos
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="chartTipologiaPermisos"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-pie me-1"></i>
                                    Distribución de Permisos por Rol
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="chartDistribucionRolPermisos"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficas Secundarias -->
                    <div class="row mb-4" id="secondary-charts">
                        <div class="col-lg-6 mb-4" id="avg-days-dept">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Promedio de Días por Departamento
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="chartPromedioDiasDepartamento"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-4" id="temporal-trends">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Tendencias Temporales
                                </div>
                                <div class="card-body">
                                    <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons">
                                        <label class="btn btn-outline-primary active">
                                            <input type="radio" name="tendenciasOptions" value="month" checked> Mensual
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="tendenciasOptions" value="quarter"> Trimestral
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="tendenciasOptions" value="year"> Anual
                                        </label>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="chartTendenciasTiempo"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="top-empleados" class='content-category'>
                    <!-- Top Empleados -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-users me-1"></i>
                                    Top Empleados por Departamento
                                </div>
                                <div class="card-body">
                                    <div id="topEmpleadosList">
                                        <!-- Se llenará dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets//js/dashboard/dashboard.js"></script>
    
    <!-- Tu script de gráficas (el que proporcionaste anteriormente) -->
    <script>
        // Aquí iría todo el código JavaScript que proporcionaste anteriormente
        // Configuración general de Chart.js, funciones de carga de datos, etc.
    </script>
</body>
</html>

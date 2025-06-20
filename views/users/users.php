<?php
session_start();
include('./../../admin/includes/config.php');
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../Repository/UserRepository.php";
require_once __DIR__ . "/../../controllers/UserController.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$user = new User();
$userRepository = new UserRepository($dbh, $user);
$userController = new UserController($userRepository);
$ManagerSession = new ManagerSession();

$super_admin = $ManagerSession->getSession('super_admin');                                                                          

if ($super_admin) {   
    // Manejar activación/desactivación
    if(isset($_GET['inid'])) {
        $userController->updateUser($_GET['inid'], ['Status' => 2]);
        header('location:users.php');
        exit;
    }

    if(isset($_GET['id'])) {
        $userController->updateUser($_GET['id'], ['Status' => 1]);
        header('location:users.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Gestión de Usuarios</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <style>
        :root {
            --primary-color: #e74c3c;
            --primary-dark: #c0392b;
            --secondary-color: #3498db;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        
        .data-table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .table-title {
            font-size: 1.5rem;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px 15px;
            margin-left: 10px;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }
        
        .user-table {
            width: 100% !important;
        }
        
        .user-table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 15px;
        }
        
        .user-table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .user-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
        }
        
        .status-inactive {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin: 0 3px;
            color: white;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-disable {
            background-color: var(--warning-color);
        }
        
        .btn-enable {
            background-color: var(--success-color);
        }
        
        .btn-edit {
            background-color: var(--secondary-color);
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
        }
        
        .page-item {
            margin: 0 5px;
        }
        
        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 5px;
            background-color: white;
            border: 1px solid #ddd;
            color: var(--dark-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .page-link:hover {
            background-color: #f5f5f5;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .page-item.disabled .page-link {
            color: #aaa;
            pointer-events: none;
        }
        
        .department-badge {
            display: inline-block;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin: 2px;
        }
    </style>
</head>
<body>
    <?php include('./../../components/header.php');?>
    <?php include('./../../components/sidebar.php');?>

    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Gestión de Usuarios</h1>
                <nav class="breadcrumb">
                    <a href="../leaves/leaves.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Usuarios</span>
                </nav>
            </div>

            <div class="data-table-container">
                <div class="table-header">
                    <h2 class="table-title">Listado de Usuarios</h2>
                    <a href="formuser.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </a>
                </div>

                <!-- Eliminamos el formulario de búsqueda ya que DataTables lo incluye -->

                <table id="userTable" class="user-table table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Departamentos</th>
                            <th>Estado</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargarán via AJAX -->
                    </tbody>
                </table>

                <!-- Eliminamos la paginación personalizada ya que DataTables la maneja -->
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
    <script src="../../assets/js/main/main.js"></script>
    
    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": "filtro_users.php",
                    "type": "POST",
                    "data": {
                        "action": "get_users"
                    }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "EmpId" },
                    { 
                        "data": null,
                        "render": function(data, type, row) {
                            return row.FirstName + ' ' + (row.LastName ?? '');
                        }
                    },
                    { "data": "departments" },
                    { 
                        "data": "Status",
                        "orderable": false
                    },
                    { "data": "rol" },
                    { 
                        "data": "actions",
                        "orderable": false
                    }
                ],
                order: [[0, 'desc']],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "responsive": true,
            });

            // Tooltips para botones de acción
            $('#userTable').on('draw.dt', function() {
                $('[title]').tooltip({
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });
    </script>
</body>
</html>
<?php
} else {
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
}
?>
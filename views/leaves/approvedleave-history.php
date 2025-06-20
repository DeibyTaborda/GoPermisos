<?php
session_start();
error_reporting(0);
include('../../includes/config.php');
require_once __DIR__ . "/../../models/Leave.php";
require_once __DIR__ . "/../../repository/LeaveRepository.php";
require_once __DIR__ . "/../../models/ManagerSession.php";

$leave = new Leave();
$leaveRepository = new LeaveRepository($dbh, $leave);
$ManagerSession = new ManagerSession();

$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

if($rol_id === 2 || $rol_id === 3 || $super_admin) {

?>
<body>
    <?php include('../../components/header.php');?>
    <?php include('../../components/sidebar.php');?>
    
    <main class="app-main">
        <div class="app-content">
            <div class="page-header">
                <h1>Historial de Permisos</h1>
                <nav class="breadcrumb">
                    <a href="leaves.php">Inicio</a>
                    <span class="separator"><i class="fas fa-chevron-right"></i></span>
                    <span>Permisos</span>
                </nav>
            </div>

            <div class="data-table-container">
                <div class="table-header">
                    <h2 class="table-title">Solicitudes de Permiso</h2>
                </div>

                <table id="leavesTable" class="department-table table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre del empleado</th>
                            <th>Tipo de permiso</th>
                            <th>Fecha de solicitud</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargarán via AJAX -->
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
    <script>
        $(document).ready(function() {
            $('#leavesTable').DataTable({
                "serverSide": true,
                "ajax": {
                    "url": 'filtro_leaves.php',
                    "type": "POST",
                    "data":  function(d) {
                        d.action = 'get_leaves',
                        d.estado = 4
                    },
                },
                "columns": [
                    { "data": "id" },
                    { 
                        "data": null,
                        "render": function(data, type, row) {
                            return row.FirstName + ' ' + (row.LastName ?? '');
                        }
                    },
                    { "data": "LeaveType" },
                    { 
                        "data": "PostingDate",
                        render: function(data) {
                                    const fecha = new Date(data);
                                    return fecha.toLocaleString("es-ES", {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit',
                                        hour12: true
                                    });
                                }
                    },

                    { 
                        "data": "status",
                        "orderable": false,
                        "render": function(data, type, row) {
                let statusClass = '';


                switch (data) {
                    case 'Pendiente':
                        statusClass = '<span class="status-badge status-pending"><i class="fas fa-clock me-1"></i> Pendiente</span>';
                        break;
                    case 'Aprobado':
                        statusClass = '<span class="status-badge status-approved"><i class="fas fa-check-circle me-1"></i> Aprobado</span>';
                        break;
                    case 'No aprobado':
                        statusClass = '<span class="status-badge status-rejected"><i class="fas fa-times-circle me-1"></i> Rechazado</span>';
                        break;
                    case 'Cancelado':
                        statusClass = '<span class="status-badge status-cancelled"><i class="fas fa-ban me-1"></i> Cancelado</span>';
                        break;
                    case 'Anulado':
                        statusClass = '<span class="status-badge status-voided"><i class="fas fa-ban me-1"></i> Anulado</span>';
                } 

                return statusClass;
            }
                    },
                    { 
                        "data": "actions",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "responsive": true,
                "order": [[0, "desc"]]
            });

            // Tooltips para botones de acción
            $('#leavesTable').on('draw.dt', function() {
                $('[title]').tooltip({
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });
    </script>
</body>
<?php
} else {
    echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
}
?>
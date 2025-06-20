<?php
session_start();
error_reporting(0);
include('../../includes/config.php');
require_once __DIR__ .'/../../models/Leave.php';
require_once __DIR__ . "/../../repository/LeaveRepository.php";
require_once __DIR__ . "/../../models/ManagerSession.php";
require_once __DIR__ . "/../../models/RecordCounter.php";
require_once __DIR__ . "/../../models/Department.php";
require_once __DIR__ . "/../../models/LeaveType.php";
require_once __DIR__ . "/../../models/User.php";

$leave = new Leave();
$leaveRepository = new LeaveRepository($dbh, $leave);
$department = new Department();
$RecordDepartments = new RecordCounter($dbh, $department);
$user = new User();
$RecordUsers = new RecordCounter($dbh, $user);
$leaveType = new LeaveType();
$RecordLeaveTypes = new RecordCounter($dbh, $leaveType);

$ManagerSession = new ManagerSession();
$rol_id = $ManagerSession->getSession('rol_id');
$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

$totalDepartments = $RecordDepartments->countRecords();
$totalUsers = $RecordUsers->countRecords();
$totalLeaveTypes = $RecordLeaveTypes->countRecords();

if($rol_id === 2 || $rol_id === 3 || $super_admin) {
    if ($super_admin) {
        $leaves = $leaveRepository->getLeaves();
    } else if ($rol_id === 2) {
        $leaves = $leaveRepository->getLeavesAsLeader($user_id);
    } else if ($rol_id === 3) {
        $leaves = $leaveRepository->getLeavesAsDirector($user_id);
    }
?>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Admin | Dashboard</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../../assets/plugins/materialize/css/materialize.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">    
        <link href="../../assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
        <link href="../../assets/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet">
        	
        <!-- Theme Styles -->
        <link href="../../assets/css/alpha.css" rel="stylesheet" type="text/css"/>
        <link href="../../assets/css/custom.css" rel="stylesheet" type="text/css"/>
        
    </head>
    <body>

        <?php include('../../admin/includes/header.php');?> 
        <?php include('../../admin/includes/sidebar.php');?>

            <main class="mn-inner">
                <div class="middle-content">
                    <div class="row no-m-t no-m-b">
                        <div class="col s12 m12 l4">
                            <div class="card stats-card">
                                <div class="card-content">
                                    <span class="card-title">Empleado</span>
                                    <span class="stats-counter">
                                    <span class="counter"><?php echo htmlentities($totalUsers);?></span></span>
                                </div>
                                <div id="sparkline-bar"></div>
                            </div>
                        </div>
                        <?php if ($super_admin) { ?>
                        <div class="col s12 m12 l4">
                            <div class="card stats-card">
                                <div class="card-content">
                                    <span class="card-title">Departamentos</span>                           
                                    <span class="stats-counter"><span class="counter"><?php echo htmlentities( $totalDepartments);?></span></span>
                                </div>
                                <div id="sparkline-line"></div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="col s12 m12 l4">
                            <div class="card stats-card">
                                <div class="card-content">
                                    <span class="card-title">Tipo de Permiso o Licencia</span>
                                    <span class="stats-counter"><span class="counter"><?php echo htmlentities($totalLeaveTypes);?></span></span>
                                </div>
                                <div class="progress stats-card-progress">
                                    <div class="determinate" style="width: 70%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row no-m-t no-m-b">
                        <div class="col s12 m12 l12">
                            <div class="card invoices-card">
                                <div class="card-content">
                                    <span class="card-title">Últimas solicitudes de permisos o licencias</span>
                                    <table id="example" class="display responsive-table ">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th width="200">Nombre</th>
                                                <th width="120">Tipo de Permiso o Licencia</th>
                                                <th width="180">Fecha de publicación</th>                 
                                                <th>Estado</th>
                                                <th align="center">Acción</th>
                                            </tr>
                                        </thead>                                
                                        <tbody>
                                            <?php
                                                if($leaves) {
                                                foreach($leaves as $leave){         
                                            ?>  
                                            <tr>
                                                <td> <b><?php echo htmlentities($leave->id);?></b></td>
                                                <td><?php echo htmlentities($leave->FirstName." ".$leave->LastName);?>(<?php echo htmlentities($leave->EmpId);?>)</td>
                                                <td><?php echo htmlentities($leave->LeaveType);?></td>
                                                <td><?php echo htmlentities($leave->PostingDate);?></td>
                                                <td>
                                                    <?php       
                                                        $stats = $leave->Status;

                                                        switch ($stats) {
                                                            case 3:
                                                                echo '<span style="color: #007bff">Pendiente de aprobación</span>'; 
                                                                break;
                                                            case 4:
                                                                echo '<span style="color: #28a745">Aprobado</span>';
                                                                break;
                                                            case 5:
                                                                echo '<span style="color: #dc3545">No Aprobado</span>';
                                                                break;
                                                            case 6:
                                                                echo '<span style="color: #fd7e14">Cancelado</span>'; 
                                                                break;
                                                        }
                                                        
                                                    ?>
                                                </td>
                                                <td>
                                                <td><a href="leave-details.php?leaveid=<?php echo htmlentities($leave->id);?>" class="waves-effect waves-light btn blue m-b-xs"  > Ver detalles</a></td>
                                            </tr>
                                            <?php }} else { ?>
                                                <tr>
                                                    <td colspan="6">No hay solicitudes de permisos para este departamento</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Javascripts -->
        <script src="../../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../../assets/plugins/materialize/js/materialize.js"></script>
        <script src="../../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../../assets/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="../../assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
        <script src="../../assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="../../assets/plugins/chart.js/chart.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../../assets/plugins/curvedlines/curvedLines.js"></script>
        <script src="../../assets/plugins/peity/jquery.peity.min.js"></script>
        <script src="../../assets/js/alpha.min.js"></script>
        <script src="../../assets/js/pages/dashboard.js"></script>
        
    </body>
</html>
<?php } else {
            echo "<script type='text/javascript'> document.location = '../../index.php'; </script>";
} ?>
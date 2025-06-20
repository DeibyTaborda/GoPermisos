<?php
session_start();
error_reporting(0);
include('./../../../admin/includes/config.php');
require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../../../Repository/UserRepository.php";
require_once __DIR__ . "/../../../controllers/UserController.php";
require_once __DIR__ . "/../../../models/ManagerSession.php";

$user = new User();
$userRepository = new UserRepository($dbh, $user);
$userController = new UserController($userRepository);
$ManagerSession = new ManagerSession();

$super_admin = $ManagerSession->getSession('super_admin');

if ($super_admin) {   
    if(isset($_GET['inid'])) {
        $userController->updateUser($_GET['inid'], ['Status' => 2]);
        header('location:managedirector.php');
    }

    if(isset($_GET['id'])) {
        $userController->updateUser($_GET['id'], ['Status' => 1]);
        header('location:managedirector.php');
    }

    $results = $userRepository->getUsers(['RolID' => 3]);
    $directors = [];

    foreach($results as $user) {
        $id_user = $user->id;

        if (!isset($directors[$id_user])) {
            $directors[$id_user] = [
                'id' => $user->id,
                'EmpId' => $user->EmpId,
                'FirstName' => $user->FirstName,
                'LastName' => $user->LastName,
                'Status' => $user->Status,
                'rol' => $user->rol,
                'departments' => []
            ];
        }

        $directors[$id_user]['departments'][] = $user->DepartmentName;
    }
    
    $base_url = $_SERVER['BASE_URL'];
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Admin | Administrar Directores</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../../../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../../../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
        <link href="../../../assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

            
        <!-- Theme Styles -->
        <link href="../../../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../../../assets/css/custom.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>

       <?php include('./../../../admin/includes/header.php');?>
       <?php include('./../../../admin/includes/sidebar.php');?>

        <main class="mn-inner">
            <div class="row">
                <div class="col s12">
                    <div class="page-title">Gestionar Directores</div>
                </div>
                <div class="col s12 m12 l12">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">Información de Directores</span>
                            <table id="example" class="display responsive-table ">
                                <thead>
                                    <tr>
                                        <th>no Sr </th>
                                        <th>Id Emp </th>
                                        <th>Nombre completo</th>
                                        <th>Departamento</th>
                                        <th>Estado</th>
                                        <th>Rol</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>     
                                <tbody>
                                    <?php 
                                        if($directors) {
                                            foreach($directors as $user){               
                                    ?>  
                                    <tr>
                                        <td> <?php echo htmlentities($user['id']);?></td>
                                        <td><?php echo htmlentities($user['EmpId']);?></td>
                                        <td><?php echo htmlentities($user['FirstName']);?>&nbsp;<?php echo htmlentities($user['LastName']);?></td>
                                        <td><?php foreach ($user['departments'] as $department) {
                                            echo htmlentities($department) . "<br>";
                                        } ?></td>
                                        <td>
                                            <?php 
                                                $stats = $user['Status'];

                                                if($stats === 1){
                                            ?>
                                                <a class="waves-effect waves-green btn-flat m-b-xs">Activo</a>
                                            <?php } else { ?>
                                                <a class="waves-effect waves-red btn-flat m-b-xs">Inactivo</a>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo htmlentities($user['rol']);?></td>
                                        <td>
                                            <?php if ($user['Status'] === 1) { ?>
                                                <a 
                                                    href="managedirector.php?inid=<?php echo htmlentities($user['id']);?>" 
                                                    onclick="return confirm('¿Quieres deshabilitar este Director?');"
                                                    class="btn orange lighten-1 tooltipped" data-position="top" data-tooltip="Deshabilitar">
                                                    <i class="material-icons">visibility_off</i>
                                                </a>
                                            <?php } else {?>
                                                <a 
                                                    href="managedirector.php?id=<?php echo htmlentities($user['id']);?>" 
                                                    onclick="return confirm('¿Quieres activar este Director?');"
                                                    class="btn green lighten-1 tooltipped" data-position="top" data-tooltip="Activar">
                                                    <i class="material-icons">check_circle</i>
                                                </a>
                                            <?php }  ?>
                                                <a href="../adduser.php?action=edit&id_user=<?php echo htmlentities($user['id']);?>" class="btn blue lighten-1 tooltipped" data-position="top" data-tooltip="Editar">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                        </td>
                                    </tr>
                                        <?php }}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
         
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="../../../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../../../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../../../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../../../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../../../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="../../../assets/js/alpha.min.js"></script>
        <script src="../../../assets/js/pages/table-data.js"></script>
        
    </body>
</html>
<?php
} else {
    echo "<script type='text/javascript'> document.location = '../../../index.php'; </script>";
} ?>
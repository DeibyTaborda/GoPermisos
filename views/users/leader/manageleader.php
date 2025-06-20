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

$user_id = $ManagerSession->getSession('user_id');
$rol_id = $ManagerSession->getSession('rol_id');
$super_admin = $ManagerSession->getSession('super_admin');

if ($super_admin) {   
    if (isset($_GET['acti'])) {
        $updateleader = $userController->updateUser( $_GET['acti'], ['Status' => 1]);
        header('location:manageleader.php');
    }
    
    if (isset($_GET['inact'])) {
        $updateleader = $userController->updateUser( $_GET['inact'], ['Status' => 2]);
        header('location:manageleader.php');
    }
    
    $leaders = $userRepository->getUsers(['RolID' => 2]);

 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Admin | Administrar Líderes</title>
        
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
                    <div class="page-title">Gestionar Líderes</div>
                </div>
                <div class="col s12 m12 l12">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">Información de Líderes</span>
                            <table id="example" class="display responsive-table ">
                                <thead>
                                    <tr>
                                        <th>no Sr </th>
                                        <th>Id Líder </th>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th>Departamento</th>
                                        <th>Rol</th>
                                        <th>Accion</th>
                                    </tr>
                                </thead>     
                                <tbody>
                                <?php 
                                    if (!empty($leaders)) { 
                                        foreach($leaders as $leader) { ?>
                                            <tr>
                                                <td><?php echo $leader->id; ?></td>
                                                <td><?php echo $leader->EmpId; ?></td>                                             
                                                <td><?php echo $leader->FirstName . ' ' . $leader->LastName; ?></td>
                                                <td><?php echo $leader->Status === 1 ? '<span class="green-text">Activo</span>' : '<span class="red-text">Inactivo</span>'; ?></td>
                                                <td><?php echo $leader->DepartmentName; ?></td>
                                                <td><?php echo $leader->rol; ?></td>
                                                <td>
                                                    <?php if ($leader->Status === 1) { ?>
                                                <a 
                                                    href="manageleader.php?inact=<?php echo htmlentities($leader->id);?>" 
                                                    onclick="return confirm('¿Quieres deshabilitar este Líder?');"
                                                    class="btn orange lighten-1 tooltipped" data-position="top" data-tooltip="Deshabilitar">
                                                    <i class="material-icons">visibility_off</i>
                                                </a>
                                            <?php } else {?>
                                                <a 
                                                    href="manageleader.php?acti=<?php echo htmlentities($leader->id);?>" 
                                                    onclick="return confirm('¿Quieres activar este Líder?');"
                                                    class="btn green lighten-1 tooltipped" data-position="top" data-tooltip="Activar">
                                                    <i class="material-icons">check_circle</i>
                                                </a>
                                            <?php }  ?>
                                                <a href="../adduser.php?action=edit&id_user=<?php echo htmlentities($leader->id);?>" class="btn blue lighten-1 tooltipped" data-position="top" data-tooltip="Editar">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                </td>
                                            </tr> 
                                        <?php } 
                                    } else { ?>
                                        <tr>
                                            <td colspan="4">No hay líderes registrados.</td>
                                        </tr>
                                    <?php } ?>
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
        <script src="../../../assets/js/alpha.js"></script>
        <script src="../../../assets/js/pages/table-data.js"></script>
        
    </body>
</html>
<?php } else {
        echo "<script type='text/javascript'> document.location = '../../../index.php'; </script>";
} ?>
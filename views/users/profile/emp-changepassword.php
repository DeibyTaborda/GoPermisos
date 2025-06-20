<?php
session_start();
error_reporting(0);
include('../../../includes/config.php');
require_once __DIR__ . "/../../../utils/alerts.php";
require_once __DIR__ . "/../../../models/User.php";
require_once __DIR__ . "/../../../Repository/UserRepository.php";
require_once __DIR__ . "/../../../controllers/UserController.php";
require_once __DIR__ . "/../../../models/ManagerSession.php";

$ManagerSession = new ManagerSession();
$user_id = $ManagerSession->getSession('user_id');
$rol_id = $ManagerSession->getSession('rol_id');
$super_admin = $ManagerSession->getSession('super_admin');

if(empty($rol_id)) {
    header('location:index.php');
} else {
    if(isset($_POST['change'])) {
        $user = new User();
        $userRepository = new UserRepository($dbh, $user);
        $userController = new UserController($userRepository);
        $password = $_POST['password'];
        $newpassword = $_POST['newpassword'];
        $confirmpassword = $_POST['confirmpassword'];

        $userToChangePassword = $userRepository->getByIdUser($user_id);
        if (password_verify($password, $userToChangePassword->Password)) {
            if ($newpassword !== $confirmpassword) {
                showToast('error', 'Las contraseñas no coinciden');
            } else {
                $request = $userController->updateUser( $user_id, ['Password' => $_POST['newpassword']]);
            }
        } else {
            showToast('error', 'La contraseña actual es incorrecta');
        }
        
        if (isset($request['success']) && $request['success'] === true) {
            showToast('success', 'Perfil actualizado correctamente');
        } else if (isset($request['type']) && $request['type'] === 'info') {
            showToast('info', $request['message']);
        } else if (isset($request['success']) && $request['success'] === false) {
            showToast('success', $request['message']);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Empleado | Cambia la contraseña</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../../../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../../../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href="../../../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../../../assets/css/custom.css" rel="stylesheet" type="text/css"/>
        <link href="../../../assets/css/modal.css" rel="stylesheet" type="text/css"/>
        <link href="../../../assets/css/alerts/alerts.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
    <?php if ($rol_id === 2 || $rol_id === 3 || $super_admin) {
                include('../../../admin/includes/header.php');
                include('../../../admin/includes/sidebar.php');
            } else {
                include('../../../includes/header.php');
                include('../../../includes/sidebar.php');
            }
        ?>
        <main class="mn-inner">
            <div class="row">
                <div class="col s12">
                    <div class="page-title">Cambiar contraseña</div>
                </div>
                <div class="col s12 m12 l6">
                    <div class="card">
                        <div class="card-content">
                            <div class="row">
                                <form class="col s12" name="chngpwd" method="post">
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="password" type="password"  class="validate" autocomplete="off" name="password"  required>
                                            <label for="password">Contraseña actual</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <input id="password" type="password" name="newpassword" class="validate" autocomplete="off" required>
                                            <label for="password">Nueva contraseña</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <input id="confirmpassword" type="password" name="confirmpassword" class="validate" autocomplete="off" required>
                                            <label for="confirmpassword">Confirmar contraseña</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <button type="submit" name="change" class="waves-effect waves-light btn indigo m-b-xs" onclick="return valid();">Cambiar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="../../../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../../../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../../../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../../../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../../../assets/js/alpha.js"></script>
        <script src="../../../assets/js/pages/form_elements.js"></script>
        
    </body>
</html>
<?php } ?> 
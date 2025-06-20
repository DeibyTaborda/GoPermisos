<?php 
session_start();
$base_url = $_SERVER['BASE_URL'];
require_once $_SERVER['DOCUMENT_ROOT'] . "/$base_url/models/User.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/$base_url/repository/UserRepository.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/$base_url/models/ManagerSession.php";

$ManagerSession = new ManagerSession();

$user_id = $ManagerSession->getSession('user_id');
$super_admin = $ManagerSession->getSession('super_admin');

$user = new User();
$userRepository = new UserRepository($dbh, $user);
$user = $userRepository->getByIdUser($user_id);

$base_url = $_SERVER['BASE_URL'];
?>
<aside id="slide-out" class="side-nav white fixed">
    <div class="side-nav-wrapper">
        <div class="sidebar-profile">
            <div class="sidebar-profile-image">
                <img src="/<?= $base_url ?>/assets/images/profile-image.png" class="circle" alt="">
            </div>
            <div class="sidebar-profile-info">
                <p><?php echo htmlentities($user->FirstName);?></p>
                <span><?php echo htmlentities($user->EmpId)?></span>
            </div>
        </div>
        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
            <li class="no-padding"><a class="waves-effect waves-grey" href="/<?= $base_url ?>/views/leaves/dashboard.php"><i class="material-icons">settings_input_svideo</i>Dashboard</a></li>
            <?php 
                if ($super_admin) {
            ?>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">person</i>Usuarios<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="/<?= $base_url ?>/views/users/users.php">Gestionar usuarios</a></li>
                        <li><a href="/<?= $base_url ?>/views/users/leader/manageleader.php">Gestionar líderes</a></li>
                        <li><a href="/<?= $base_url ?>/views/users/director/managedirector.php">Gestionar directores</a></li>
                        <li><a href="/<?= $base_url ?>/views/users/adduser.php">Crear usuario</a></li>
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">business</i>Departamentos<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="/<?= $base_url ?>/views/departments/managedepartments.php">Gestionar departamento</a></li>
                        <li><a href="/<?= $base_url ?>/views/departments/form_department.php">Agregar departamento</a></li>
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">assignment</i>Tipo de licencias<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="/<?= $base_url ?>/views/leavestypes/manageleavetype.php">Gestionar tipo de licencias</a></li>
                        <li><a href="/<?= $base_url ?>/views/leavestypes/form_leavetype.php">Agregar tipo de licencia</a></li>
                    </ul>
                </div>
            </li>
            <?php 
                }
            ?>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">desktop_windows</i>Permisos<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="/<?= $base_url ?>/views/leaves/leaves.php">Todos los permismos o licencias </a></li>
                        <li><a href="/<?= $base_url ?>/views/leaves/pending-leavehistory.php"> Solicitudes pendientes </a></li>
                        <li><a href="/<?= $base_url ?>/views/leaves/approvedleave-history.php">Solicitudes aprobadas</a></li>
                            <li><a href="/<?= $base_url ?>/views/leaves/notapproved-leaves.php">Solicitudes no aprobadas</a></li>

                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey"><i class="material-icons">event_note</i>Mis permisos<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                <div class="collapsible-body">
                    <ul>
                        <li><a href="/<?= $base_url ?>/views/apply-leave/apply-leave.php">Solicitar permiso</a></li>
                        <li><a href="/<?= $base_url ?>/views/apply-leave/leavehistory.php">Historial de mis permisos</a></li>
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="waves-effect waves-grey" href="/<?= $base_url ?>/logout.php"><i class="material-icons">exit_to_app</i>Salir</a>
            </li>  
        </ul>
    </div>
</aside>
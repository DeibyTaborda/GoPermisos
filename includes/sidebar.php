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
                <?php if($user) { ?>
                <p><?php echo htmlentities($user->FirstName." ".$user->LastName);?></p>
                <span><?php echo htmlentities($user->EmpId)?></span>
                <?php } ?>
            </div>
        </div>
        <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
            <li class="no-padding">
                <a class="waves-effect waves-grey" href="/<?= $base_url ?>/views/users/profile/myprofile.php">
                    <i class="material-icons">
                        account_box
                    </i>
                    Mis perfiles
                </a>
            </li>
            <li class="no-padding">
                <a class="waves-effect waves-grey" href="/<?= $base_url ?>/views/users/profile/emp-changepassword.php">
                    <i class="material-icons">
                        settings_input_svideo
                    </i>
                    Cambia la contraseña
                </a>
            </li>
            <li class="no-padding">
                <a class="collapsible-header waves-effect waves-grey">
                    <i class="material-icons">
                        aplicaciones
                    </i>
                    Permisos
                    <i class="nav-drop-icon material-icons">
                        keyboard_arrow_right
                    </i>
                </a>
                <div class="collapsible-body">
                    <ul>
                        <li>
                            <a href="/<?= $base_url ?>/views/apply-leave/apply-leave.php">
                                Solicitar Permiso o Licencia
                            </a>
                        </li>
                        <li>
                            <a href="/<?= $base_url ?>/views/apply-leave/leavehistory.php">
                                Historial de Permisos o Licencias
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="no-padding">
                <a class="waves-effect waves-grey" href="/<?= $base_url ?>/logout.php">
                    <i class="material-icons">exit_to_app</i>
                    Salir
                </a>
            </li>
        </ul> 
    </div>
</aside>

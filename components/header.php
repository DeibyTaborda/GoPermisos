<?php
$super_admin = $ManagerSession->getSession('super_admin');
$user_id = $ManagerSession->getSession('user_id');
$rol_id = $ManagerSession->getSession('rol_id');

require_once __DIR__ . "/../models/GetNotificaciones.php";
require_once __DIR__ . "/../controllers/GetNotificationController.php";
require_once __DIR__ . "/../utils/utils.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../Repository/UserRepository.php";

$user = new User();
$userRepository = new UserRepository($dbh, $user);
$dataUser = $userRepository->getByIdUser($user_id);

$getNotifications = new GetNotifications($dbh);
$getNotificationController = new GetNotificationController($getNotifications);
$notificationLeaves = $getNotificationController->getNotifications($rol_id, $user_id);

$total_noti_colab = count($notificationLeaves['leavesColaboradores']);
$total_noti_user = count($notificationLeaves['leavesUser']);
$total_noti = $total_noti_colab + $total_noti_user;

$base_url = $_SERVER['BASE_URL'];

function rol($rol_id, $super_admin) {
    if ($super_admin) {
        return 'Super Admin';
    } else if ($rol_id === 2) {
        return 'Líder';
    } else if ($rol_id === 3) {
        return 'Director';
    } else if ($rol_id === 1) {
        return 'Colaborador';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRINMO || Gestión de Permisos</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_red.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS personalizado -->
    <style>
    :root {
        --primary-color: <?php echo $_SERVER['PROJECT_NAME'] === 'GoPermisos' ? '#e74c3c' : '#1976D2' ?>;
        --primary-dark: <?php echo $_SERVER['PROJECT_NAME'] === 'GoPermisos' ? '#c0392b' : '#115293' ?>;
        --secondary-color: #3498db;
        --dark-color: #2c3e50;
        --light-color: #ecf0f1;
        --gray-color: #95a5a6;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --sidebar-width: 250px;
        --sidebar-collapsed-width: 80px;
        --header-height: 70px;
    }
    </style>
    <link rel="stylesheet" href="/<?= $base_url ?>/assets/css/main/main.css?v=1.2">
    <link rel="stylesheet" href="/<?= $base_url ?>/assets/css/alerts/alerts.css">
    <link rel="stylesheet" href="/<?= $base_url ?>/assets/css/main/tables.css?v=1.2">
    <link rel="stylesheet" href="/<?= $base_url ?>/assets/css/main/file-uploads.css">
    <link rel="stylesheet" href="/<?= $base_url ?>/assets/css/pages/details.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <div class="header-left">
                <div class="logo-container">
                    <span class="app-name"><?php echo $_SERVER['PROJECT_NAME'] === 'GoPermisos' ? 'CRINMO' : 'CRINMBO' ?></span>
                    <span class="app-subname">Gestión de Permisos</span>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="header-right">
                <div class="notification-container">
                    <button class="notification-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge"><?= $total_noti ?></span>
                    </button>
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h3>Notificaciones</h3>
                            <!-- <button class="mark-all-read">Marcar todas como leídas</button> -->
                        </div>
                        <div class="notification-list">
                            <?php if ($notificationLeaves['leavesColaboradores']): ?>
                            <?php   foreach ($notificationLeaves['leavesColaboradores'] as $notification):?> 
                            <a href="/<?= $base_url ?>/views/leaves/leave-details.php?leaveid=<?php echo $notification->id; ?>">
                                <div class="notification-item unread">
                                    <div class="notification-icon">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p>Solicitud de permiso pendiente de <?php echo $notification->FirstName ?></p>
                                        <small><?php echo timeAgo($notification->PostingDate) ?></small>
                                    </div>
                                </div>
                            </a>
                            <?php   endforeach;?> 
                            <?php endif; ?>
                            <?php if(!empty($notificationLeaves['leavesUser'])):?>
                            <?php   foreach ($notificationLeaves['leavesUser'] as $notification):?>
                            <a href="/<?= $base_url ?>/views/apply-leave/leave-details.php?leaveid=<?php echo $notification->id; ?>">
                                <div class="notification-item">
                                    <div class="notification-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p><?php echo getPermisoStatusMessage($notification->Status); ?></p>
                                        <small><?php echo timeAgo($notification->AdminRemarkDate); ?></small>
                                    </div>
                                </div>
                            </a>
                            <?php   endforeach;?> 
                            <?php endif;?>
                        </div>
                        <!-- <div class="notification-footer">
                            <a href="#">Ver todas las notificaciones</a>
                        </div> -->
                    </div>
                </div>
                <div class="user-profile">
                    <img src="/<?= $base_url ?>/assets/images/profile-image.png" alt="Usuario" class="user-avatar">
                </div>
                <div class="user-dropdown" id="userDropdown">
                    <p class="user-dropdown-name"><?= $dataUser->FirstName . " " . $dataUser->LastName ?></p>
                    <p class="user-dropdown-role"><?= rol($rol_id, $super_admin) ?></p>
                </div>

            </div>
        </header>
<?php
$base_url = $_SERVER['BASE_URL'];
$super_admin = $ManagerSession->getSession('super_admin');
$rol = $ManagerSession->getSession('rol_id');
?>
<aside class="app-sidebar" id="appSidebar">
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/dashboard.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/<?= $base_url ?>/views/users/profile/myprofile.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Mi perfil</span>
                </a>
            </li>
            <?php if ($super_admin): ?>
            <li class="nav-item has-submenu">
                <a href="#" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                    <i class="fas fa-chevron-right submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="/<?= $base_url ?>/views/users/users.php">Lista de usuarios</a></li>
                    <li><a href="/<?= $base_url ?>/views/users/formuser.php">Agregar usuario</a></li>
                    <!-- <li><a href="/users/roles.php">Roles</a></li> -->
                </ul>
            </li>
            <li class="nav-item has-submenu">
                <a href="#" class="nav-link">
                    <i class="fas fa-building"></i>
                    <span>Departamentos</span>
                    <i class="fas fa-chevron-right submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="/<?= $base_url ?>/views/departments/managedepartments.php">Lista de departamentos</a></li>
                    <li><a href="/<?= $base_url ?>/views/departments/form_department.php">Agregar departamento</a></li>
                </ul>
            </li>
            <li class="nav-item has-submenu">
                <a href="#" class="nav-link">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Sedes</span>
                    <i class="fas fa-chevron-right submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="/<?= $base_url ?>/views/sedes/sedes.php">Lista de sedes</a></li>
                    <li><a href="/<?= $base_url ?>/views/sedes/form_sede.php">Agregar sede</a></li>
                </ul>
            </li>
            <li class="nav-item has-submenu">
                <a href="#" class="nav-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Tipo de permisos</span>
                    <i class="fas fa-chevron-right submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="/<?= $base_url ?>/views/leavestypes/manageleavetype.php">Lista de tipo de permisos</a></li>
                    <li><a href="/<?= $base_url ?>/views/leavestypes/form_leavetype.php">Agregar tipo de permiso</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if ($super_admin || $rol === 2 || $rol === 3): ?>
            <li class="nav-item has-submenu">
                <a href="#" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Permisos</span>
                    <i class="fas fa-chevron-right submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="/<?= $base_url ?>/views/leaves/leaves.php">Todos los permisos</a></li>
                    <li><a href="/<?= $base_url ?>/views/leaves/pending-leavehistory.php">Pendientes</a></li>
                    <li><a href="/<?= $base_url ?>/views/leaves/approvedleave-history.php">Aprobados</a></li>
                    <li><a href="/<?= $base_url ?>/views/leaves/notapproved-leaves.php">Rechazados</a></li>
                </ul>
            </li>
           <?php endif; ?> 
            <li class="nav-item has-submenu">
                <a href="#" class="nav-link">
                    <i class="fas fa-user-clock"></i>
                    <span>Mis permisos</span>
                    <i class="fas fa-chevron-right submenu-toggle"></i>
                </a>
                <ul class="submenu">
                    <li><a href="/<?= $base_url ?>/views/apply-leave/leavehistory.php">Historial</a></li>
                    <li><a href="/<?= $base_url ?>/views/apply-leave/apply-leave.php">Solicitar permiso</a></li>
                </ul>
            </li>
            <?php if ($super_admin): ?>
            <li class="nav-item">
                <a href="/<?= $base_url ?>/views/dashboard.php" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Estadísticas</span>
                </a>
            </li>
            <?php endif; ?> 
            <li class="nav-item">
                <a href="/<?= $base_url ?>/logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
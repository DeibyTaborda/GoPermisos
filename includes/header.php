<?php 
    $isread = 0;

    $sql = "
        SELECT 
            tblleaves.id AS lid,
            tblusers.FirstName,
            tblusers.LastName,
            tblusers.EmpId,
            DATE_FORMAT(tblleaves.AdminRemarkDate, '%d/%m/%Y %h:%i %p') AS AdminRemarkDate,
            DATE_FORMAT(tblleaves.ToDate, '%d/%m/%Y %h:%i %p') AS ToDate,
            DATE_FORMAT(tblleaves.FromDate, '%d/%m/%Y %h:%i %p') AS FromDate,
            tblleaves.Description,
            tblleaves.AdminRemark,
            tblleaves.Status,
            tblleaves.IsRead     
        FROM tblleaves
        JOIN tblusers 
            ON tblleaves.empid = tblusers.id
        WHERE tblusers.EmailID = :email_id
        AND IsRead != 2
        AND (tblleaves.status = 4
        OR tblleaves.status = 5)
    ";

    $query = $dbh -> prepare($sql);
    $query->bindParam(':email_id', $emplogin,PDO::PARAM_STR);
    $query->execute();
    $notifications = $query->fetchAll(PDO::FETCH_OBJ);

    $unreadcount=$query->rowCount();
?>

<div class="loader-bg"></div>
<div class="loader">
    <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
            <div class="circle"></div>
            </div><div class="circle-clipper right">
            <div class="circle"></div>
            </div>
        </div>
        <div class="spinner-layer spinner-spinner-teal lighten-1">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
            <div class="circle"></div>
            </div><div class="circle-clipper right">
            <div class="circle"></div>
            </div>
        </div>
        <div class="spinner-layer spinner-yellow">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
            <div class="circle"></div>
            </div><div class="circle-clipper right">
            <div class="circle"></div>
            </div>
        </div>
        <div class="spinner-layer spinner-green">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div><div class="gap-patch">
            <div class="circle"></div>
            </div><div class="circle-clipper right">
            <div class="circle"></div>
            </div>
        </div>
    </div>
</div>
<div class="mn-content fixed-sidebar">
    <header class="mn-header navbar-fixed">
        <nav class="red darken-1">
            <div class="nav-wrapper row">
                <section class="material-design-hamburger navigation-toggle">
                    <a href="#" data-activates="slide-out" class="button-collapse show-on-large material-design-hamburger__icon">
                        <span class="material-design-hamburger__layer"></span>
                    </a>
                </section>
                <div class="header-title col s3">      
                    <span class="chapter-title">CRINMO | GESTIÓN DE PERMISOS</span>
                </div>
                <ul class="right col s9 m3 nav-right-menu">
                    <li class="hide-on-small-and-down"><a href="javascript:void(0)" data-activates="dropdown1" class="dropdown-button dropdown-right show-on-large"><i class="material-icons">notifications_none</i>
                    <span class="badge"><?php echo htmlentities($unreadcount);?></span></a></li>
                    <li class="hide-on-med-and-up"><a href="javascript:void(0)" class="search-toggle"><i class="material-icons">search</i></a></li>
                </ul>
                
                <ul id="dropdown1" class="dropdown-content notifications-dropdown">
                    <li class="notificatoins-dropdown-container">
                        <ul>
                            <li class="notification-drop-title">Notifications</li>
                            <?php 
                                if($notifications) {
                                    foreach($notifications as $result) {            
                            ?>  
                            <li>
                                <button style="border: none; width: 100%; background-color: white;" class="btn-leave-details btn-notification" data-id="<?php echo htmlentities($result->lid);?>">
                                    <div class="notification">
                                        <div class="notification-icon circle cyan"><i class="material-icons">done</i></div>
                                        <div class="notification-text"><p><b><?php echo htmlentities($result->FirstName." ".$result->LastName);?><br />(<?php echo htmlentities($result->EmpId);?>)</b> <?= $result->Status === 4 ? 'permiso aceptado' : 'permiso denegado';?></p><span>el <?php echo htmlentities($result->AdminRemarkDate);?></b</span></div>
                                    </div>
                                </button>
                            </li>
                            <?php   
                                    }
                                } 
                            ?>          
                </ul>
            </div>
        </nav>
    </header>
    <div id="modal">
    <div id="modal-content"></div>
</div>

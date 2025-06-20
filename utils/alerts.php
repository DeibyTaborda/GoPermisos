<?php
function showAlert($type, $message, $acction) {
    $icons = [
        'success' => '✔',
        'error' => 'x',
        'info' => 'ℹ️'
    ];

    $title = [
        'success' => 'Éxito',
        'error' => 'Error',
        'info' => '¡Estas seguro!'
    ];

    $id = "alert_" . uniqid();
?>
<div class="super-card" id="<?php echo $id; ?>">
    <div class="card-alert <?php echo $type ?>">
        <div class="check-circle <?php echo $type ?>"><?php echo $icons[$type] ?></div>
        <div class="title"><?php echo $title[$type] ?></div>
        <div class="message"><?php echo $message ?></div>
    </div>
</div>

<script>
    setTimeout(() => {
        const el = document.getElementById('<?php echo $id; ?>');
        if (el) {
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }
    }, 3000);
</script>
<?php } ?>

<?php 
    function showToast($type, $message) {
        $icons = [
            'success' => '✔',
            'error' => 'x',
            'info' => '💡'
        ];

        $title = [
            'success' => 'Éxito',
            'error' => 'Error',
            'info' => 'Aviso'
        ];

        $id = "toast_" . uniqid();

    ?>
    <div class="container-toast <?php echo $type ?>" id="<?php echo $id; ?>">
        <div class="toast-icon <?php echo $type ?>"><?php echo $icons[$type] ?></div>
        <div class="toast-title">
            <p><?php echo $title[$type] ?></p>
            <p><?php echo $message ?></p>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const el2 = document.getElementById('<?php echo $id; ?>');
            if (el2) {
                el2.style.opacity = '0';
                setTimeout(() => el2.remove(), 500);
            }
        }, 3000);
    </script>
    <?php
}

function showSweetAlert($type, $message, $redirectUrl = null, $isEditing = false) {
    $icons = [
        'success' => 'success',
        'error' => 'error',
        'info' => 'info'
    ];

    $title = [
        'success' => 'Éxito',
        'error' => 'Error',
        'info' => 'Aviso'
    ];

    echo "<script>
            Swal.fire({
                icon: '{$icons[$type]}',
                title: '{$title[$type]}',
                html: '{$message}',
                showConfirmButton: true,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    " . ($type == 'success' && $redirectUrl && !$isEditing ? 
                        "window.location.href = '{$redirectUrl}';" : 
                        "\$('.modal').modal('hide');") . "
                }
            });
          </script>";
}
?>
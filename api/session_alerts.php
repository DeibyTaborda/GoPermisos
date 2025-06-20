<?php
session_start();
$response = [];

if (!empty($_SESSION['alert_type']) && !empty($_SESSION['alert_message'])) {
    $response = [
        'alert_type' => $_SESSION['alert_type'],
        'alert_message' => $_SESSION['alert_message'],
        'id_registro' => isset($_SESSION['id_registro']) ? $_SESSION['id_registro'] : null
    ];
    
    unset($_SESSION['alert_type']);
    unset($_SESSION['alert_message']);
    unset($_SESSION['id_registro']);
}

header('Content-Type: application/json');
echo json_encode($response);
?>

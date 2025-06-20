<?php
function calcularTiempoPermiso($salida, $llegada) {
    $inicio = new DateTime($salida);
    $fin = new DateTime($llegada);
    
    if ($fin < $inicio) {
        return "Error: La hora de llegada no puede ser antes de la hora de salida.";
    }

    $totalMinutos = 0;

    while ($inicio < $fin) {
        $diaSemana = $inicio->format('N');
        $hora = (int)$inicio->format('H');
        $minuto = (int)$inicio->format('i');

        if (
            ($diaSemana >= 1 && $diaSemana <= 5 && $hora >= 8 && $hora < 18) ||
            ($diaSemana == 6 && $hora >= 8 && $hora < 12)
        ) {
            $totalMinutos++;
        }
        $inicio->modify('+1 minute');
    }
    $horas = floor($totalMinutos / 60);
    $minutos = $totalMinutos % 60;

    return "{$horas} horas y {$minutos} minutos";
}

function formatDate($fecha) {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
    return $date->format('d/m/Y h:i A');
}

function existFileDisability($proofs, $nameDefault) {
    if (isset($proofs)) {
        foreach ($proofs as $proof) {
            if (strpos($proof, $nameDefault) !== false) {
                return true;
            }
        }
    }
    return false;
}

function existIncapacidad($files, $nameFile) {
    if (empty($files)) {
        return true;
    }

    if ($files >= 1) {
        foreach($files as $file) {
            if (count($files) === 1 && strpos($file, $nameFile) !== false) {
                return true;
            }
        }
    }

    return false;
}

function convertirHora12a24($fecha12) {
    $dateTime = DateTime::createFromFormat('d/m/Y h:i A', $fecha12);
    if ($dateTime) {
        return $dateTime->format('Y-m-d H:i:s');
    } else {
        return false;
    }
}

function obtenerExtension($rutaArchivo) {
    $info = pathinfo($rutaArchivo);
    return isset($info['extension']) ? strtolower($info['extension']) : '';
}

function orderByDate($array) {
  usort($array, function ($a, $b) {
    return strtotime($b->PostingDate) - strtotime($a->PostingDate);
    });

    return $array;
}

function getActionButtonsUser($user) {
    $buttons = '';
    
    if ($user->Status == 1) {
        $buttons .= '<a href="users.php?inid='.$user->id.'" 
                     onclick="return confirm(\'¿Deshabilitar este usuario?\')"
                     class="action-btn btn-disable" title="Deshabilitar">
                        <i class="fas fa-eye-slash"></i>
                    </a>';
    } else {
        $buttons .= '<a href="users.php?id='.$user->id.'" 
                     onclick="return confirm(\'¿Activar este usuario?\')"
                     class="action-btn btn-enable" title="Activar">
                        <i class="fas fa-check-circle"></i>
                    </a>';
    }
    
    $buttons .= '<a href="formuser.php?action=edit&id_user='.$user->id.'" 
                   class="action-btn btn-edit" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>';
    
    return $buttons;
}

function timeAgo($date) {
    date_default_timezone_set('America/Bogota');
    
    $now = time(); // Hora actual
    $your_date = strtotime($date); // Convertir la fecha a timestamp
    $diff = $now - $your_date; // Diferencia en segundos

    // Si la diferencia es menos de 1 minuto
    if ($diff < 60) {
        return 'Hace menos de un minuto';
    }

    // Si la diferencia es menos de 1 hora
    if ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "Hace $minutes minuto" . ($minutes > 1 ? 's' : '');
    }

    // Si la diferencia es menos de 1 día
    if ($diff < 86400) { // 24 horas
        $hours = floor($diff / 3600);
        return "Hace $hours hora" . ($hours > 1 ? 's' : '');
    }

    // Si la diferencia es menos de 2 días (y decir "ayer")
    if ($diff < 172800) { // 2 días
        return 'Ayer';
    }

    // Si la diferencia es menos de 7 días
    if ($diff < 604800) { // 7 días
        $days = floor($diff / 86400);
        return "Hace $days día" . ($days > 1 ? 's' : '');
    }

    // Si la diferencia es menos de 30 días (aproximadamente un mes)
    if ($diff < 2592000) { // 30 días
        $weeks = floor($diff / 604800);
        return "Hace $weeks semana" . ($weeks > 1 ? 's' : '');
    }

    // Si la diferencia es menos de un año
    if ($diff < 31536000) { // 365 días
        $months = floor($diff / 2592000);
        return "Hace $months mes" . ($months > 1 ? 'es' : '');
    }

    // Si la diferencia es más de un año
    $years = floor($diff / 31536000);
    return "Hace $years año" . ($years > 1 ? 's' : '');
}

function getPermisoStatusMessage($status) {
    if ($status === 4) {
        return 'Tu permiso fue aprobado';
    } else if ($status === 5) {
        return 'Tu permiso fue rechazado';
    }
}
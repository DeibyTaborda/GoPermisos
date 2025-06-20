<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../const/private.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host = CORREO_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = CORREO;
        $this->mailer->Password = CORREO_PASS;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Encoding = 'base64';
    }

    public function cargarPlantilla($plantilla, $datos) {
        $contenido = file_get_contents("{$plantilla}.html");

        foreach ($datos as $clave => $valor) {
            $contenido = str_replace("{{{$clave}}}", $valor, $contenido);
        }

        return $contenido;
    }

    public function enviarCorreo($destinatario, $asunto, $plantilla, $datos, $imagen) {
        try {
            $this->mailer->setFrom(CORREO, 'Sistema de permisos');
            $this->mailer->addAddress($destinatario);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $asunto;

            $contenidoCorreo = $this->cargarPlantilla($plantilla, $datos);
            $this->mailer->Body = $contenidoCorreo;

            $this->mailer->addEmbeddedImage($imagen, 'portada', 'logo.png');

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            return "Error al enviar el correo: {$this->mailer->ErrorInfo}";
        }
    }
}
?>

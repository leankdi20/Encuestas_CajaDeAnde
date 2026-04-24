<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require "phpmailer/Exception.php";
require "phpmailer/PHPMailer.php";
require "phpmailer/SMTP.php";

class Correo {

  public static function enviarCorreo($receptor, $titulo, $mensaje) {
    $phpmailer = new PHPMailer();

    $phpmailer->Username = "notificacion_formulario@cajadeande.fi.cr";
    //$phpmailer->SMTPDebug = 3;  // Opciones 0, 1, 2
    $phpmailer->SMTPSecure = true;
    $phpmailer->SMTPAutoTLS = false;
    $phpmailer->Host = "172.16.8.252";
    $phpmailer->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
    $phpmailer->Port = 25;
    $phpmailer->IsSMTP(); // use SMTP
    $phpmailer->SMTPAuth = false;
    $phpmailer->CharSet = "utf-8";
    $phpmailer->setFrom("notificacion_formulario@cajadeande.fi.cr", "Informes");
    $phpmailer->AddAddress($receptor); 
    $phpmailer->Subject = $titulo;
    $phpmailer->Body = $mensaje;
    $phpmailer->IsHTML(true);

    if(!$phpmailer->Send()) {
      return $phpmailer->ErrorInfo;
    } else {
      return "";
    }
  }

}

?>

<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require "clases/phpmailer/Exception.php";
require "clases/phpmailer/PHPMailer.php";
require "clases/phpmailer/SMTP.php";

$phpmailer = new PHPMailer();

    $phpmailer->Username = "notificacion_formulario@cajadeande.fi.cr";
    //$phpmailer->Password = "123123";
    $phpmailer->SMTPDebug = 3;  // Opciones 0, 1, 2
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
    $phpmailer->setFrom("notificacion_formulario@cajadeande.fi.cr", "Informes Prueba");
    $phpmailer->AddAddress("ATENCION_SEGUROS_GENERALES@cajadeande.fi.cr"); 
    $phpmailer->Subject = "Prueba desde Caja de ANDE";
    $phpmailer->Body = "Prueba!";
    $phpmailer->IsHTML(true);

    if(!$phpmailer->Send()) {
      echo "ERROR <br>" . $phpmailer->ErrorInfo;
    } else {
      return "";
    }
?>

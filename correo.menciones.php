<?php
      include("php/phpmailer/class.phpmailer.php");
      include("php/phpmailer/class.smtp.php");
      $fecha=date("d/m/Y")." a las ".date("h:i:sa");
      $mail = new PHPMailer();
      $body = "<a href=\"http://www.plaxed.com/u/$usr_alias\">$usr_alias</a> te ha etiquetado en una <a href=\"http://www.plaxed.com/p/$nuevoId\">publicación</a>.";
      $body.= "<br><br><br><a href=\"http://www.plaxed.com/\">Plaxed.com</a> - Todos los Derechos Reservados ".date("Y");
      $body.= "<br><br><h6>Enviado: $fecha</h6>";
      $mail->Mailer = "smtp";
      $mail->IsSMTP(); 
      $mail->CharSet = "UTF-8";
      $mail->Port = 465; // 26 o 465
      // si el SMTP necesita autenticación
      $mail->SMTPAuth = true;
      //$mail->SMTPSecure = "ssl"; //quito esto?

      // credenciales usuario
      $mail->Username = "plaxedco";
      $mail->Password = "501878Plaxed+";
      $mail->Host = "ssl://box696.bluehost.com";
      $mail->From = "no-responder@plaxed.com";
      $mail->FromName = "Notificación Plaxed";
      $mail->Subject = "$usr_alias te ha etiquetado";
      $mail->AltBody = "Prueba de cuerpo alternativo"; 
      $mail->MsgHTML($body);
      $mail->AddAddress("$rs_um[2]", "$rs_um[3]");
      if($mail->Send()) {
          // se envio
      } else {
          // no se envio
      }
?>                        
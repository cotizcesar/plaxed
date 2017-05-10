<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    if (isset($_POST['formulariorecuperar'])){
        $correo = $_POST['correo'];
        if (empty($correo)){
            echo json_encode(array("respuesta"=>"error","mensaje"=>"Error! Los datos son incorrectos."));
            exit();
        }
        $r=mysql_query("SELECT correo, usuario_activo, usuario_confirmado FROM usuario WHERE correo='$correo'");
        if (mysql_num_rows($r)==1){
            $rs=mysql_fetch_array($r);
            if ($rs[1]==0){
                echo json_encode(array("respuesta"=>"error","mensaje"=>"El usuario está inhabilitado."));
                exit();
            }
            $dato1 = sha1(cadenaAzar());
            $dato2 = sha1(cadenaAzar());
            $fecha = date("Y-m-d H:i:s");
            mysql_query("DELETE FROM solicitudes_pendientes WHERE correo='$correo' and tipo='cambio_clave'");
            mysql_query("INSERT INTO solicitudes_pendientes (correo,dato1,dato2,tipo,fecha) VALUES ('$correo','$dato1','$dato2','cambio_clave','$fecha')");
            if (!$plaxed->APP->esLocal()){
                //Si no es Local, enviamos el correo.
                if (!mysql_error()){
                    $cuerpo = "Estás recibiendo este correo porque solicitaste la recuperación de tu contraseña. No podemos recuperar tu contraseña anterior, pero podemos generar una nueva. Por favor sigue el enlace que aparece debajo para continuar con el proceso:<br><br>";
                    $cuerpo.= "<a href=\"http://www.plaxed.com/generar-clave/$correo/$dato1/$dato2\">http://www.plaxed.com/confirmar/$correo/$dato1/$dato2</a>";
                    $notiRegistro = new NotificacionCorreo();
                    $notiRegistro->Asunto("Plaxed: Recuperación de Contraseña");
                    $notiRegistro->CorreoOrigen("donotreply@plaxed.com");
                    $notiRegistro->NombreOrigen("Plaxed");
                    $notiRegistro->Cuerpo($cuerpo);
                    $notiRegistro->AgregarDestino($correo, $nombre);
                    if (!$notiRegistro->Enviar()){                      
                        $ar=fopen("logEmail.txt","a");
                        fputs($ar,$notiRegistro->ErrorInfo);
                        fputs($ar,"\n");
                        fclose($ar);
                    }

                }           
            }
            echo json_encode(array("respuesta"=>"ok","mensaje"=>"Se ha envíado un correo con las instrucciones de recuperación."));
            exit();
        }
        else{
            echo json_encode(array("respuesta"=>"error","mensaje"=>"Error! El correo no está registrado."));
            exit();
        }
        exit();
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Plaxed.com</title>
    <meta property="og:image" content="http://www.plaxed.com/images/template/opengraph.png"/>    
    <meta property="og:title" content="Plaxed - La Primera Red Social Venezolana"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="http://www.plaxed.com"/>
    <meta property="og:site_name" content="Plaxed"/>
    <meta property="fb:admins" content="cotizcesar,JesusRamonCS"/>
    <meta property="og:description"
          content="Plaxed es una red social en la que compartes actualizaciones de estado, fotos, vídeos y muchas cosas más. Todo eso en 200 caracteres."/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="ROBOTS" content="NOODP" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <base href="<?php echo $SERVIDOR; ?>" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <LINK href="css/login.css" rel=STYLESHEET TYPE=text/css>
    <script type="text/javascript" src="js/spval-1.js"></script>
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript">
        function validame(f){
            var valForm = supervalidacion(f);
            if (valForm){
                bloquearForm(true);
                var datos = $('#form-recuperar').serialize();
                $.ajax({
                    url: './recuperar',
                    type: 'POST',
                    data: datos,
                    success: function(data, textStatus, xhr){
                        var resp = $.parseJSON(data);  
                        alert(resp.mensaje);                      
                        if (resp.respuesta=="error"){                            
                            bloquearForm(false);
                            $('#correo').focus();
                        }
                        else{
                            location.href='./';
                        }
                    },
                    error: function(xhr, textStatus, errorThrown){
                        bloquearForm(false);
                        alert('Ha ocurrido un error! Intente de nuevo.')
                    }

                });
            }
            return false;
        }
        function bloquearForm(param){
            if (param){
                $('#correo').attr('readonly','readonly');   
                $('#submit').attr('disabled','disabled');   
            }
            else{
                $('#correo').removeAttr('readonly');    
                $('#submit').removeAttr('disabled');    
            }            
        }
    </script>
</head>
<body>
    <div class="header">
        <div class="harea">
            <div class="logo">
                <a href="#"><img src="images/site/logo300.png"></a>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="carea">
            <div class="omnibox">
                <form action="#" method="post" onsubmit="return validame(this);" id="form-recuperar">
                    <input type="hidden" name="formulariorecuperar">
                    <p>Si olvidaste tu contraseña, ingresa tu correo electrónico para que recibas las instrucciones de recuperación...</p>
                    <br>
                    <p>Correo Electrónico</p>
                    <input type="text" class="nombre" name="correo" id="correo" maxlength="100" spval="*|@" placeholder="Correo electrónico">                    
                    <input type="submit" class="send" value="Recuperar" id="submit">
                </form>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="farea">
            <div class="plx">
                Plaxed.com - Todos los derechos reservados 2010-2012.<br>
                Hecho con amor desde Mérida y Maracaibo.                
            </div>
            <div class="socialr">
                <a href="http://on.fb.me/OuMMsB" target="_blank"><img src="images/site/facebook_32.png"></a>
                <a href="http://bit.ly/PypgH0" target="_blank"><img src="images/site/twitter_32.png"></a>
                <a href="http://bit.ly/Rpxjsl" target="_blank"><img src="images/site/youtube_32.png"></a>
            </div>
        </div>
    </div>
</body>
</html>
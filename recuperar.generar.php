<?php
    if (isset($_POST['formulariogclave'])){
    	
        $clave1 = $_POST['clave1'];
        $clave2 = $_POST['clave2'];
        $correo = $_POST['correo'];
        $dato1 = $_POST['dato1'];
        $dato2 = $_POST['dato2'];
        $clave1=trim($clave1);
        $clave2=trim($clave2);
        $solicitudId = 0;
        $r_val = mysql_query("SELECT solicitudes_pendientes_id, correo, dato1, dato2 FROM solicitudes_pendientes WHERE correo='$correo' and dato1='$dato1' and dato2='$dato2' and tipo='cambio_clave'");
        if (mysql_num_rows($r_val)==1){
			$rs_val = mysql_fetch_array($r_val);
			$solicitudId = $rs_val[0];
		}
		else{
			echo json_encode(array("respuesta"=>"error","mensaje"=>"La solicitud no existe."));
    		exit();
		}
		if (empty($clave1) || empty($clave2)){
        	echo json_encode(array("respuesta"=>"error","mensaje"=>"Debe introducir la contraseña y confirmarla."));
            exit();
        }
		if ($clave1!=$clave2){
        	echo json_encode(array("respuesta"=>"error","mensaje"=>"Las contraseñas no coinciden."));
            exit();
        }
		$clave=sha1(md5($clave1));
        mysql_query("DELETE FROM solicitudes_pendientes WHERE solicitudes_pendientes_id='$solicitudId'");
        mysql_query("UPDATE usuario SET clave='$clave' WHERE correo='$correo'");
        $r=mysql_query("SELECT usuario_id FROM usuario WHERE correo='$correo'");
        $rs=mysql_fetch_array($r);
        $_SESSION['xyz12345_conectado']="esto_es_para_navegar";
        $_SESSION['xyz12345_id']=$rs[0];
        //
        echo json_encode(array("respuesta"=>"ok","mensaje"=>"La contraseña ha sido cambiada."));
        exit();
    }
    else{
    	$correo = isset($parametros[0]) ? $parametros[0] : "";
		$dato1 = isset($parametros[1]) ? $parametros[1] : "";
		$dato2 = isset($parametros[2]) ? $parametros[2] : "";
		$r_val = mysql_query("SELECT solicitudes_pendientes_id, correo, dato1, dato2 FROM solicitudes_pendientes WHERE correo='$correo' and dato1='$dato1' and dato2='$dato2' and tipo='cambio_clave'");
		if (mysql_num_rows($r_val)!=1){
			header("location: $SERVIDOR");
			exit();
		}
		//$rs_val = mysql_fetch_array($r_val);
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Plaxed.com</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="ROBOTS" content="NOODP" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <base href="<?php echo $SERVIDOR; ?>">
    <LINK href="css/login.css" rel=STYLESHEET TYPE=text/css>
    <script type="text/javascript" src="js/spval-1.js"></script>
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript">
        function validame(f){
            var valForm = supervalidacion(f);
            if (valForm){
                bloquearForm(true);
                var datos = $('#form-clave').serialize();
                $.ajax({
                    url: './generar-clave',
                    type: 'POST',
                    data: datos,
                    success: function(data, textStatus, xhr){
                        var resp = $.parseJSON(data);                        
                        alert(resp.mensaje);
                        if (resp.respuesta=="error"){
                            bloquearForm(false);
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
                $('#clave1').attr('readonly','readonly');   
                $('#clave2').attr('readonly','readonly');  
            }
            else{
                $('#clave1').removeAttr('readonly');    
                $('#clave2').removeAttr('readonly');
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
				<form action="#" method="post" onsubmit="return validame(this);" id="form-clave">
                    <input type="hidden" name="formulariogclave">
                    <input type="hidden" name="correo" id="correo" value="<?php echo $correo; ?>">
                    <input type="hidden" name="dato1" id="dato1" value="<?php echo $dato1; ?>">
                    <input type="hidden" name="dato2" id="dato2" value="<?php echo $dato2; ?>">
                    <p>Ingrese la nueva contraseña y confírmela para finalizar con el proceso de recuperación:</p>
                    <br>
					<p>Nueva Contraseña</p>
					<input type="password" class="nombre" name="clave1" id="clave1" maxlength="15" spval="*" placeholder="Nueva Contraseña">
					<p>Confime Contraseña</p>
					<input type="password" class="password" name="clave2" id="clave2" maxlength="15" spval="*|eq(clave1)" placeholder="Confirme Contraseña">
					<input type="submit" class="send" value="Cambiar">
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

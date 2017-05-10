<?php
	include("php/recaptchalib.php");
	$publickey = "6Lf03NcSAAAAAKNFVR208fcVuOPuhQ42p6Hbfgcl"; // you got this from the signup page
	if (isset($_POST['formuarlioregistro'])){
		$privatekey = "6Lf03NcSAAAAAIEwFlqx66dvzkGFBZZeZXCPTW-s";
		$captchaServidor = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "";
		$captchaChallenge = (isset($_POST["recaptcha_challenge_field"])) ? $_POST["recaptcha_challenge_field"] : "";
		$captchaResponse = (isset($_POST["recaptcha_response_field"])) ? $_POST["recaptcha_response_field"] : "";
		$resp = recaptcha_check_answer ($privatekey,
                                $captchaServidor,
                                $captchaChallenge,
                                $captchaResponse);
		
		if (!$registroAbierto){
            echo json_encode(array("respuesta"=>"error","mensaje"=>"El registro se encuentra inhabilitado. Estamos haciendo cambios. Regresa más tarde.", "codigo"=>"0"));
            exit();
        }

		if (!$resp->is_valid){
			echo json_encode(array("respuesta"=>"error","mensaje"=>"El código no coincide.","codigo"=>"1"));
			exit();
		}
		
    	$usuario = $_POST['usuario'];
    	$usuario=trim($usuario);
    	if (empty($usuario)){
    		echo json_encode(array("respuesta"=>"error","mensaje"=>"El campo Usuario es obligatorio.","codigo"=>"3"));
			exit();
    	}
        $clave1 = $_POST['clave1'];
        $clave1=trim($clave1);
        $clave2 = $_POST['clave2'];
		$clave2=trim($clave2);
		if ($clave1!=$clave2){
			echo json_encode(array("respuesta"=>"error","mensaje"=>"Las contraseñas no coinciden.","codigo"=>"4"));
			exit();
		}
		if (empty($clave1)){
			echo json_encode(array("respuesta"=>"error","mensaje"=>"La contraseña no puede ser vacía.","codigo"=>"5"));
			exit();
		}
		$claveEncriptada=sha1(md5($clave1));        	
        $nombre = $_POST['nombre'];
        $nombre = trim($nombre);
        if (empty($nombre)){
			echo json_encode(array("respuesta"=>"error","mensaje"=>"El campo Nombre es obligatorio.","codigo"=>"6"));
			exit();
		}
        $correo1 = $_POST['correo1'];
        $correo2 = $_POST['correo2'];
        $correo1 = trim($correo1);
        $correo2 = trim($correo2);

        if ($correo1!=$correo2){
        	echo json_encode(array("respuesta"=>"error","mensaje"=>"Los Correos no coinciden.","codigo"=>"7"));
			exit();
        }
        if (empty($correo1)){
			echo json_encode(array("respuesta"=>"error","mensaje"=>"El campo Correo es obligatorio.","codigo"=>"8"));
			exit();
		}
		$correo = $correo1;
		$sexo = $_POST['sexo'];
		if ($sexo=="-")
			$sexo="";
        if (empty($sexo)){
			echo json_encode(array("respuesta"=>"error","mensaje"=>"El campo Sexo es obligatorio.","codigo"=>"9"));
			exit();
		}
		$fechad = $_POST['fndia'];
		if ($fechad=="-")
			$fechad="";
		$fecham = $_POST['fnmes'];
		if ($fecham=="-")
			$fecham="";
		$fechaa = $_POST['fnano'];
		if ($fechaa=="-")
			$fechaa="";
        if (empty($fechad) || empty($fecham) || empty($fechaa)){
			echo json_encode(array("respuesta"=>"error","mensaje"=>"La Fecha de Nacimiento es obligatoria.","codigo"=>"10"));
			exit();
		}

		$usuariosReservados = array("admin", "administrador", "redplaxed");
		$tmpUsr = strtolower($usuario);
        if (in_array($tmpUsr, $usuariosReservados)){
        	echo json_encode(array("respuesta"=>"error","mensaje"=>"El Usuario no está disponible.","codigo"=>"14"));
			exit();
        }

		$fechaNac = $fechaa."-".$fecham."-".$fechad;
        $fecha=date("Y-m-d H:i:s");

        $r_al=mysql_query("SELECT alias FROM usuario WHERE alias='$usuario'");
        if (mysql_num_rows($r_al)>0){
        	echo json_encode(array("respuesta"=>"error","mensaje"=>"El Usuario ya está registrado.","codigo"=>"11"));
			exit();
        }
        $r_co=mysql_query("SELECT correo FROM usuario WHERE correo='$correo'");
        if (mysql_num_rows($r_co)>0){
            echo json_encode(array("respuesta"=>"error","mensaje"=>"El Correo ya está registrado.","codigo"=>"12"));
			exit();
        }
        $dominio = explode("@",$correo);
        $r_do=mysql_query("SELECT dominio FROM dominios_invalidos WHERE dominio='$dominio[1]'");
        if (mysql_num_rows($r_do)>0){
            echo json_encode(array("respuesta"=>"error","mensaje"=>"El Correo posee un dominio no permitido.","codigo"=>"15"));
			exit();
        }

        $r=mysql_query("INSERT INTO usuario (usuario_activo,usuario_confirmado,alias,clave,nombre,correo,fecha_registro,sexo,fecha_nacimiento) 
        			VALUES ('0','0','$usuario','$claveEncriptada','$nombre','$correo','$fecha', '$sexo', '$fechaNac')");
        
        if (!mysql_error()){
        	$dato1 = sha1(cadenaAzar());
        	$dato2 = sha1(cadenaAzar());

        	mysql_query("INSERT INTO solicitudes_pendientes (correo,dato1,dato2,tipo,fecha) VALUES ('$correo','$dato1','$dato2','confirmar_cuenta','$fecha')");
        	
        	if (!$plaxed->APP->esLocal()){
        		//Si no es Local, enviamos el correo.
        		if (!mysql_error()){
        			$cuerpo = "Estás un paso de formar parte de nuestra Red Plaxed. Por favor sigue el link que aparece a continuación:<br><br>";
        			$cuerpo.= "<a href=\"http://www.plaxed.com/confirmar/$correo/$dato1/$dato2\">http://www.plaxed.com/confirmar/$correo/$dato1/$dato2</a>";
        			$notiRegistro = new NotificacionCorreo();
	        		$notiRegistro->Asunto("Registro Plaxed: Confirmación");
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
        	
        	echo json_encode(array("respuesta"=>"ok","mensaje"=>"El Usuario ha sido registrado.\nSe ha enviado un correo a $correo para validar su cuenta.","codigo"=>"13"));
			exit();
        }
        else{
        	echo json_encode(array("respuesta"=>"error","mensaje"=>"Ocurrió un error! Es posible que no se haya completado el registro.","codigo"=>"12"));
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
    <LINK href="css/registro.css" rel=STYLESHEET TYPE=text/css>
    <script type="text/javascript" src="js/spval-1.js"></script>
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript">
    	function validame(f){
    		$('#recaptcha_response_field').attr('spval','*');
    		var valForm = supervalidacion(f);
    		if (valForm){
    			//bloquearForm(true);
    			var datos = $('#fregistro').serialize();
    			$.ajax({
    				url: './registro',
    				type: 'POST',
    				data: datos,
	    			success: function(data, textStatus, xhr){
	    				var resp = $.parseJSON(data);
	    				alert(resp.mensaje);
	    				if (resp.respuesta=="error"){
	    					bloquearForm(false);
	    					if (resp.codigo==1){
	    						Recaptcha.reload();	    						
	    					}
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
    			$('#nombre').attr('readonly','readonly');	
    			$('#usuario').attr('readonly','readonly');	
    			$('#correo1').attr('readonly','readonly');	
    			$('#correo2').attr('readonly','readonly');	
    			$('#sexo').attr('readonly','readonly');	
    			$('#fndia').attr('readonly','readonly');	
    			$('#fnmes').attr('readonly','readonly');	
    			$('#fnano').attr('readonly','readonly');	
    			$('#clave1').attr('readonly','readonly');	
    			$('#clave2').attr('readonly','readonly');	
    			$('#recaptcha_response_field').attr('readonly','readonly');	
    		}
    		else{
    			$('#nombre').removeAttr('readonly');	
    			$('#usuario').removeAttr('readonly');	
    			$('#correo1').removeAttr('readonly');	
    			$('#correo2').removeAttr('readonly');		
    			$('#sexo').removeAttr('readonly');	
    			$('#fndia').removeAttr('readonly');		
    			$('#fnmes').removeAttr('readonly');		
    			$('#fnano').removeAttr('readonly');		
    			$('#clave1').removeAttr('readonly');		
    			$('#clave2').removeAttr('readonly');	
    			$('#recaptcha_response_field').removeAttr('readonly');	
    		}
    		
    	}
    </script>
</head>
<body>
	<div class="header">
		<div class="harea">
			<div class="logo">
				<a href="./"><img src="images/site/logo300.png"></a>
			</div>

		</div>
	</div>
	<div class="content">
		<div class="carea">
			<div class="regbox">
				<form id="fregistro" action="#" method="post" onsubmit="return validame(this);">
					<input type="hidden" name="formuarlioregistro">
					<p>Nombre y Apellido</p>
					<input type="text" name="nombre" id="nombre" maxlength="30" spval="*" placeholder="Nombre y Apellido">
					<p>Nombre de Usuario</p>
					<input type="text" name="usuario" id="usuario" maxlength="15" placeholder="Nombre de Usuario" spval="*|eval(/^[a-zñÑ][a-z0-9_ñÑ]+$/i)" msjError="Ingrese un nombre de usuario válido. Solo se permiten letras, números y el guión bajo, pero debe empezar con una letra y tener al menos 4 caracteres. Ejemplo: jose, luis24, el_mejor, el_gran_20">
					<p>Correo Electrónico</p>
					<input type="text" name="correo1" id="correo1" maxlength="100" spval="*|@" placeholder="Correo Electrónico">
					<p>Confirmar Correo</p>
					<input type="text" name="correo2" id="correo2" maxlength="100" spval="*|@|eq(correo1)" placeholder="Confirmar Correo">
					<p>Sexo</p>
					<select class="sexo" name="sexo" id="sexo" spval="!-">
						<option value="-">Selecciona tu sexo</option>
						<option value="h">Hombre</option>
						<option value="m">Mujer</option>
					</select>
					<p>Fecha de Nacimiento</p>
						<select class="fedena" name="fndia" id="fndia" spval="!-">
						<option value="-">Día</option>
						<?php
							for ($i=1; $i<=31; $i++){
								$dia = $i;
								if (strlen($dia)==1)
									$dia="0".$dia;
								echo "<option value=\"$dia\">$dia</option>";

							}
						?>
					</select>
					<select class="fedena" name="fnmes" id="fnmes" spval="!-">
						<option value="-">Mes</option>
						<?php
							for ($i=1; $i<=12; $i++){
								$meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
								$mes = $i;
								$nMes = $meses[$mes];
								if (strlen($mes)==1)
									$mes="0".$mes;
								echo "<option value=\"$mes\">$nMes</option>";

							}
						?>
					</select>
					<select class="fedena" name="fnano" id="fnano" spval="!-">
						<option value="-">Año</option>
						<?php
							for ($i=date("Y")-12; $i>=1930; $i--){
								$ano = $i;
								echo "<option value=\"$ano\">$ano</option>";

							}
						?>
					</select>
					<p class="contra">Contraseña</p>
					<input type="password" name="clave1" id="clave1" maxlength="15" spval="*" placeholder="Contraseña">
					<p>Confirmar Contraseña</p>
					<input type="password" name="clave2" id="clave2" maxlength="15" spval="*|eq(clave1)" placeholder="Confirme Contraseña">
					<p>Ingrese el Código</p>
					<?php
						echo recaptcha_get_html($publickey);
					?>
					<input type="submit" class="send" value="Registrarse">
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
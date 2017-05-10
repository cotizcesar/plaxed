<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    if (isset($_POST['formulariologin'])){
        $usuario = $_POST['usuario'];
        $clave = $_POST['clave'];
        $usuario=trim($usuario);
        $clave=trim($clave);
        $clave=sha1(md5($clave));
        if (empty($usuario) || empty($clave)){
            echo json_encode(array("respuesta"=>"error","mensaje"=>"Error! Los datos son incorrectos."));
            exit();
        }
        $r=mysql_query("SELECT alias, clave, usuario_id, usuario_activo, usuario_confirmado FROM usuario WHERE alias='$usuario'");
        if (mysql_num_rows($r)==1){
            $rs=mysql_fetch_array($r);
            if ($rs[4]!=1){
                echo json_encode(array("respuesta"=>"error","mensaje"=>"El usuario no ha sido confirmado."));
                exit();
            }
            if ($rs[3]!=1){
                echo json_encode(array("respuesta"=>"error","mensaje"=>"El usuario se encuentra inactivo."));
                exit();
            }
            if ($clave==$rs[1]){
                $_SESSION['xyz12345_conectado']="esto_es_para_navegar";
                $_SESSION['xyz12345_id']=$rs[2];
                echo json_encode(array("respuesta"=>"ok","mensaje"=>"Los datos son correctos."));
                exit();      
            }
            else{
                echo json_encode(array("respuesta"=>"error","mensaje"=>"Error! Los datos son incorrectos."));
                exit();
            }
        }
        else{
            echo json_encode(array("respuesta"=>"error","mensaje"=>"Error! El usuario no existe."));
            exit();
        }
        exit();
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml">
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
                var datos = $('#form-ingreso').serialize();
                $.ajax({
                    url: './',
                    type: 'POST',
                    data: datos,
                    success: function(data, textStatus, xhr){
                        var resp = $.parseJSON(data);
                        
                        if (resp.respuesta=="error"){
                            alert(resp.mensaje);
                            bloquearForm(false);
                        }
                        else{
                            location.reload();
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
                $('#usuario').attr('readonly','readonly');   
                $('#clave').attr('readonly','readonly');  
            }
            else{
                $('#usuario').removeAttr('readonly');    
                $('#clave').removeAttr('readonly');
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
			<a class="regist" href="./registro">Regístrate aquí</a>
			<a class="olvido" href="./recuperar">¿Olvidó su contraseña?</a>
		</div>
	</div>
	<div class="content">
		<div class="carea">
			<div class="omnibox">
				<form action="#" method="post" onsubmit="return validame(this);" id="form-ingreso">
                    <input type="hidden" name="formulariologin">
					<p>Nombre de Usuario</p>
					<input type="text" class="nombre" name="usuario" id="usuario" maxlength="15" spval="*" placeholder="Nombre de Usuario">
					<p>Contraseña</p>
					<input type="password" class="password" name="clave" id="clave" maxlength="15" spval="*" placeholder="Contraseña">
					<input type="submit" class="send" value="Iniciar sesión">
				</form>
				<div class="clear"></div>
			</div>
			<div class="pplbox">
				<p>¿Quiénes están en Plaxed?</p>
				<?php 
					$r=mysql_query("SELECT usuario_id FROM usuario WHERE avatar=1 ORDER BY RAND() DESC LIMIT 16");
					while ($rs=mysql_fetch_array($r)){
						echo "<a href=\"javascript:;\"><img src=\"images/users/user-".$rs[0]."-48x48.png\">\n</a>";
					}
				?>
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
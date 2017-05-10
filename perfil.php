<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    $idPerfil = 0;
    $r_perfil=mysql_query("SELECT usuario_id FROM usuario WHERE alias='$parametros[0]'");
    if (mysql_num_rows($r_perfil)==1){
        $rs_perfil = mysql_fetch_array($r_perfil);
        $idPerfil = $rs_perfil[0];
    }
    else{
        header("Location: $SERVIDOR");
        exit();
    }

    // Se obtienen los datos del perfil
    $usrPerfil = $plaxed->obtenerSesion($idPerfil);

    // Si existe bloque en cualquier direccion, se redirecciona al home
    $r_bloq = mysql_query("SELECT * FROM bloqueo WHERE (usuario_origen_id='$sesion->id' AND usuario_destino_id='$usrPerfil->id') OR (usuario_origen_id='$usrPerfil->id' AND usuario_destino_id='$sesion->id')");
    if (mysql_num_rows($r_bloq)>0){
        header("location: $SERVIDOR");
        exit();
    }

    $tagsPerfil="";
    $imgPerfil="";
    if ($idPerfil!=0){
        $tags = $usrPerfil->tags;
        if (!empty($tags)){
            $tags = explode(",", $tags);
            foreach ($tags as $tag){
                if (!empty($tagsPerfil))
                    $tagsPerfil.=" ";
                $tagsPerfil.="<a href=\"./tema/$tag\">#$tag</a>";
            }
        }     
        $imgPerfil = "./images/users/user-".$idPerfil."-perfil.png";
        if (!file_exists($imgPerfil)){
            $imgPerfil = "./images/users/user-160x160.png";
        }
    }
    
    $accionConexion = "conectar";
    $r_cact = mysql_query("SELECT conexion_id FROM conexion WHERE (usuario1_id='$sesion->id' AND usuario2_id='$usrPerfil->id') OR (usuario1_id='$usrPerfil->id' AND usuario2_id='$sesion->id')");
    if (mysql_num_rows($r_cact)>0){    
        $accionConexion = "desconectar";
    }
    else{
        $r_sol = mysql_query("SELECT estado FROM solicitud WHERE usuario_origen_id='$sesion->id' AND usuario_destino_id='$usrPerfil->id' AND estado='1'");
        if (mysql_num_rows($r_sol)>0){
            $accionConexion = "cancelar";
        }
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
    <base href="<?php echo $SERVIDOR; ?>" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <LINK href="css/main.css" rel="STYLESHEET" TYPE="text/css">
    <LINK href="css/perfil.css" rel="STYLESHEET" TYPE="text/css">
    <LINK href="js/jqueryui/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="STYLESHEET" TYPE="text/css">        
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/funciones.generales.js"></script>
    <script type="text/javascript" src="js/timeline.v3.js"></script>
    <script type="text/javascript" src="js/spval-1.js"></script>
    <script type="text/javascript" src="js/html5placeholder.jquery.js"></script>
    <script type="text/javascript" src="js/jquery.tinyscrollbar.min.js"></script>
    <script type="text/javascript" src="js/directorio.php"></script>
    <script type="text/javascript" src="js/jqueryui/js/jquery-ui-1.8.23.custom.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.triggeredAutocomplete.js"></script>
    <script src="js/upload.js"></script>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="hbox">
                <div class="logo">
                    <a class="sprite" href="./"></a>
                </div>
                <div id="div-barra-menu">
                    <div class="m-salir" title="Salir">
                        <a class="sprite" href="./salida"></a>
                    </div>
                    <div class="m-opciones" title="Opciones">
                        <a class="sprite" href="./opciones"></a>
                    </div>
                    <div class="m-mp" title="Mensajes Privados">
                        <a class="sprite" href="./mp"></a>
                    </div>
                    <div class="m-etiquetas" title="Mis Etiquetas">
                        <a class="sprite" href="./etiquetas"></a>
                    </div>
                    <div class="m-perfil" title="Mi Perfil">
                        <a class="sprite" href="./u/<?php echo $sesion->alias; ?>"></a>
                    </div>
                    <div class="m-publicar" title="Publicar">
                        <a class="sprite" href="javascript:;" id="lnkPublicarMensaje"></a>
                    </div>
                    <div class="m-home" title="Inicio">
                        <a class="sprite" href="./"></a>
                    </div>    
                    <div class="search">
                        <input type="text" id="busqueda" name="busqueda" placeholder="@usuario, palabra, #tema"> 
                    </div>
                </div>                
                <div class="rmen" id="div_aviso_mensajes">
                    <a href="javascript:;" title="Nuevos Mensajes">0</a>
                </div>
                                <div class="not" title="<?php echo "$usr_num_notificaciones notificaciones no leídas"; ?>">
                    <a href="javascript:;" id="lnkAbrirNotificaciones"><span id="span_c_notificaciones"><?php echo $lbl_num_notificaciones; ?></span></a>
                </div>
                            </div>        
        </div>
        <div class="content">
			<div class="pblock">
				<div class="avatar160">
					<img src="<?php echo $imgPerfil; ?>" width="160" height="160">
				</div>
				<div class="dadeus">
					<div class="nodeus"><?php echo $usrPerfil->nombre; ?></div>
					<div class="usuari">@<?php echo $usrPerfil->alias; ?></div>
					<div class="etdeus"><?php echo $tagsPerfil; ?></div>
					<div class="ubdeus">
						<div class="udufon"><img src="images/template/location.png"><?php echo $usrPerfil->ubicacion; ?></div>
					</div>
					<div class="biodeu"><?php echo texto_a_url($usrPerfil->biografia); ?></div>
				</div>
				<div class="medeus" id="medeus">
                    <?php if ($usrPerfil->id == $sesion->id): ?>
					<a class="edpeus" href="./opciones">Editar perfil</a>
                    <?php else: ?>
                        <?php if ($accionConexion=="conectar"): ?>
                            <a class="conect" id="conect" idusuario="<?php echo $usrPerfil->id; ?>" href="javascript:;" title="Solicitar conexión.">Conectar</a>
                        <?php else: ?>
                            <?php if ($accionConexion=="desconectar"): ?>
                                <a class="descon" id="descon" idusuario="<?php echo $usrPerfil->id; ?>" href="javascript:;">Desconectar</a>
                            <?php else: ?>
                                <a class="cancel" id="cancel" idusuario="<?php echo $usrPerfil->id; ?>" href="javascript:;" title="Cancelar solicitud">Cancelar</a>
                            <?php endif; ?>
					   <?php endif; ?>
                    <?php endif; ?>
				</div>
				<div class="esdeus">
					<div class="botpun"><span><?php echo $usrPerfil->puntos; ?></span> puntos</div>					
					<div class="botcon"><span><?php echo $usrPerfil->conexiones; ?></span> conexiones</div>
					<div class="botpos"><span><?php echo $usrPerfil->posts; ?></span> posts</div>					
				</div>			
			</div>
            <div class="cl" id="cl">
                <?php 
                    include("timeline.publicaciones.php");
                ?>        
                <div class="clear"></div>
            </div>                  
            <div class="cr">
            	<div class="gblock">
                    <div class="ttlbox">Conexiones</div><a class="seem" href="javascript:;">Ver todos</a>   
                    <ul id="ul-conexiones">
                        <?php
                            foreach ($arrAmigos as $am){
                                $amAlias = $am['alias'];
                                $amAvatar = $am['avatar'];
                                echo "<li><a title=\"$amAlias\" href=\"./u/$amAlias\"><img src=\"./images/users/$amAvatar\"></a></li>";
                            }
                        ?>
                    </ul>
                </div>
				<div class="footer">
                	<div class="gblock">
                		Plaxed.com - Todos los derechos reservados 2012<br>
                    	<a href="javascript:;">Sobre nosotros</a>
                    	<a href="javascript:;">Ayuda</a>
                    	<a href="javascript:;">Condiciones de Uso</a>
                    	<a href="javascript:;">Privacidad</a>
                    	<a href="javascript:;">Recursos</a>
                    	<a href="javascript:;">Publicidad</a>
                    	<a href="javascript:;">Desarrolladores</a>
                    </div>	
                </div>
            </div>
            <div id="lmore" class="lmore">
                <a href="javascript:;" id="lnkMasPlaxs">Cargar más plaxs...</a>
            </div>
                        
        </div>
    </div>
    <div id="div-capa-absoluta" class="div_absoluto">
        <div id="div-publicar" class="div-publicar">
            <p>
            <textarea id="text_mensaje" name="text_mensaje" class="mention" placeholder="Escriba su comentario..."></textarea>
            </p>
            <div class="cuenta">
                <span id="span_cuenta">Te quedan 200 caracteres</span>
            </div>
            <div class="div-btn-publicar">
                <input type="button" id="btn-enviar" value="Plaxear!">                
                <div class="div-progress-bar">
                    <div class="div-progress"></div>
                </div>
                <a href="javascript:;" id="link-quitar-foto">Quitar adjunto</a>
                <div class="div-link-foto">
                    <form id="form-upload" action="./?enviarPost" method="POST" enctype="multipart/form-data">
                    <input type="file" id="file-foto" name="archivo"  title="Subir una foto">                    
                    </form>                    
                    <img src="images/template/btn_img.png" title="Subir una foto">
                </div>                
            </div>
        </div>  
        <div id="div-notificaciones" class="div-notificaciones">
            <div id="topnotificaciones">
                <span class="al-left">Notificaciones</span>
                <div class="clear"></div> 
            </div>                      
            <ul id="ul-notificaciones">                
                <?php
                    include("timeline.notificaciones.php");
                ?>          
            </ul>
            <div class="ultimo"><a href="./not">Ver más...</a></div>                    
        </div>
        <div id="div-preview-foto-perfil"></div>
    </div>
    <div class="boxalr" id="boxalr">
        <div class="boxttl">
            <span id="boxalr-ttl"></span>
            <a class="sprite bxclos" href="#"></a>
        </div>
        <div class="clear"></div>
        <div class="boxtxt">
            <span id="boxalr-txt"></span>
        </div>
        <div class="clear"></div>
    </div>
    <div class="boxalr" id="boxqst">
        <div class="boxttl">
            <span id="boxalr-ttl"></span>
            <a class="sprite bxclos" href="#"></a>
        </div>
        <div class="clear"></div>
        <div class="boxtxt">
            <span id="boxalr-txt"></span>
        </div>
        <div class="clear"></div>
        <div class="bxbtns" id="bxbtns"></div>
        <div class="clear"></div>
    </div>
</body>
</html>
<?php
    $busquedaActual="";
    if ($modulo=="buscar"){
        $busquedaActual=$parametros[1];
    }
    $usuarioActual=0;
    if ($modulo=="u"){
        $r_ua=mysql_query("SELECT usuario_id FROM usuario WHERE alias='$parametros[0]'");
        $rs_ua=mysql_fetch_array($r_ua);
        $usuarioActual = $rs_ua[0];
    }
    $temaActual="";
    if ($modulo=="tema"){
        $temaActual = $parametros[1];
    }
?>
<script type="text/javascript">
    $(document).ready(function(){
        jsPlaxed.setUltimoIdPublicacion(<?php echo $ultimoIdPublicacion; ?>);
        jsPlaxed.setPostIdBottom(<?php echo $PostIdBottom; ?>);
        jsPlaxed.setModuloActual('<?php echo $modulo; ?>');
        jsPlaxed.setBusquedaActual('<?php echo ($modulo == "buscar") ? $parametros[0]: "" ?>');
        jsPlaxed.setUsuarioActual(<?php echo ($modulo == "u") ? $usuarioActual: "" ?>);
        jsPlaxed.setConversacionActual(<?php echo $conversacionActual; ?>);
        jsPlaxed.setTemaActual('<?php echo $temaActual; ?>');
        jsPlaxed.iniciar();
    });
</script>
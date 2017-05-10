<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    include("timeline.ajax.php");
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <base href="<?php echo $SERVIDOR; ?>" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <LINK href="css/main.css" rel=STYLESHEET TYPE=text/css>
    <LINK href="js/jqueryui/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel=STYLESHEET TYPE=text/css>        
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
    <a href="javascript:(function(){var titulo=document.title;var ruta=encodeURIComponent(location.href);var win=window.open('http://www.plaxed.com/compartir?ruta='+ruta+'&via=Marcador'+'&contenido='+titulo, '_blank');win.focus();})();" class="bookmarlket" title="Arrastra este enlace a tus marcadores y podrás publicar desde cualquier sitio aquí en Plaxed!">Plaxed</a>
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
                        <a class="sprite" href="./u/<?php echo $usr_alias?>"></a>
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
                <?php
                    if ($modulo!="not"):
                ?>
                <div class="not" title="<?php echo "$usr_num_notificaciones notificaciones no leídas"; ?>">
                    <a href="javascript:;" id="lnkAbrirNotificaciones"><span id="span_c_notificaciones"><?php echo $lbl_num_notificaciones; ?></span></a>
                </div>
                <?php
                    else:
                ?>
                <div class="div-marca-notif">
                    <a id="a-marca-notif" href="javascript:;">Descartar notificaciones</a>
                </div>
                <?php
                    endif;
                ?>
            </div>        
        </div>
        <div class="content">
            <div class="cl" id="cl">
                <?php
                    if ($modulo=="not"){
                        include("notificaciones.php");
                    }
                    else{
                        include("timeline.publicaciones.php");
                    }                    
                ?>
                <div class="clear"></div>
            </div>                  
            <div class="cr">
            	<div class="gblock">
					<div class="cntub last">
						<div class="avatar48"><img src="./images/users/<?php echo $usr_avatar48; ?>"></div>
						<div class="name"><a href="./u/<?php echo $sesion->alias; ?>" class="user-link"><?php echo $usr_nombre; ?></a></div>
						<div class="usern">@<?php echo $usr_alias; ?></div>
						<div class="usern">Puntos Disponibles: <span id="info-puntos"><?php echo $sesion->puntosDisponibles; ?></span></div>
					</div>
					<div class="ustats">
						<span id="span_c_mensajes"><a href="javascript:;"><?php echo $usr_cantidad_mensajes; ?> Publicaciones</a></span>
						<span id="span_c_conexiones"><a href="javascript:;"><?php echo $sesion->conexiones; ?> Panas</a></span>
						<span id="span_c_puntos"><a href="javascript:;"><?php echo $usr_puntos; ?> Puntos</a></span>
					</div>
                </div>
                <?php
                    $numDinamico = rand(1,6);
                    $tituloDinamico = "-";
                    //$numDinamico = 5;
                    switch ($numDinamico) {
                        case 1:
                            $tituloDinamico = "Plaxeros con más Conexiones";
                            break;
                        case 2:
                            $tituloDinamico = "Plaxeros con menos Conexiones";
                            break;
                        case 3:
                            $tituloDinamico = "Plaxeros con más Posts";
                            break;
                        case 4:
                            $tituloDinamico = "Plaxeros con menos Posts";
                            break;
                        case 5:
                            $tituloDinamico = "Plaxeros con más Puntos";
                            break;
                        case 6:
                            $tituloDinamico = "Plaxeros con menos Puntos";
                            break;
                    }
                ?>
            	<div class="gblock">
                	<div class="ttlbox"><?php echo $tituloDinamico; ?></div><a class="seem" href="javascript:;">Ver todos</a>
                        <?php
                            $ORDER = "";
                            $extraWhere = "";
                            if ($numDinamico == 1)
                                $ORDER = "ORDER BY conexiones DESC, fecha_registro";
                            if ($numDinamico == 2){
                                $ORDER = "ORDER BY conexiones, fecha_registro";
                                $extraWhere = " AND conexiones>0";
                            }
                            if ($numDinamico == 3)
                                $ORDER = "ORDER BY posts DESC, fecha_registro";
                            if ($numDinamico == 4){
                                $ORDER = "ORDER BY posts, fecha_registro";
                                $extraWhere = " AND posts>0";
                            }
                            if ($numDinamico == 5)
                                $ORDER = "ORDER BY puntos DESC, fecha_registro";
                            if ($numDinamico == 6)
                                $ORDER = "ORDER BY puntos ASC, fecha_registro";
                            $r_seg=mysql_query("SELECT usuario_id, nombre, alias, puntos, avatar, posts, conexiones FROM usuario WHERE avatar='1' $extraWhere $ORDER LIMIT 5");
                            $contador=0;
                            while ($rs_seg=mysql_fetch_array($r_seg)){
                                $contador++;
                                $seg_avatar48=($rs_seg[4]==1) ? "user-$rs_seg[0]-48x48.png":"user-48x48.png";
                                $conexiones = $rs_seg[6];
                                if ($contador==5){
                                    $classextra=" last";
                                }
                                else{
                                    $classextra="";
                                }
                                //@$rs_seg[2] / $conexiones Panas / $rs_seg[3] Puntos;
                                $infoUser = "";
                                if ($numDinamico == 1)
                                    $infoUser = "$conexiones Conexiones";
                                if ($numDinamico == 2)
                                    $infoUser = "$conexiones Conexiones";
                                if ($numDinamico == 3)
                                    $infoUser = "$rs_seg[5] Publicaciones";
                                if ($numDinamico == 4)
                                    $infoUser = "$rs_seg[5] Publicaciones";
                                if ($numDinamico == 5)
                                    $infoUser = "$rs_seg[3] Puntos";
                                if ($numDinamico == 6)
                                    $infoUser = "$rs_seg[3] Puntos";
                                echo "
                                    <div class=\"cntub$classextra\">
                                        <div class=\"avatar48\"><img src=\"images/users/$seg_avatar48\"></div>
                                        <div class=\"name\"><a class=\"user-link\" href=\"./u/$rs_seg[2]\">$rs_seg[1]</a></div>
                                        <div class=\"usern\">$infoUser</div>
                                        <a class=\"fuser\" userid=\"$rs_seg[0]\" href=\"javascript:;\">Conectar</a>
                                    </div>
                                ";
                            }
                        ?>
                </div>
                <div class="gblock">
                    <div class="ttlbox">Usuarios En Línea</div>
                    <ul id="ul-activos">
                        <?php
                            foreach ($arrUsuariosActivos as $av){
                                $acAlias = $av['alias'];
                                $acAvatar = $av['avatar'];
                                echo "<li><a title=\"$acAlias\" href=\"./u/$acAlias\"><img src=\"./images/users/$acAvatar\"></a></li>";
                            }
                        ?>
                    </ul>
                </div>
				<div class="gblock lomasplax">
					<div class="ttlbox">Lo + plax</div>
					<ol>
						<li><a href="javascript:;">#ZulaSonriePorque</a></li>
						<li><a href="javascript:;">#ElCuraLoSabía</a></li>
						<li><a href="javascript:;">#ZulaQuote</a></li>
						<li><a href="javascript:;">#7O</a></li>
						<li><a href="javascript:;">#YormanTieneOtra</a></li>
						<li><a href="javascript:;">#HOLO</a></li>
						<li><a href="javascript:;">#VengoDelFuturo</a></li>
						<li><a href="javascript:;">#LasBotasDeCafe</a></li>
						<li><a href="javascript:;">#DanielLloraPorDestacados</a></li>
						<li><a href="javascript:;">#ArFinDiez</a></li>
					</ol>
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
            <?php
                    if (!in_array($modulo, array('p','c','not'))):
                ?>
                <div id="lmore" class="lmore">
                    <a href="javascript:;" id="lnkMasPlaxs">Cargar más plaxs...</a>
                </div>
                <?php
                    endif;
                    if ($modulo=="c"){
                        echo "<div id=\"div-linea-bottom\"></div>";
                    }
                ?>        
                <?php
                    if ($modulo=="not"):
                ?>
                <div id="lmore" class="lmore">
                    <a href="javascript:;" id="lnkMasNotificaciones">Cargar más notificaciones...</a>
                </div>
                <?php
                    endif;
                ?>
            <div class="clear"></div>
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
        jsPlaxed.setUsuarioActual(<?php echo ($modulo == "u") ? $usuarioActual : "" ?>);
        jsPlaxed.setConversacionActual(<?php echo $conversacionActual; ?>);
        jsPlaxed.setTemaActual('<?php echo $temaActual; ?>');
        jsPlaxed.iniciar();
        <?php
            if (!empty($postConversacionActual)){
                echo "document.location.hash = '#post-$postConversacionActual';\n";
                echo "
                $('#div_msj_$postConversacionActual').animate({
                    opacity: 0.1
                },800);
                $('#div_msj_$postConversacionActual').animate({
                    opacity: 1
                },800);";
            }
        ?>
        /*
        // Rothe
        $("head").append("<link href='http://leorothe.com/plaxed/happy-birthday/style.css' type='text/css' rel='stylesheet'>"); 
        $(".avatar48, #ul-activos li").css("position", "relative").each(function(){
            var $this = $(this);
            var self = this;
            if ($(this).find(".birthday-hat").length) return;
            var $hat = $('<div class="birthday-hat" />').appendTo(this).hide();
            if ($this.is(".avatar48"))
                $hat.addClass("big");
            else
                $hat.addClass("small");
            $hat.addClass("v"+(Math.ceil(Math.random()*3)));
            setTimeout(function(){
                $hat.show().css("margin-bottom", 2000).animate({marginBottom: 0}, {duration: 600, complete: function(){
                var i = 0,x=Math.PI * 180;
                setInterval(function(){
                    var t = "rotate("+(Math.sin((i+=0.01)*x) * 5)+"deg)";
                    if ("transform" in self.style)
                        self.style.webkitTransform = t;
                    else if ("webkitTransform" in self.style)
                        self.style.webkitTransform = t;
                    else if ("mozTransform" in self.style)
                        self.style.mozTransform = t;
                    else if ("msTransform" in self.style)
                        self.style.msTransform = t;         
                }, 50); 
         
            }});
        }, Math.random() * 2000);
    })[0].style.webkitTransform;*/
    });
</script>
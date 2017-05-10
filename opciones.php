<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    noCache();
    if ($accion=="imagen-subir"){
        include("opciones.imagen-subir.php");
        exit();
    }
    elseif ($accion=="imagen-recortar"){
        include("opciones.imagen-recortar.php");
        exit();   
    }
    elseif ($accion=="imagen-miniatura"){
        include("opciones.imagen-miniatura.php");
        exit();   
    }
    elseif ($accion=="imagen-lista"){
        include("opciones.imagen-lista.php");
        exit();   
    }
    if ($accion=="perfil"){
        include("opciones.perfil.php");
        exit();
    }
    $r_usuario = mysql_query("SELECT avatar, alias, nombre, correo FROM usuario WHERE usuario_id='$usr_id'");
?>  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Plaxed / Opciones</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="ROBOTS" content="NOODP" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <base href="<?php echo $SERVIDOR; ?>" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <LINK href="css/opciones.css" rel=STYLESHEET TYPE=text/css>
    <script type="text/javascript" src="js/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="js/funciones.generales.js"></script>
    <script type="text/javascript" src="js/spval-1.js"></script>
    <script type="text/javascript" src="js/opciones.js"></script>
    <script type="text/javascript" src="js/html5placeholder.jquery.js"></script>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="hbox">
                <div class="logo">
                    <a class="sprite" href="./"></a>
                </div>
                <div class="srch">
                </div>
                <div id="div-barra-menu">
                    <div class="m-salir" title="Salir">
                        <a class="sprite" href="./salida"></a>
                    </div>
                    <div class="m-perfil" title="Mi Perfil">
                        <a class="sprite" href="./u/<?php echo $usr_alias?>"></a>
                    </div>
                    <div class="m-home" title="Inicio">
                        <a class="sprite" href="./"></a>
                    </div>                    
                </div>                
            </div>        
        </div>
        <div class="content">
            <div class="cl" id="cl">
                <form id="form-foto" method="post" enctype="multipart/form-data" target="iframe-foto" action="./opciones/imagen-subir">
                <div class="foto-postal">
                    <div class="lnk-upload-foto" title="Click para cargar una nueva foto...">
                        Cargar Foto
                    </div>
                    <input type="file" class="file-img" id="fotousuario" name="fotousuario" title="Click para cargar una nueva foto...">                    
                    <?php
                        if (file_exists("./images/users/user-".$usr_id."-perfil.png")):
                    ?>
                        <img class="img-perfil" width="160" height="160" src="./images/users/user-<?php echo $usr_id; ?>-perfil.png">
                    <?php
                        endif;
                    ?>
                </div>
                </form>
                <div class="div-perfil">
                    <form id="form-perfil" action="">
                        <span class="nota-form">* Algunos datos todavía no se pueden editar.</span>
                        <label for="falias">Alias</label>
                        <input id="falias" name="falias" disabled="disabled" value="<?php echo $sesion->alias; ?> (*)" maxlength="15">
                        <label for="falias">Correo</label>
                        <input id="fcorreo" name="fcorreo" disabled="disabled" value="<?php echo $sesion->correo; ?> (*)" maxlength="100">
                        <label for="fnombre">Nombre</label>
                        <input id="fnombre" name="fnombre" value="<?php echo $sesion->nombre; ?>" maxlength="30">
                        <label for="fbiografia">Biografía</label>
                        <textarea id="fbiografia" name="fbiografia" rows="4"><?php echo $sesion->biografia; ?></textarea>
                        <label for="fubicacion">Ubicación</label>
                        <input id="fubicacion" name="fubicacion" value="<?php echo $sesion->ubicacion; ?>">
                        <label for="ftags">Tus 5 Palabras</label>
                        <input id="ftags" placeholder="ej: playa, baile, comida, moda, deportes" name="ftags" value="<?php echo $sesion->tags; ?>" spval="*|eval(/^(\s+)?[a-z0-9ñÑáéíóúÁÉÍÓÚ]+((\s+)?,(\s+)?[a-z0-9ñÑáéíóúÁÉÍÓÚ]+){4}(\s+)?$/i)" maxlength="50" msjError="El campo es obligatorio. Debes ingresar 5 palabras que te definan. Por ejemplo: autos, internet, deportes, lectura, playa">
                        <span class="tip-form">Defínete en 5 palabras separadas por comas.</span>
                        <div class="clear"></div>
                        <label for="fclave1">Clave</label>
                        <input id="fclave1" type="password" name="fclave1" value="" maxlength="15">
                        <label for="fclave2">Confirmar Clave</label>
                        <input id="fclave2" type="password" name="fclave2" value="" maxlength="15" spval="eq(fclave1)" msjError="Las contraseñas no coinciden">
                        <span class="tip-form">Llene los campos de contraseña sólo si desea cambiarla.</span>
                        <div class="clear"></div>
                        <input type="button" value="Actualizar" class="submit" id="btn-submit">
                        <div class="clear"></div>
                    </form>
                </div>
                <div class="clear"></div>
            </div>                  
            <div class="cr">                
                <div class="gblock">
                    <div class="ttlbox">Perfil</div>
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
                        <a href="javascript:;">Desarroladores</a>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    <div class="div-iframe-foto">
        <div class="interno">
            <iframe name="iframe-foto" id="iframe-foto"></iframe>
        </div>
    </div>    
</body>
</html>
</script>
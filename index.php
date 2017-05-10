<?php
    $ie6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE);
    if ($ie6){
        echo "Internet Exporer 6? ni en juego... usa Chrome, Firefox, Opera... o al menos actualiza Internet Explorar a la version 8<br>
        <br>Lamento informarte que Internet Explorer 6 no está en condiciones de ingresar a esta red.";
        exit();
    }

    $variableSeguridad="";
    $registroAbierto=false;
    session_start();
    $modulo = "";
    $accion="";
    include("php/plaxed.variables.php");
    include("php/funciones.php");
    include("php/class.plaxed.php");
    // el servidor BASE
    if ($_SERVER['HTTP_HOST']=="localhost"){
        $BDD=LinkBD();
    }
    else{
        $BDD=LinkBD1();    
    }
    
    $ses = (isset($_SESSION['xyz12345_conectado'])) ? $_SESSION['xyz12345_conectado'] : false;
    $usr_id = 0;
    if ($ses == "esto_es_para_navegar"){
        $usr_id = $_SESSION['xyz12345_id'];
    }   
    try {
        $plaxed = new cPlaxed($usr_id);    
    } catch (Exception $e) {
        echo $e->getMessage();
        exit();
    }    
    $SERVIDOR = $plaxed->APP->servidorBase;
    $sesion = $plaxed->obtenerSesion($usr_id);
    // Compatiblidad
    $usr_alias = $sesion->alias;
    $usr_nombre = $sesion->nombre;

	if (isset($_GET['url'])){
		$url = $_GET['url'];
        $url = strtolower($url);
        $url = explode("/", $url);
        $modulo = array_shift($url);
        $parametros=$url;
        if (isset($parametros[0]))
            $accion=$parametros[0];
	}
    if (preg_match('/\.php$/i', $modulo)){
        $modulo = substr($modulo, 0, (strlen($modulo)-4));
    }
    if (!in_array($modulo,array("salida","u","c","p","etiquetas","mp","opciones", "buscar","tema","stats","not", "opciones","feedback","phpinfo","js","upload","api","clave", "compartir", "registro", "confirmar", "recuperar", "generar-clave", "solicitudes"))){
        $modulo="";
    }
    if ($modulo=='clave' && ($sesion->id==1 || $sesion->id==2)){
        include("claveplaxed.php");
        exit();
    }
    if ($modulo=="phpinfo"){
         phpinfo();
         exit();
    }
    
    if (!$sesion->conectado){
        if ($modulo=="registro"){
            include("registro.php");
            exit();
        }
        if ($modulo=="confirmar"){
            include("registro.confirmar.php");
            exit();
        }
        if ($modulo=="recuperar"){
            include("recuperar.php");
            exit();
        }
        if ($modulo=="generar-clave"){
            include("recuperar.generar.php");
            exit();
        }
        include("login.php");
        exit();
    }

    if ($modulo=="compartir"){
        include("compartir.php");
        exit();
    }

    if ($modulo=="api"){
        include("api.php");
        exit();
    }

    if ($modulo=="salida" || !$sesion->activo){
        include("./salir.php");
        exit();
    }    

    if ($modulo=="upload"){
        if ($parametros[0]=="img"){
            include("upload.img.php");
            exit();
        }
    }
    if ($modulo=="js"){
        if ($parametros[0]=="directorio.php"){
            $r=mysql_query("SELECT usuario_id, nombre, alias FROM usuario WHERE alias!='' AND alias!='$usr_alias' ORDER BY alias");
            $aliasTmp="";
            while ($rs=mysql_fetch_array($r)){
                if (!empty($aliasTmp)){
                    $aliasTmp.=",\n";
                }
                $acentuadas = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú");
                $normalizadas = array("a","e","i","o","u","A","E","I","O","U");
                $nombre = str_replace($acentuadas, $normalizadas, $rs[1]);
                $nombre = str_replace("/[^a-zA-Z_]/", "", $nombre);
                $nombre = preg_replace("/[^a-z\s_]/i", "", $nombre);
                $aliasTmp.="{value: '$rs[2]', label:'$rs[2] ($nombre)'}";
                //$aliasTmp.="'$rs[2]'";
            }
            header("content-type: application/x-javascript");
            echo "var arregloAutocompletar = [\n$aliasTmp\n];";
            exit();
        }
    }

    $r_user=mysql_query("SELECT nombre, alias, avatar, puntos FROM usuario WHERE usuario_id='$usr_id'");
    $rs_user=mysql_fetch_array($r_user);
    $usr_puntos = $rs_user[3];
    $usr_avatar48=($rs_user[2]==1) ? "user-$usr_id-48x48.png":"user-48x48.png";
    $usr_avatar32=($rs_user[2]==1) ? "user-$usr_id-32x32.png":"user-32x32.png";
    #
    $r_mensajes=mysql_query("SELECT COUNT(usuario_id) FROM publicacion WHERE usuario_id='$usr_id'");
    $rs_mensajes=mysql_fetch_array($r_mensajes);
    $usr_cantidad_mensajes = $rs_mensajes[0];
    
    #Borrar la notificacion de la publicacion que se muestra individualmente
    if ($modulo=="p"){
        mysql_query("UPDATE notificacion SET visto=1 WHERE usuario_destino_id='$usr_id' AND publicacion_id='$parametros[0]'");
    }      
    $conversacionActual = 0;
    $postConversacionActual = "";
    $datosConvers = "";
    //Borra la notificacion del post en cuestion visto en la conversacion.
    if ($modulo=="c"){
        $datosConvers=explode(".", $parametros[0]);
        $conversacionActual=(isset($datosConvers[0])) ? $datosConvers[0] : "";
        $postConversacionActual = (isset($datosConvers[1])) ? $datosConvers[1] : "";
        mysql_query("UPDATE notificacion SET visto=1 WHERE usuario_destino_id='$usr_id' AND publicacion_id='$postConversacionActual'");
    }    
    //Borrar la notificacion de la conexion en el perfil del usuario
    $idPerfil = 0;
    if ($modulo=="u"){
        $r_up = mysql_query("SELECT usuario_id FROM usuario where alias='$parametros[0]'");
        $rs_up = mysql_fetch_array($r_up);
        $idPerfil=$rs_up[0];
        mysql_query("UPDATE notificacion SET visto=1 WHERE usuario_destino_id='$usr_id' AND usuario_origen_id='$rs_up[0]' AND tipo='conexion'");
    }

    $r_num_notificaciones=mysql_query("SELECT notificacion_id FROM v_notificacion WHERE usuario_destino_id='$usr_id' AND visto=0");
    $usr_num_notificaciones=mysql_num_rows($r_num_notificaciones);
    if ($usr_num_notificaciones==""){
        $usr_num_notificaciones="0";
    }
    $lbl_num_notificaciones = $usr_num_notificaciones;
    if ($usr_num_notificaciones>99){
        $lbl_num_notificaciones = "99+";
    }

    // Se borran los que tengan mas de 20 segundos sin comunicarse con el server.
    $momentoActual = date("Y-m-d H:i:s");
    mysql_query("DELETE FROM usuario_online WHERE (TIMESTAMPDIFF(SECOND, momento, '$momentoActual'))>20");
    //if ($usr_id!=1 && $usr_id!=2){
        // Se confirma que el usuario esta online
        $r_online=mysql_query("SELECT usuario_id FROM usuario_online WHERE usuario_id='$usr_id'");
        if (mysql_num_rows($r_online)>0){
            mysql_query("UPDATE usuario_online SET momento='$momentoActual' WHERE usuario_id='$usr_id'");
        }
        else{
            mysql_query("INSERT INTO usuario_online (usuario_id,momento) VALUES ('$usr_id','$momentoActual')");
        }  
    //}
    //Lista de Usuarios activos
    $r_online=mysql_query("SELECT u.alias, uo.usuario_id, u.avatar FROM usuario_online uo
                        INNER JOIN usuario u ON (u.usuario_id=uo.usuario_id) 
                        WHERE TIMESTAMPDIFF(SECOND, uo.momento, '$momentoActual')<=20 AND u.usuario_id!='$usr_id'
                        ORDER BY uo.momento DESC 
                        LIMIT 20");
    $arrUsuariosActivos = array();
    while ($rs_online=mysql_fetch_array($r_online)){
        $onAvatar = ($rs_online[2]==1) ? "user-".$rs_online[1]."-32x32.png" : "user-32x32.png";
        $arrUsuariosActivos[] = array("alias"=>$rs_online[0], "usuario_id"=>$rs_online[1], "avatar"=>$onAvatar);
    }

    $arrAmigos = array();
    if ($modulo=='u'){
        //Mis conexiones
        $r_amigo = mysql_query("SELECT u.alias, u.usuario_id, u.avatar FROM usuario u
                                INNER JOIN conexion c ON (c.usuario1_id=u.usuario_id AND c.usuario2_id='$idPerfil')
                                UNION 
                                SELECT u.alias, u.usuario_id, u.avatar FROM usuario u
                                INNER JOIN conexion c ON (c.usuario1_id='$idPerfil' AND c.usuario2_id=u.usuario_id)
                                ORDER BY alias 
                                LIMIT 50");
        echo mysql_error();
        while ($rs_amigo = mysql_fetch_array($r_amigo)){
            $amAvatar = ($rs_amigo[2]==1) ? "user-".$rs_amigo[1]."-32x32.png" : "user-32x32.png";
            $arrAmigos[] = array("alias"=>$rs_amigo[0], "usuario_id"=>$rs_amigo[1], "avatar"=>$amAvatar);
        }
    }
        

    header ('Content-type: text/html; charset=utf-8');
    $urlPublicacionId=0;
    $ultimoIdPublicacion=0;
    $PostIdBottom=0;
    

    if ($modulo=="opciones"){
        include("opciones.php");
        exit();
    }
    if ($modulo=="u"){
        include("perfil.php");
        exit();
    }
    elseif ($modulo=="feedback"){
        include("feedback.php");
    }
    else{
        include("timeline.php");    
    }	
?>

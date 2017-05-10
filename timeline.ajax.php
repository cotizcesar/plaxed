<?php
    if (!isset($variableSeguridad)){
        echo json_encode(array("respuesta"=>"error", "descripcion"=>"Acceso denegado."));
        exit();
    }
    if (isset($_GET['enviarPost'])){
        $salida = array("respuesta"=>"ok", "mensaje"=>"Se ha registrado la publicación.");
        try {
            $plaxed->registrarPost($_POST);    
        } catch (Exception $e) {
            $salida = array("respuesta"=>"error", "mensaje"=>$e->getMessage());
        }        
        echo json_encode($salida);
        exit();
    }

    if (isset($_GET['solicitarConexion'])){
        try {
            $respuesta = $plaxed->solicitarConexion($_POST);
        } catch (Exception $e) {
            $respuesta = array("respuesta" => "error", "mensaje"=>$e->getMessage(), "codigo"=>"0");
        }
        echo json_encode($respuesta);
        exit();
    }
    if (isset($_GET['eliminarConexion'])){
        try {
            $respuesta = $plaxed->eliminarConexion($_POST);
        } catch (Exception $e) {
            $respuesta = array("respuesta" => "error", "mensaje"=>$e->getMessage(), "codigo"=>"0");
        }
        echo json_encode($respuesta);
        exit();
    }
    if (isset($_GET['cancelarConexion'])){
        try {
            $respuesta = $plaxed->cancelarConexion($_POST);
        } catch (Exception $e) {
            $respuesta = array("respuesta" => "error", "mensaje"=>$e->getMessage());
        }
        echo json_encode($respuesta);
        exit();
    }
    if (isset($_GET['rechazarConexion'])){
        try {
            $respuesta = $plaxed->rechazarConexion($_POST);
        } catch (Exception $e) {
            $respuesta = array("respuesta" => "error", "mensaje"=>$e->getMessage());
        }
        echo json_encode($respuesta);
        exit();
    }
    if (isset($_GET['aceptarConexion'])){
        try {
            $respuesta = $plaxed->aceptarConexion($_POST);
        } catch (Exception $e) {
            $respuesta = array("respuesta" => "error", "mensaje"=>$e->getMessage());
        }
        echo json_encode($respuesta);
        exit();
    }


    if (isset($_GET['descartarNotificaciones'])){
        try {
            $respuesta = array("respuesta" => "ok", "mensaje"=>"Las notificaciones han sido marcadas como vistas.");
            $plaxed->descartarNotificaciones();
        } catch (Exception $e) {
            $respuesta = array("respuesta" => "error", "mensaje"=>$e->getMessage());
        }
        echo json_encode($respuesta);
        exit();
    }
    
    if (isset($_GET['masPlaxs'])){
        $bottomId=$_POST['bottomId'];
        $moduloActual=$_POST['moduloActual'];
        $usuarioActual=$_POST['usuarioActual'];
        $busquedaActual=$_POST['busquedaActual'];
        $temaActual=$_POST['temaActual'];
        if ($moduloActual=="u"){
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto FROM publicacion WHERE publicacion_id < $bottomId AND usuario_id='$usuarioActual' ORDER BY publicacion_id DESC LIMIT 20";    
        }
        elseif ($moduloActual=="buscar"){
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto FROM publicacion WHERE publicacion_id < $bottomId AND contenido LIKE '%$busquedaActual%' ORDER BY publicacion_id DESC LIMIT 20";    
        }
        elseif ($moduloActual=="etiquetas"){
            $sql_timeline="SELECT
                          p.publicacion_id, p.contenido, p.fecha, p.usuario_id, p.puntos, p.conversacion_id, p.adjunto
                        FROM
                          publicacion p
                          INNER JOIN mencion m ON (m.publicacion_id=p.publicacion_id AND m.usuario_destino_id='$usr_id')
                        WHERE p.publicacion_id < $bottomId
                        ORDER BY
                          fecha DESC LIMIT 20";
        }
        elseif ($moduloActual=="tema"){
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto FROM publicacion WHERE publicacion_id < $bottomId AND contenido REGEXP '#[[:<:]]".$temaActual."[[:>:]]' ORDER BY publicacion_id DESC LIMIT 20";
        }
        else{
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto FROM publicacion WHERE publicacion_id < $bottomId ORDER BY publicacion_id DESC LIMIT 20";    
        }    
        $arrPosts = array();            
        $r_post = mysql_query($sql_timeline);        
        while ($rs_post=mysql_fetch_array($r_post)){
            $publicacion_id=$rs_post[0];
            $usuario_id=$rs_post[3];
            $puntos=$rs_post[4];
            $conversacion_id=$rs_post[5];
            $adjunto = ($rs_post[6]==1)?true:false;

            $r1=mysql_query("SELECT alias,nombre,avatar FROM usuario WHERE usuario_id='$rs_post[3]'");
            $rs1=mysql_fetch_array($r1);
            $alias = $rs1[0];
            $usuario = $rs1[1];

            $mio = ($usuario_id==$usr_id) ? true : false;
            $avatar48=($rs1[2]==1) ? "user-$usuario_id-48x48.png":"user-48x48.png";

            $youtube="";
            $youtube=extraerYoutube($rs_post[1]);
            if (!empty($youtube)){
                //$youtube=str_replace("\"","\\\"", $youtube);
            }
            $texto = $rs_post[1];
            $menciones = obtenerMenciones($texto, $alias, $usr_alias);
            $texto = str_replace("<3", "❤", $texto);
            $texto = texto_a_url($texto);
            if (!get_magic_quotes_gpc()){
                //$texto = str_replace("\"", "\\\"", $texto);
            }

            // fecha del post
            $fecha="Hace ".dif_fechas($rs_post[2], date("Y-m-d H:i:s"));

            // Tooltip de las fechas
            $fechatt=date("d/m/Y h:i:s a",strtotime($rs_post[2]));

            $r_vot=mysql_query("SELECT publicacion_voto_id FROM publicacion_voto WHERE publicacion_id='$rs_post[0]' AND usuario_id='$usr_id'");
            if (mysql_num_rows($r_vot)==0){
                $votado = false;
            }
            else{
                $votado=true;
            }
            $adjuntoOriginal="";
            $adjuntoMiniatura="";
            $arrAdjunto=false;
            if ($adjunto){
                $r_adj = mysql_query("SELECT original, miniatura FROM publicacion_adjunto WHERE publicacion_id='$publicacion_id'");
                $rs_adj = mysql_fetch_array($r_adj);
                $adjuntoOriginal = $rs_adj[0];
                $adjuntoMiniatura = $rs_adj[1];
                $arrAdjunto = array("original"=>$adjuntoOriginal,"miniatura"=>$adjuntoMiniatura);
            }            

            $arrPosts[] = array(
                "autor" => array("id"=>$usuario_id, "alias"=>$alias, "nombre"=>$usuario, "avatar48"=>$avatar48),
                "publicacion" => array("id"=>$publicacion_id, "contenido"=>$texto, "puntos"=>$puntos, "conversacion_id"=>$conversacion_id, "votado"=>$votado, "adjunto"=>$arrAdjunto, "fecha"=>$fecha, "fechatt"=>$fechatt, "menciones"=>$menciones, "mio"=>$mio, "youtube"=>$youtube)
                );
        }
        echo json_encode($arrPosts);
        exit();
    }
    if (isset($_GET['recuperarPosts'])){
        $ultimoIdPublicacion = (isset($_POST['ultimoIdPublicacion'])) ? $_POST['ultimoIdPublicacion'] : "";
        $PostIdBottom = (isset($_POST['PostIdBottom'])) ? $_POST['PostIdBottom'] : "";
        $temaActual = (isset($_POST['temaActual'])) ? $_POST['temaActual'] : "";
        $usuarioActual = (isset($_POST['usuarioActual'])) ? $_POST['usuarioActual'] : 0;
        // 
        $moduloTL = $_POST['modulo'];
        if ($moduloTL=="etiquetas"){
            $sql_timeline="SELECT
                          p.publicacion_id, p.contenido, p.fecha, p.usuario_id, p.puntos, p.conversacion_id, p.adjunto, p.replax_id
                        FROM
                          publicacion p
                          INNER JOIN mencion m ON (m.publicacion_id=p.publicacion_id AND m.usuario_destino_id='$usr_id') 
                        WHERE 
                            p.publicacion_id > $ultimoIdPublicacion
                        ORDER BY
                          p.publicacion_id LIMIT 20";
        }
        elseif($moduloTL=="u"){
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto, replax_id FROM publicacion WHERE publicacion_id > $ultimoIdPublicacion AND usuario_id='$usuarioActual' ORDER BY publicacion_id LIMIT 20";
        }
        elseif ($moduloTL == 'c'){
            $conversacionActual = $_POST['conversacionActual'];
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto, replax_id FROM publicacion WHERE publicacion_id > $PostIdBottom AND conversacion_id='$conversacionActual' ORDER BY publicacion_id LIMIT 20";
        }
        elseif ($moduloTL == 'tema'){
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto, replax_id FROM publicacion WHERE publicacion_id > $ultimoIdPublicacion AND contenido REGEXP '#[[:<:]]".$temaActual."[[:>:]]' ORDER BY publicacion_id LIMIT 20";
        }
        elseif ($moduloTL == 'buscar'){
            $busquedaActual = $_POST['busquedaActual'];
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto, replax_id FROM publicacion WHERE publicacion_id > $ultimoIdPublicacion AND contenido like '%$busquedaActual%' ORDER BY publicacion_id LIMIT 20";
        }
        else{
            $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id,puntos,conversacion_id, adjunto, replax_id FROM publicacion WHERE publicacion_id > $ultimoIdPublicacion ORDER BY publicacion_id LIMIT 20";
        }
        $r = mysql_query($sql_timeline);
        $arrPosts = array();
        while ($rs=mysql_fetch_array($r)){
            $publicacion_id=$rs[0];
            $replax_id=$rs[7];
            $contenido=$rs[1];
            $usuario_id=$rs[3];
            $puntos=$rs[4];
            $conversacion_id=$rs[5];
            $adjunto = ($rs[6]==1)?true:false;
            $fechaPost = $rs[2];

            $r1=mysql_query("SELECT alias,nombre,avatar FROM usuario WHERE usuario_id='$usuario_id'");
            $rs1=mysql_fetch_array($r1);
            $alias = $rs1[0];
            $usuario = $rs1[1];

            $mio = ($usuario_id==$usr_id) ? true : false;
            if ($rs1[2]==1){
                $avatar48="user-$usuario_id-48x48.png";
                $rutaAvatarOriginal = './images/users/user-'.$usuario_id.'-original.png';
                if (file_exists($rutaAvatarOriginal)){
                    $dim = getimagesize($rutaAvatarOriginal);
                    //$avatarmaxd = $dim[0].'x'.$dim[1];
                    $imgAncho = $dim[0];
                    $imgAlto = $dim[1];
                }
                else{
                    //$avatarmaxd = "160x160";
                    $imgAncho = "160";
                    $imgAlto = "160";
                }
            }
            else{
                $avatar48="user-48x48.png";
            }

            $youtube="";
            $youtube=extraerYoutube($contenido);
            if (!empty($youtube)){
                //$youtube=str_replace("\"","\\\"", $youtube);
            }
            
            $menciones = obtenerMenciones($contenido, $alias, $usr_alias);
            $contenido = str_replace("<3", "❤", $contenido);
            $contenido = texto_a_url($contenido);

            // fecha del post
            $fecha="Hace ".dif_fechas($fechaPost, date("Y-m-d H:i:s"));

            // Tooltip de las fechas
            $fechatt=date("d/m/Y h:i:s a",strtotime($fechaPost));

            $r_vot=mysql_query("SELECT publicacion_voto_id FROM publicacion_voto WHERE publicacion_id='$publicacion_id' AND usuario_id='$usr_id'");
            if (mysql_num_rows($r_vot)==0){
                $votado = false;
            }
            else{
                $votado = true;
            }
            $adjuntoOriginal="";
            $adjuntoMiniatura="";
            $arrAdjunto = false;
            if ($adjunto){
                $r_adj = mysql_query("SELECT original, miniatura FROM publicacion_adjunto WHERE publicacion_id='$publicacion_id'");
                $rs_adj = mysql_fetch_array($r_adj);
                $adjuntoOriginal = $rs_adj[0];
                $adjuntoMiniatura = $rs_adj[1];
                $arrAdjunto = array("original"=>$adjuntoOriginal,"miniatura"=>$adjuntoMiniatura);
            }

            $arrPosts[] = array(
                "autor" => array("id"=>$usuario_id, "alias"=>$alias, "nombre"=>$usuario, "avatar48"=>$avatar48, "avatarAncho"=>$imgAncho, "avatarAlto"=>$imgAlto),
                "publicacion" => array("id"=>$publicacion_id, "contenido"=>$contenido, "puntos"=>$puntos, "conversacion_id"=>$conversacion_id, "votado"=>$votado, "adjunto"=>$arrAdjunto, "fecha"=>$fecha, "fechatt"=>$fechatt, "menciones"=>$menciones, "mio"=>$mio, "youtube"=>$youtube)
                );
        }
        # Datos del usuario (cantidad de mensajes, segudores, puntos)
        $arrUsuario = array("c_mensajes"=>"$usr_cantidad_mensajes", "c_puntos"=>"$usr_puntos", "c_notificaciones"=>"$usr_num_notificaciones", "c_puntos_disponibles"=>$sesion->puntosDisponibles, "c_conexiones"=>$sesion->conexiones);

        $arrNotificaciones = array();
        $r_not = mysql_query("SELECT tipo, publicacion_id, visto, usuario_origen_nombre,publicacion_id, destino_id, usuario_origen_id, usuario_origen_alias FROM v_notificacion WHERE usuario_destino_id='$usr_id' ORDER BY visto, fecha DESC LIMIT 6");
        while ($rs_not=mysql_fetch_array($r_not)){
            $not_linea="";
            $tipo=$rs_not[0];            
            $destino=$rs_not[5];         
            $visto=($rs_not[2]==1) ? "si" : "no";
            if ($tipo=='mencion'){
                $r_men = mysql_query("SELECT p.conversacion_id FROM publicacion p 
                                    INNER JOIN mencion m ON (m.publicacion_id=p.publicacion_id)
                                    WHERE m.mencion_id='$destino'");
                $rs_men = mysql_fetch_array($r_men);
                $conversacion_id = $rs_men[0];   
                $not_linea="<a href=\"./c/$conversacion_id.$rs_not[1]#post-$rs_not[1]\">$rs_not[3] te ha etiquetado en una publicación.</a>";
            }
            if ($tipo=='puntuacion'){
                $r_voto = mysql_query("SELECT voto FROM publicacion_voto WHERE publicacion_voto_id='$rs_not[5]'");
                $rs_voto = mysql_fetch_array($r_voto);
                $palabra = ($rs_voto[0]=='+') ? "positiva" : "negativa";
                $not_linea="<a href=\"./p/$rs_not[1]\">Alguien ha votado $palabra tu publicación.</a>";
            }
            if ($tipo=='conexion-solicitud'){
                $nombre = $rs_not[3];
                $alias = $rs_not[7];
                $id = $rs_not[6];
                $not_texto="$nombre ha solicitado conexión.";
                $not_linea.="<a href=\"javascript:;\" class=\"lnk-conex\" userid=\"$id\" usernombre=\"$nombre\" useralias=\"$alias\">$not_texto</a>";  
            }
            if ($tipo=='conexion'){
                $nombre = $rs_not[3];
                $alias = $rs_not[7];
                $id = $rs_not[6];
                $not_texto="Se ha creado una conexión entre $nombre y tú.";
                $not_linea.="<a href=\"./u/$alias\">$not_texto</a>";
            }
            if (!empty($not_linea)){
                $arrNotificaciones[]=array("contenido"=>"$not_linea", "visto"=>"$visto");
            }
        }
        $arrUsuarioPerfil = false;
        if ($usuarioActual!=0){
            $r_userp = mysql_query("SELECT posts, puntos, conexiones FROM usuario WHERE usuario_id='$usuarioActual'");
            $rs_userp = mysql_fetch_array($r_userp);
            $arrUsuarioPerfil=array("posts"=>$rs_userp[0], "puntos"=>$rs_userp[1], "conexiones"=>$rs_userp[2]);
        }

        //el array arrUsuariosActivos se carga en el home.

        $arrJSON = array("posts"=> $arrPosts, "usuario"=>$arrUsuario, "notificaciones"=>$arrNotificaciones, "perfil"=>$arrUsuarioPerfil, "activos"=>$arrUsuariosActivos);
        echo json_encode($arrJSON);
        exit();
    }
    if (isset($_GET['enviarPuntuacion'])){
        try {
            $puntos = $plaxed->enviarPuntuacion($_POST);    
            $salida = array("respuesta"=>"ok", "mensaje"=>"Puntuación registrada.","puntos"=>$puntos);
        } catch (Exception $e) {
            $salida = array("respuesta"=>"error", "mensaje"=>$e->getMessage());
        }
        echo json_encode($salida);
        exit();
   }
    if (isset($_GET['eliminarPost'])){
        try {
            $plaxed->eliminarPost($_POST);    
            $salida = array("respuesta"=>"ok", "mensaje"=>"Se ha eliminado la publicación.");
        } catch (Exception $e) {
            $salida = array("respuesta"=>"error", "mensaje"=>$e->getMessage());
        }
        echo json_encode($salida);
        exit();
   }
?>
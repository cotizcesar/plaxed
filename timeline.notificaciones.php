<?php
    $not_linea="";
    $r_notif=mysql_query("SELECT tipo, publicacion_id, visto, usuario_origen_nombre,publicacion_id,destino_id,usuario_origen_alias,usuario_origen_id FROM v_notificacion WHERE usuario_destino_id='$usr_id' ORDER BY visto, fecha DESC LIMIT 6");
    while ($rs_notif=mysql_fetch_array($r_notif)){
        $not_texto="";
        $tipo=$rs_notif[0];
        $visto=($rs_notif[2]==1) ? true : false;
        $classVisto = (!$visto) ? " class=\"not-vista\"" : "";
        $destino=$rs_notif[5];
        if ($tipo=='mencion'){            
            $not_texto="$rs_notif[3] te ha etiquetado en una publicación.";
            $r_men = mysql_query("SELECT p.conversacion_id FROM publicacion p 
                                    INNER JOIN mencion m ON (m.publicacion_id=p.publicacion_id)
                                    WHERE m.mencion_id='$destino'");
            $rs_men = mysql_fetch_array($r_men);
            $conversacion_id = $rs_men[0];            
            $not_linea.="<li$classVisto><a href=\"./c/$conversacion_id.$rs_notif[1]#post-$rs_notif[1]\">$not_texto</a></li>";        
        }
        elseif ($tipo=="puntuacion"){
            $r_voto = mysql_query("SELECT voto FROM publicacion_voto WHERE publicacion_voto_id='$rs_notif[5]'");
            $rs_voto = mysql_fetch_array($r_voto);
            $palabra = ($rs_voto[0]=='+') ? "positiva" : "negativa";
            $not_texto="Alguien ha votado $palabra tu publicación.";
            /*
            $r_pun = mysql_query("SELECT p.conversacion_id FROM publicacion p 
                                    INNER JOIN publicacion_voto pv ON (pv.publicacion_id=p.publicacion_id)
                                    WHERE pv.publicacion_voto_id='$destino'");
            $rs_pun = mysql_fetch_array($r_pun);
            $conversacion_id = $rs_pun[0];   */
            $not_linea.="<li$classVisto><a href=\"./p/$rs_notif[1]\">$not_texto</a></li>";        
        }
        elseif ($tipo=="conexion-solicitud"){
            $nombre = $rs_notif[3];
            $alias = $rs_notif[6];
            $id = $rs_notif[7];
            $not_texto="$nombre ha solicitado conexión.";
            $not_linea.="<li$classVisto><a href=\"javascript:;\" class=\"lnk-conex\" userid=\"$id\" usernombre=\"$nombre\" useralias=\"$alias\">$not_texto</a></li>";    
        }
        elseif ($tipo=="conexion"){
            $nombre = $rs_notif[3];
            $alias = $rs_notif[6];
            $id = $rs_notif[7];
            $not_texto="Se ha creado una conexión entre $nombre y tú.";
            $not_linea.="<li$classVisto><a href=\"./u/$alias\">$not_texto</a></li>";
        }
    }
    if (!empty($not_linea)){
        echo $not_linea;
    }
?>      
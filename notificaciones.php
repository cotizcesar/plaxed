<?php
    $r_not = mysql_query("SELECT tipo, publicacion_id, visto, usuario_origen_nombre,publicacion_id, 
                    date_format(fecha, '%d/%m/%Y a las %h:%m%p') as fecha_es, destino_id, usuario_origen_id, usuario_origen_alias 
                        FROM v_notificacion 
                        WHERE usuario_destino_id='$usr_id' 
                        ORDER BY visto, fecha DESC LIMIT 500");
    $salida = "";
    while ($rs_not=mysql_fetch_array($r_not)){
        $not_linea="";
        $tipo=$rs_not[0];            
        $claseVisto=($rs_not[2]==0) ? " not-nueva" : "";
        $fecha=$rs_not[5];
        $destino=$rs_not[6];
        $fecha=strtolower($fecha);
        $pub_vot_id=$rs_not[6];
        if ($tipo=='mencion'){
            $r_men = mysql_query("SELECT p.conversacion_id FROM publicacion p 
                                    INNER JOIN mencion m ON (m.publicacion_id=p.publicacion_id)
                                    WHERE m.mencion_id='$destino'");
            $rs_men = mysql_fetch_array($r_men);
            $conversacion_id = $rs_men[0];            
            $not_linea="<a href=\"./c/$conversacion_id.$rs_not[1]#post-$rs_not[1]\"><span>$fecha</span><p>$rs_not[3] te ha etiquetado en una publicación.</p></a>";
            $salida.="<div id=\"div_notif_$rs_not[1]\" class=\"cl-notif$claseVisto\" msjid=\"$rs_not[1]\">$not_linea</div>";
        }
        elseif($tipo=='puntuacion'){
            $r_voto = mysql_query("SELECT voto FROM publicacion_voto WHERE publicacion_voto_id='$pub_vot_id'");
            $rs_voto = mysql_fetch_array($r_voto);
            $palabra = ($rs_voto[0]=='+') ? "positiva" : "negativa";
            $not_linea="<a href=\"./p/$rs_not[1]\"><span>$fecha</span>:<p>Alguien ha votado $palabra tu publicación.</p></a>";               
            $salida.="<div id=\"div_notif_$rs_not[1]\" class=\"cl-notif$claseVisto\" msjid=\"$rs_not[1]\">$not_linea</div>";
        }
        elseif ($tipo=="conexion-solicitud"){
            $nombre = $rs_not[3];
            $alias = $rs_not[8];
            $id = $rs_not[7];
            $not_texto="$nombre ha solicitado conexión.";
            $not_linea.="<a href=\"javascript:;\" class=\"lnk-conex\" userid=\"$id\" usernombre=\"$nombre\" useralias=\"$alias\">$not_texto</a>";    
            $salida.="<div id=\"div_notif_$rs_not[1]\" class=\"cl-notif$claseVisto\" >$not_linea</div>";
        }        
        elseif ($tipo=="conexion"){
            $nombre = $rs_not[3];
            $alias = $rs_not[8];
            $id = $rs_not[7];
            $not_texto="Se ha creado una conexión entre $nombre y tú.";
            $not_linea.="<a href=\"./u/$alias\">$not_texto</a>";
            $salida.="<div id=\"div_notif_$rs_not[1]\" class=\"cl-notif$claseVisto\" >$not_linea</div>";
        }
    }
    echo $salida;
?>
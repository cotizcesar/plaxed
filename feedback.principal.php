<div id="div-tools">
    <a href="./actividad_agregar">+ Agregar Actividad +</a>
</div>
<?php
    $filtro="";
    if (isset($_GET['usuario_id']))
        $filtro="WHERE usuario_id='".$_GET['usuario_id']."'";
    $r_act = mysql_query("SELECT actividad_id, titulo, descripcion, usuario_id, alias, nombre, fecha_es, hora 
                            FROM vt_actividad 
                            $filtro
                            ORDER BY fecham DESC");
    while ($rs_act=mysql_fetch_array($r_act)){
        $descripcion=tagsOff($rs_act[2]);
        if (strlen($descripcion)>300){
            $descripcion = substr($descripcion, 0, 300)."...";
        }        
        $hora = strtolower($rs_act[7]);
        $titulo = tagsOff($rs_act[1]);
        $r_comm = mysql_query("SELECT COUNT(actividad_comentario_id) FROM actividad_comentario WHERE actividad_id='$rs_act[0]'");
        $rs_comm = mysql_fetch_array($r_comm);
        $ncomm = (int)$rs_comm[0];
        echo "
        <div class=\"item\" id=\"div-item-$rs_act[0]\">
            <div class=\"titulo\"><a href=\"./actividad/$rs_act[0]\">$titulo</a></div>
            <div class=\"usuario\">Publicado por <a href=\"./usuario/$rs_act[3]\">$rs_act[4]</a> el $rs_act[6], a las $hora ($ncomm Comentarios)</div>
            <div class=\"descripcion\"><p>$descripcion</p></div>
        </div>
        ";
    }
?>
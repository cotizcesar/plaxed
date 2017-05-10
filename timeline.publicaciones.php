<?php        
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    $clase_individual="";
    if ($modulo=="etiquetas"){
        $sql_timeline="SELECT
                          p.publicacion_id, p.contenido, p.fecha, p.usuario_id, p.puntos, p.conversacion_id, p.adjunto, p.replax_id
                        FROM
                          publicacion p
                          INNER JOIN mencion m ON (m.publicacion_id=p.publicacion_id AND m.usuario_destino_id='$usr_id')
                        ORDER BY
                          fecha DESC LIMIT 60";
    }
    elseif ($modulo=="c"){
        // las variables de llenan en index
        $sql_timeline="SELECT
                          p.publicacion_id, p.contenido, p.fecha, p.usuario_id, p.puntos, p.conversacion_id, p.adjunto, p.replax_id
                        FROM
                          publicacion p
                        WHERE
                            p.conversacion_id='$conversacionActual'
                        ORDER BY
                          p.publicacion_id";
    }
    elseif ($modulo=="p"){
        $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id, puntos, conversacion_id, adjunto, replax_id FROM publicacion WHERE publicacion_id='$parametros[0]'";
        $clase_individual=" msj-individual";
    }
    elseif ($modulo=="u"){
        $r_userurl=mysql_query("SELECT usuario_id FROM usuario WHERE alias='$parametros[0]'");
        $rs_userurl=mysql_fetch_array($r_userurl);
        $iduserurl=$rs_userurl[0];
        $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id, puntos, conversacion_id, adjunto, replax_id FROM publicacion WHERE usuario_id='$iduserurl' ORDER BY fecha DESC LIMIT 60";    
    }
    elseif ($modulo=="tema"){
        $temaActual = $parametros[0];
        $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id, puntos, conversacion_id, adjunto, replax_id FROM publicacion WHERE contenido REGEXP '#[[:<:]]".$temaActual."[[:>:]]' ORDER BY fecha DESC LIMIT 60";    
    }
    elseif ($modulo=="buscar"){
        $busquedaActual = $parametros[0];
        $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id, puntos, conversacion_id, adjunto, replax_id FROM publicacion WHERE contenido like '%$busquedaActual%' ORDER BY fecha DESC LIMIT 60";    
    }
    else{
        $sql_timeline="SELECT publicacion_id, contenido, fecha, usuario_id, puntos, conversacion_id, adjunto, replax_id FROM publicacion ORDER BY fecha DESC LIMIT 60";
    }
    $r=mysql_query($sql_timeline);
    $numPosts = mysql_num_rows($r);
    
    $primero = true;
    $ultimoIdPublicacion = 0;
    $n=0;
    if ($numPosts==0 && $modulo=="p"){
        echo "<div class=\"msj msj-individual\">El post no existe...</div>";
    }
    $PostIdBottom=0;
    while ($rs=mysql_fetch_array($r)){
        $n++;
        if ($primero){
            $ultimoIdPublicacion = $rs[0];
            $primero = false;
        }

        $publicacion_id=$rs[0];
        $publicaciono_id = $publicacion_id;
        $PostIdBottom = $publicacion_id;
        $contenido = $rs[1];
        $fechaPost = $rs[2];
        $usuario_id=$rs[3];
        $puntos=$rs[4];
        $conversacion_id=$rs[5];
        $adjunto = ($rs[6]==1)?true:false;
        $replax_id=$rs[7];        
        $txtReplax = "";
       

        if ($replax_id!=0){
            // Datos del Replax
            $r_replax = mysql_query("SELECT u.alias FROM usuario u INNER JOIN publicacion p ON (p.usuario_id=u.usuario_id) 
                                    WHERE p.replax_id = '$replax_id'");
            $usersReplax = "";
            while ($rs_replax = mysql_fetch_array($r_replax)){
                if (!empty($usersReplax))
                    $usersReplax.=" ";
                $usersReplax.="@".$rs_replax[0];
            }
            //$txtReplax = texto_a_url($usersReplax);
            $txtReplax = $usersReplax;

            //Datos del post Original
            $r_post= mysql_query("SELECT publicacion_id, contenido, fecha, usuario_id, puntos, conversacion_id, adjunto, replax_id FROM publicacion WHERE publicacion_id='$replax_id'");
            $rs_post = mysql_fetch_array($r_post);
            $publicacion_id=$rs_post[0];
            $contenido = $rs_post[1];
            $fechaPost = $rs_post[2];
            $usuario_id=$rs_post[3];
            $puntos=$rs_post[4];
            $conversacion_id=$rs_post[5];
            $adjunto = ($rs_post[6]==1)?true:false;

        }
        else{
            
        }
        if ($puntos > 0)
            $puntos = "+$puntos";
        $fecha="Hace ".dif_fechas($fechaPost, date("Y-m-d H:i:s"));
        $fechatt=date("d/m/Y h:i:s a",strtotime($fechaPost));        

        $r1=mysql_query("SELECT alias,nombre, avatar FROM usuario WHERE usuario_id='$usuario_id'");
        $rs1=mysql_fetch_array($r1);
        $usuario=$rs1[1];
        $alias=$rs1[0];

        $mio = ($usuario_id==$usr_id) ? true : false;
        $avatarmaxd = "";
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
        $menciones = obtenerMenciones($contenido, $alias, $usr_alias);
        $contenido = str_replace("<3", "♥", $contenido);
        $contenido = texto_a_url($contenido);

        if ($puntos>0){
            $puntos = "<span class=\"txt_verde\">$puntos</span>";
        }
        elseif ($puntos<0){
            $puntos = "<span class=\"txt_rojo\">$puntos</span>";
        }
        else{
            $puntos = "<span class=\"txt_neutro\">$puntos</span>";   
        }

        $r_vot=mysql_query("SELECT publicacion_voto_id FROM publicacion_voto WHERE publicacion_id='$publicacion_id' AND usuario_id='$usr_id'");
        if (mysql_num_rows($r_vot)==0){
            $votado = false;
        }
        else{
            $votado = true;
        }

        $adjuntoOriginal="";
        $adjuntoMiniatura="";
        if ($adjunto){
            $r_adj = mysql_query("SELECT original, miniatura FROM publicacion_adjunto WHERE publicacion_id='$publicacion_id'");
            $rs_adj = mysql_fetch_array($r_adj);
            $adjuntoOriginal = $rs_adj[0];
            $adjuntoMiniatura = $rs_adj[1];
        }
?>
        <div class="msj<?php echo $clase_individual; ?>" id="div_msj_<?php echo $publicaciono_id; ?>" menciones="<?php echo $menciones; ?>">
            <a class="ancla" name="<?php echo "post-<?php echo $publicaciono_id; ?>"; ?>"></a>
            <div class="avatar48">
                <img usuario="<?php echo $usuario_id; ?>" ancho="<?php echo $imgAncho; ?>" alto="<?php echo $imgAlto; ?>" src="./images/users/<?php echo $avatar48; ?>">
            </div>
            <div class="topmsj">
                <a href="./u/<?php echo $alias; ?>"><?php echo $usuario; ?></a><p id="p-username-<?php echo $publicaciono_id; ?>" class="usern">- @<?php echo $alias; ?></p>
            </div>
            <div class="text" id="div-mensaje-<?php echo $publicaciono_id; ?>">
                <?php echo $contenido; ?>
                <?php
                    if ($youtube!=""){
                        echo $youtube;
                    }
                ?>
            </div> 
            <div class="mnav" id="mnav_<?php echo $publicaciono_id; ?>">
                <?php if ($mio): ?>
                <a href="javascript:;" class="del lnkBorrar sprite" title="Borrar" msjId="<?php echo $publicaciono_id; ?>"></a>
                <?php endif; ?>
                <?php if (!$mio): ?>
                <a href="javascript:;" class="rpt sprite" title="Repetir"></a>
                <?php endif; ?>
                <a href="javascript:;" msjId="<?php echo $publicaciono_id; ?>" class="rsp lnkResponder sprite" title="Responder"></a>
                <?php if (!$mio && !$votado): ?>
                <a href="javascript:;" id="minus_<?php echo $publicaciono_id; ?>" class="minus lnkVoto sprite" title="Negativo" msjId="<?php echo $publicaciono_id; ?>" msjVoto="n"></a>
                <a href="javascript:;" id="plus_<?php echo $publicaciono_id; ?>"class="plus lnkVoto sprite" title="Positivo" msjId="<?php echo $publicaciono_id; ?>" msjVoto="y"></a>
                <?php endif; ?>
            </div>
            <div class="msj_bottom" id="msj_bottom">
                <div class="msj_fecha" id="msj_fecha">
                    <a href="./p/<?php echo $publicaciono_id; ?>" title="<?php echo $fechatt; ?>"><?php echo $fecha; ?></a>
                </div>                   
                <div class="link_conversacion">
                    <a href="./c/<?php echo $conversacion_id; ?>">Conversación</a>                    
                </div>
                <div class="msj_puntos" id="msj_puntos_<?php echo $publicaciono_id; ?>">
                    <p>Puntos: <?php echo $puntos; ?></p>
                </div>
                <?php
                    if ($adjunto):
                ?>
                    <div class="link_imagen"><a href="<?php echo $adjuntoOriginal; ?>" target="_blank">Ver Imagen</a></div>
                <?php
                    endif;
                ?>
                <?php
                if ($replax_id!=0):
                ?>
                <div class="txt-replax">Replax de <?php echo $txtReplax; ?></div>
                <?php
                    endif;
                ?>
                <div class="clear"></div>                
            </div>
            
        </div>
<?php
    }
?>
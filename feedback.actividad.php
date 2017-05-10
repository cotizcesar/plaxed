<?php
    if ($accion=="actividad_comentar"){
        $time = time();
        $comm = $_POST['comentario'];
        $id = $_POST['actividad_id'];
        $comm=trim($comm);
        $id=trim($id);
        if (empty($comm) || empty($id)){
            echo "naaah, olvidalo...";
            exit();
        }
        $fecha = date("Y-m-d H:i:s", $time);
        $con = mysql_query("INSERT INTO actividad_comentario (actividad_id,usuario_id,contenido,fecha) 
                    VALUES ('$id','$usr_id','$comm','$fecha')");
        mysql_query("UPDATE actividad SET fecham='$fecha' WHERE actividad_id='$id'");

        if ($con){
            $fecha = date("d/m/Y",$time)." a las ".date("h:ia",$time);
            $arreglo = array("comentario"=>tagsOff($comm),
                            "fecha"=>$fecha,
                            "autor"=>$usr_alias);
            $objeto = json_encode($arreglo); 
            echo $objeto;
        }
        else
            echo "error";
        exit();
    }
    $actividad_id = (isset($parametros[1])) ? $parametros[1] : 0;
    $r_actividad = mysql_query("SELECT actividad_id, cerrado, titulo, descripcion, usuario_id, nombre, alias, fecha_es, hora 
        FROM vt_actividad 
        WHERE actividad_id='$actividad_id'");
    
    if (mysql_num_rows($r_actividad)==1):
        $rs_actividad = mysql_fetch_array($r_actividad);
?>
<div class="foro">
    <div class="actividad cfondo1">
        <div class="titulo"><?php echo "#".$rs_actividad[0]." - ".tagsOff($rs_actividad[2]); ?></div>
        <div class="cabecera">
            <?php echo $rs_actividad[6]; ?> sugirió:
        </div>
        <div class="descripcion"><?php echo $rs_actividad[3]; ?></div>
        <div class="pie">
            <?php echo $rs_actividad[7]." a las ".$rs_actividad[8]; ?>
        </div>
    </div>
    <?php
        $r_comment = mysql_query("SELECT usuario_id, contenido, date_format(fecha, '%d/%m/%Y a las %h:%i%p') as fecha_es FROM actividad_comentario WHERE actividad_id='$actividad_id' ORDER BY fecha ASC");
        $claseFondo = "";
        while ($rs_comment = mysql_fetch_array($r_comment)){
            $comentario = tagsOff($rs_comment[1]);
            $r_user = mysql_query("SELECT alias FROM usuario WHERE usuario_id='$rs_comment[0]'");
            $rs_user=mysql_fetch_array($r_user);
            $fecha = strtolower($rs_comment[2]);
            echo "
            <div class=\"comentario $claseFondo\">
                <p>$rs_user[0] comentó:</p>
                <p>$comentario</p>
                <div class=\"pie\">$fecha</div>
            </div>
            ";
            if ($claseFondo=="")
                $claseFondo="cfondo1";
            else
                $claseFondo="";
        }
    ?>
</div>
<form id="form-comment" method="post" action="#">
    <input type="hidden" id="actividad_id" name="actividad_id" value="<?php echo$actividad_id ?>">
<div id="div-form-comentario">
    <div class="form-cuerpo">
        <p>
            <label>Comentario</label>
            <textarea rows="5" name="comentario" id="comentario" spval="*"></textarea>
        </p>
    </div>
    <p class="submit">
        <input type="submit" value="Enviar" id="enviar">
    </p>
</div>
</form>
<script type="text/javascript">
    $('#form-comment').submit(function(){
        if (supervalidacion(document.getElementById('form-comment'))){
            var campos = $(this).serialize();
            $('#comentario,#enviar').attr('disabled', true);
            $.post('./actividad_comentar', campos, function(data){
                //alert(data);
                if (data!='error'){
                    objeto = $.parseJSON(data);
                    var comentario = document.createElement("div");
                    var p1 = document.createElement("p");
                    var p2 = document.createElement("p");
                    var pie = document.createElement("div");
                    $(p1).html(objeto.autor+' comentó:');
                    $(p2).html(objeto.comentario);
                    $(pie).addClass('pie');
                    $(pie).html(objeto.fecha);
                    $(comentario).addClass('comentario');
                    $(comentario).append($(p1));
                    $(comentario).append($(p2));
                    $(comentario).append($(pie));
                    if ($('.foro .comentario:last').length){
                        if (!$('.foro .comentario:last').hasClass('cfondo1')){
                            $(comentario).addClass('cfondo1');
                        }    
                    }                    
                    $('.foro').append($(comentario));                    
                    $('#comentario').val('');                    
                }
                else{
                    alert('error al publicar... Repórtalo a @jrcsDev');
                }
                $('#comentario,#enviar').attr('disabled', false);
                $('#comentario').focus();
            });

        }
        return false;
    });
</script>
<?php
    else:
        echo "Actividad incorrecta";
    endif;
?>
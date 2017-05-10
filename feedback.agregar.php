<?php
    if ($accion=="actividad_registrar"){
        $fecha = date("Y-m-d H:i:s");
        $titulo="";
        $descripcion="";
        if (isset($_POST['titulo'])){
            $titulo=$_POST['titulo'];    
        }
        if (isset($_POST['descripcion'])){
            $descripcion=$_POST['descripcion'];    
        }
        
        $titulo=trim($titulo);
        $descripcion=trim($descripcion);
        
        if (empty($titulo) || empty($descripcion)){
            header('location: ./actividad_error');
            exit();    
        }

        
        mysql_query("INSERT INTO actividad (usuario_id,titulo,descripcion,fecha,fecham,cerrado) VALUES ('$usr_id','$titulo','$descripcion','$fecha','$fecha','0')");
        header('location: ./actividad_registrada');
        exit();
    }
?>
<form method="post" action="./actividad_registrar" onsubmit="return supervalidacion(this);">
<div id="div-form">
    <div class="form-cuerpo">
        <p>
            <label>Titulo</label>
            <input type="text" maxlength="60" name="titulo" id="titulo" spval="*">
        </p>
        <p>
            <label>Descripción</label>
            <textarea rows="5" name="descripcion" id="descripcion" spval="*"></textarea>
        </p>
    </div>
    <p class="submit">
        <input type="submit" value="Agregar">
    </p>
</div>
</form>
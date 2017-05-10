<?php
    if (isset($_POST['x'])){
        $targ_w = 160;
        $targ_h = 160;
        //$jpeg_quality = 90;
        $x=$_POST['x'];
        $y=$_POST['y'];
        $w=$_POST['w'];
        $h=$_POST['h'];
        $src = "./tmp/user-".$usr_id."2.png";
        if (!file_exists($src)){
            echo "la imagen origen no existe.. $src";
            exit();
        }
        
        $img_r = imagecreatefrompng($src);
        if (!$img_r){
            echo "no se pudo cargar la imagen... $src";
            exit();
        }
        $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

        imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$w,$h);
        $nombref="user-$usr_id-perfil.png";
        $destino="images/users/$nombref";

        imagepng($dst_r,$destino);
        //@unlink("tmp/user-".$usr_id.".png");
        
        header("location: $SERVIDOR/opciones/imagen-miniatura/".time());
        exit();
    }
    $extension = $parametros[1];
    $imagenUrl = "./tmp/user-".$usr_id.".img";
    $tipo = mime_content_type($imagenUrl);

    //Redimensionamiento a 800 x 600
    $anchura=800; 
    $hmax=600; 
    $datos = getimagesize($imagenUrl); 
    if($tipo=="image/jpeg"){
        $img = @imagecreatefromjpeg($imagenUrl);
    }
    elseif ($tipo=="image/png"){
        $img = @imagecreatefrompng($imagenUrl);
    }
    else{
        echo "ERROR! Contacte al servicio técnico.";
        exit();
    }
    $ratio = ($datos[0] / $anchura); 
    $altura = ($datos[1] / $ratio); 
    if($altura>$hmax){$anchura2=$hmax*$anchura/$altura;$altura=$hmax;$anchura=$anchura2;}
    $thumb = imagecreatetruecolor($anchura,$altura); 
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $anchura, $altura, $datos[0], $datos[1]); 
    $nuevaImg = "./images/users/user-".$usr_id."-original.png";
    imagepng($thumb, $nuevaImg);
    imagedestroy($thumb);
   
    //Redimensionamiento para la ventana de acortamiento
    $imagenUrl = $nuevaImg;
    $anchura=660; 
    $hmax=525; 
    $datos = getimagesize($imagenUrl); 
    $datos = getimagesize($imagenUrl); 
    $img = @imagecreatefrompng($imagenUrl);
    
    $ratio = ($datos[0] / $anchura); 
    $altura = ($datos[1] / $ratio); 
    if($altura>$hmax){$anchura2=$hmax*$anchura/$altura;$altura=$hmax;$anchura=$anchura2;}
    $thumb = imagecreatetruecolor($anchura,$altura); 
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $anchura, $altura, $datos[0], $datos[1]); 
    $nuevaImg = "./tmp/user-".$usr_id."2.png";
    imagepng($thumb, $nuevaImg);
    imagedestroy($thumb);
    //chmod('/var/www/plaxed/tmp/user-$usr_id.png', 0777);
    //@unlink("./tmp/user-$usr_id.jpg");
    $infoImg = getimagesize($nuevaImg); 
    $ratio = $anchura/$hmax;
?>
<html>
<head>
    <title>Plaxed / Opciones</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="ROBOTS" content="NOODP" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <base href="<?php echo $SERVIDOR; ?>" />
    <LINK href="css/opciones.css" rel=STYLESHEET TYPE=text/css>
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/Jcrop/js/jquery.Jcrop.min.js"></script>
    <link rel="stylesheet" href="js/Jcrop/css/jquery.Jcrop.css" type="text/css" />
</head>
<body>
    <div class="div-img-original">
        <div class="img">
            <img id="jcrop_target" src="<?php echo $nuevaImg."?".time(); ?>">
        </div>
    </div>
    <div class="div-previsualizar">
        <div class="div-mensaje">
            <span>Recorta tu foto!</span>
        </div>
        <div class="div-miniatura">
            <img id="preview" src="<?php echo $nuevaImg; ?>">
        </div>
        <div class="div-boton">
            <input type="button" value="Listo!" id="btn-listo">
        </div>
    </div>
    <form id="form-recorte" onsubmit="return checkCoords();" method="post" action="./opciones/imagen-recortar/<?php echo time(); ?>">
        <input type="hidden" id="x" name="x">
        <input type="hidden" id="y" name="y">
        <input type="hidden" id="w" name="w">
        <input type="hidden" id="h" name="h">
        
    </form>
        
</body>
<script type="text/javascript">
    $(function(){
        $('#jcrop_target').Jcrop({
            onChange: showPreview,
            onSelect: updateCoords,
            setSelect: [ 0, 0, 160, 160 ],
            minSize: [160,160],
            aspectRatio: 160/160
        });
    });
    function showPreview(coords)
    {
        var rx = 160 / coords.w;
        var ry = 160 / coords.h;

        $('#preview').css({
            width: Math.round(rx * <?php echo $infoImg[0]; ?>) + 'px',
            height: Math.round(ry * <?php echo $infoImg[1]; ?>) + 'px',
            marginLeft: '-' + Math.round(rx * coords.x) + 'px',
            marginTop: '-' + Math.round(ry * coords.y) + 'px'
        });
    }
    function updateCoords(c)
    {
        $('#x').val(c.x);
        $('#y').val(c.y);
        $('#w').val(c.w);
        $('#h').val(c.h);
    };
    function checkCoords()
    {
        if (parseInt($('#w').val())) return true;
        alert('Debe seleccionar una region...');
        return false;
    };
    $('#btn-listo').click(function(){
        $('#form-recorte').submit();
    })
</script>
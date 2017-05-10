<?php
    if (isset($_POST['x'])){
        $x=$_POST['x'];
        $y=$_POST['y'];
        $w=$_POST['w'];
        $h=$_POST['h'];

        $targ_w = 48;
        $targ_h = 48;        
        
        $src = "images/users/user-".$usr_id."-perfil.png";
        $img_r = imagecreatefrompng($src);
        $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

        imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$w,$h);
        $nombref="user-".$usr_id."-48x48.png";
        $destino="images/users/$nombref";
        imagepng($dst_r,$destino);

        $targ_w = 32;
        $targ_h = 32;   

        $img_r = imagecreatefrompng($src);
        $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

        imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$w,$h);
        $nombref="user-".$usr_id."-32x32.png";
        $destino="images/users/$nombref";
        imagepng($dst_r,$destino);
        
        mysql_query("UPDATE usuario SET avatar=1 WHERE usuario_id='$usr_id'");

        //Se borran las temporales
        unlink("./tmp/user-".$usr_id."2.png");
        unlink("./tmp/user-".$usr_id.".img");
        header("location: $SERVIDOR/opciones/imagen-lista/".time());
        exit();
    }
    $imagenUrl = "./images/users/user-".$usr_id."-perfil.png";
    $datos = getimagesize($imagenUrl); 
    $anchura=$datos[0]; 
    $altura=$datos[1];
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
            <img id="jcrop_target" src="<?php echo $imagenUrl."?".time(); ?>">
        </div>
    </div>
    <div class="div-previsualizar-mini">
        <div class="div-mensaje">
            <span>Selecciona tu miniatura!</span>
        </div>
        <div class="div-miniatura">
            <img id="preview" src="<?php echo $imagenUrl."?".time(); ?>">
        </div>
        <div class="div-boton">
            <input type="button" value="Listo!" id="btn-listo">
        </div>
    </div>
    <form id="form-recorte" onsubmit="return checkCoords();" method="post" action="./opciones/imagen-miniatura/<?php echo time(); ?>">
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
            setSelect: [ 0, 0, 48, 48 ],
            minSize: [48,48],
            aspectRatio: 1
        });
    });
    function showPreview(coords)
    {
        var rx = 48 / coords.w;
        var ry = 48 / coords.h;

        $('#preview').css({
            width: Math.round(rx * <?php echo $anchura; ?>) + 'px',
            height: Math.round(ry * <?php echo $altura; ?>) + 'px',
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
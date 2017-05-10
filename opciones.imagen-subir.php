<?php
    if (isset($_FILES['fotousuario'])){
        $nombre = $_FILES['fotousuario']['name'];
        $tipo = $_FILES['fotousuario']['type']; 
        $tam = $_FILES['fotousuario']['size'];
        $nombreTmp = $_FILES['fotousuario']['tmp_name'];    
    }
    else{
        echo "Ha ocurrido un error.";
        exit();
    }
    
    $tamMax = 700000;

    $infoImg = getimagesize($nombreTmp);
    $ancho = $infoImg[0];
    $alto = $infoImg[1];

    $mensaje = '<img src="images/template/loader_posts.gif">';
    $error = false;
    $cerrar = '<p><a href="javascript:;" onclick="cierrame();">Cerrar</a></p>';

    if (!preg_match("/image\/(png|jpeg)$/i", $tipo)){
        $mensaje = "El tipo de archivo es inválido... Solo se permiten: png, jpeg, jpg. $cerrar";
        $error = true;
    }
    elseif ($tam>$tamMax){
        $mensaje = "La imagen no debe tener un tamaño superior a 700kb. $cerrar";
        $error = true;
    }
    elseif ($ancho<160 || $alto<160){
        $mensaje = "El tamaño mínimo de la imagen debe ser 160px de ancho por 160px de alto. $cerrar";
        $error = true;
    }

    if (!$error){
        // se sube el archivo
        $extension = ($tipo == "image/png") ? "png" : "jpg";
        //$destino = "./tmp/user-$usr_id.".$extension;
        $destino = "./tmp/user-".$usr_id.".img";
        
        //@unlink("./tmp/user-$usr_id.png");  
        //@unlink("./tmp/user-$usr_id.jpg");         

        if (move_uploaded_file($nombreTmp, $destino)){
            header("location: $SERVIDOR/opciones/imagen-recortar/$extension/".time());
            exit();
        }
        else{
            $mensaje="Error al procesar la imagen. :( $cerrar";
        }
    }
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
</head>
<body>
    <div class="div-foto-animacion">
        <?php echo $mensaje; ?>
    </div>
</body>
<script type="text/javascript">
    var cierrame = function(){
        top.location.reload();
    }    
</script>
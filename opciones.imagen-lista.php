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
    <div class="div-foto-animacion verde-grande">
        Ya ha finalizado la configuración de su foto de perfil...
        <p>Espere un momento...</p>
    </div>
</body>

<script type="text/javascript">
    window.onload = function(){
        setTimeout(cierrame, 5000);
    }
    var cierrame = function(){
        top.location.reload();
    }    
</script>
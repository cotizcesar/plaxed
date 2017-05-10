<?php
    $contenido = isset($_GET['contenido']) ? $_GET['contenido']." " : '';
    $contenido = urldecode($contenido);
    $via = isset($_GET['via']) ? ' vía #'.$_GET['via'] : '';
    $via = urldecode($via);
    $ruta = isset($_GET['ruta']) ? $_GET['ruta'] : '';
    if (function_exists('curl_init')){
        $curl = curl_init(); 
        curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url'); 
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1); 
        $datos = array('longUrl'=>$ruta);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
        $salida = curl_exec($curl);
        curl_close($curl);
        if ($salida){
            $json = json_decode($salida);
            $ruta = $json->id;
        }
    }

    //$ruta = urldecode($ruta);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Plaxed.com</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="ROBOTS" content="NOODP" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <base href="<?php echo $SERVIDOR; ?>" />
    <link rel="icon" type="image/png" href="favicon.png" />
    <link rel="stylesheet" type="text/css" href="css/compartir.css">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/spval-1.js"></script>
</head>
<body>
    <div class="form-compartir">
        <label id="etiqueta">Compartir en Plaxed:</label><label id="cuenta"></label>
        <div class="clear"></div>
        <textarea id="texto" name="texto"><?php echo "$contenido$ruta$via"; ?></textarea>
        <input type="button" value="Plaxear!" id="btn-compartir">
        <div class="clear"></div>
    </div>
</body>
<script type="text/javascript">
    $('#texto').on('keypress keydown keyup', function(){
        $('#cuenta').html(200-$(this).val().length);
    });
    $(document).ready(function(){
        $('#cuenta').html(200-$('#texto').val().length);
        $('#btn-compartir').on('click', function(){
            var texto = $('#texto').val();
            texto = $.trim(texto);
            var longitud = texto.length;
            if (longitud>0 && longitud<=200){
                /*
                // INICIO DE ACORTAMIENTO
                
                var links = texto.match(/http:\/\/\S+/gi);
                console.log(texto);
                console.log(links);
                return false;
                var xhr = new XMLHttpRequest();
                xhr.onload=function(){
                    var nuevaUrl = $.parseJSON(xhr.responseText);
                    texto = texto.replace(nuevaUrl.longUrl, nuevaUrl.id);
                }
                for (i=0;i<links.length;i++){
                    xhr.open("POST","https://www.googleapis.com/urlshortener/v1/url", false);               
                    xhr.setRequestHeader("Content-type", "application/json");
                    xhr.setRequestHeader("rX-JavaScript-User-Agent", "Google APIs Explorer");
                    xhr.send(JSON.stringify({'longUrl': links[i]}));
                }
                //FIN DE ACORTAMIENTO
                */
                texto = encodeURIComponent(texto);
                texto = texto.replace(/\+/g,'%2B');
                $.ajax({
                    url: 'index.php?enviarPost',
                    type: 'POST',
                    data: 'txt='+texto+'&en_respuesta=0&modulo=marcador',
                    success: function(data, textStatus, xhr){
                        var pub = $.parseJSON(data);
                        if (pub.respuesta=="error"){
                            alert(pub.mensaje);
                            $('#cuenta').html(200-$('#texto').val().length);
                            $('#btn-compartir').removeAttr('disabled');
                            $('#btn-compartir').attr('value','Plaxear!');
                            $('#texto').removeAttr('disabled');
                        }
                        else{    
                            alert(pub.mensaje);                        
                            $('#texto').val('');
                            location.href='./';
                        }                        
                    },
                    ajaxSend: function(){
                        $('#btn-compartir').attr('value','Plaxeando...');
                        $('#btn-compartir').attr('disabled','disabled');
                        $('#texto').attr('disabled','disabled');
                    },
                    error: function(xhr, textStatus, errorThrown){
                        $('#btn-compartir').attr('value','Plaxear!');
                        $('#btn-compartir').removeAttr('disabled');
                        $('#texto').attr('disabled','disabled');
                        alert('Ha ocurrido un error. Es posible que no se haya publicado.');
                    }
                });
            }                
        });
    });
</script>
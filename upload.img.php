<?php
    $clave = "82fe883efe8cc478881aaf50561e276a";
    $nombreTmp = $_FILES['archivo']['tmp_name'];  
    $nombre = $_FILES['archivo']['name'];  
    $tipo = $_FILES['archivo']['type']; 
    $tam = $_FILES['archivo']['size'];
    $tamMax = 1000000;
    if (!preg_match("/image\/(png|jpeg|gif|x-png|pjpeg)$/i", $tipo)){
        echo json_encode(array("respuesta"=>"error", "mensaje"=>"Tipo de archivo inválido! no se permite $tipo"));
        exit();
    }
    if ($tam>$tamMax){
        echo json_encode(array("respuesta"=>"error", "mensaje"=>"El tamaño de la imagen no debe exceder 1MB."));
        exit();
    }
    
    $handle = fopen($nombreTmp, "r");
    $data = fread($handle, filesize($nombreTmp));

    // $data is file data
    $pvars   = array('image' => base64_encode($data), 'key' => $clave);
    $timeout = 30;
    $curl    = curl_init();

    curl_setopt($curl, CURLOPT_URL, 'http://api.imgur.com/2/upload.json');
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);

    $json = json_decode(curl_exec($curl));

    curl_close ($curl);
    echo json_encode(array("respuesta"=>"ok", "mensaje"=>"Archivo subido.", "nombre"=>"$nombre", "original" => $json->upload->links->original, "miniatura" => $json->upload->links->small_square));
?>
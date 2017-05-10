<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    date_default_timezone_set("America/Caracas");
    function LinkBD(){
        $c=mysql_connect("localhost","root","");
        mysql_select_db("plaxedco_nuevo", $c);
		mysql_query("SET NAMES 'UTF8'");
        mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
        return $c;
    }
    function LinkBD1(){
        $c=mysql_connect("localhost","plaxedco","501878Plaxed+");
        mysql_select_db("plaxedco_nuevo", $c);
		mysql_query("SET NAMES 'UTF8'");
        mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
        return $c;
    }
    function dif_fechas($fi, $ff){
        $f1=strtotime($fi);
        $f2=strtotime($ff);
        $mk1 = mktime(date("H", $f1),date("i", $f1),date("s", $f1),date("n", $f1),date("j", $f1),date("Y", $f1));
        $mk2 = mktime(date("H", $f2),date("i", $f2),date("s", $f2),date("n", $f2),date("j", $f2),date("Y", $f2));
        $segundos=floor($mk2-$mk1);
        $minutos=floor($segundos/60);
        $horas=floor($minutos/60);
        $dias=floor($horas/24);
        $meses=floor($dias/30);
        $anos=floor($meses/12); 
        $extra="";
        $salida ="";
        if ($anos>0){
            if ($anos==1)
                $extra="año";
            else
                $extra="años";
            $salida = "$anos $extra";
        }
        elseif ($meses>0){
            if ($meses==1)
                $extra="mes";
            else
                $extra="meses";
            $salida = "$meses $extra";
        }
        elseif ($dias>0){
            if ($dias==1)
                $extra="día";
            else
                $extra="días";
            $salida = "$dias $extra";
        }
        elseif ($horas>0){
            if ($horas==1)
                $extra="hora";
            else
                $extra="horas";
            $salida = "$horas $extra";
        }
        elseif ($minutos>0){
            if ($minutos==1)
                $extra="minuto";
            else
                $extra="minutos";
            $salida = "$minutos $extra";
        }
        else{
            if ($segundos==1)
                $extra="segundo";
            else
                $extra="segundos";
            $salida = "$segundos $extra";
        }
        return $salida;
    }
    function parteEntera($num){
        $x = explode(".", $num);
        return $x[0];
    }

    function texto_a_url($text){
        // pad it with a space so we can match things at the start of the 1st line. 
        $ret = ' ' . $text;

        $ret = preg_replace("/</", "&lt;", $ret);
        $ret = preg_replace("/>/", "&gt;", $ret);
         
        // matches an "xxxx://yyyy" URL at the start of a line, or after a space. 
        // xxxx can only be alpha characters. 
        // yyyy is anything up to the first space, newline, comma, double quote or < 
        //$ret = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3" target="_blank">\2://\3</a>', $ret);
        //$ret = preg_replace("#([\t\r\n ])(https?://(\S+|$))#i", '\1<a href="\2\2.1" target="_blank">\2\2.1</a>', $ret);
         
        // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing 
        // Must contain at least 2 dots. xxxx contains either alphanum, or "-" 
        // zzzz is optional.. will contain everything up to the first space, newline,  
        // comma, double quote or <. 
        //$ret = preg_replace("#([\t\r\n ])((www|ftp)\.)(([\w\-\+]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\3.\4" target="_blank">\3.\4</a>', $ret);

        

        $ret = preg_replace("#([\t\r\n ])(https?://)(([\w\-\+]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2\3" target="_blank">\2\3</a>', $ret);
        $ret = preg_replace("#([\t\r\n ])([a-z0-9][\w\-]*)(\.[a-z0-9][\w\-]+)(\S+)*#i", '$1<a href="http://$2$3$4" target="_blank">$2$3$4</a>', $ret);
        

        $ret = preg_replace("/([\s])#([\wáéíóúÁÉÍÓÚñÑÇç]+)/i", '$1<a href="./tema/$2$3" rel="nofollow">#$2$3</a>', $ret);
        $ret = preg_replace("/([\s\¿\?\(\)\.,!¡\-#])@([\w]+)/i", '$1<a href="./u/$2$3" rel="nofollow">@$2</a>', $ret);
         
        // Remove our padding.. 
        $ret = substr($ret, 1);
         
        return($ret);
    }

    function tagsOff($txt){
        $txt = str_replace("<", "&lt;", $txt);
        $txt = str_replace(">", "&gt;", $txt);
        return $txt;
    }

    function obtenerMenciones($txt, $autor, $me, $arroba=true){
        $patron_menciones = "/(^|[\s\¿\?\(\)\.,!¡\-#])@(\w+)/i";
        preg_match_all($patron_menciones, $txt, $coincidencias);

        $salida = "";
        $arrAlias = array();
        $arrAliasTmp = array();
        $me = strtolower($me);
        if (strtolower($autor)!=$me){
            $arrAlias[]="@".$autor;
            $arrAliasTmp[] = strtolower($autor);
        }            
        foreach ($coincidencias[2] as $mencionado){
            #$salida.="@".$mencionado;
            $men = strtolower($mencionado);
            if (!in_array($men,$arrAliasTmp) && $men!=$me ){
                $arrAliasTmp[]=$men;
                if ($arroba)
                    $arrAlias[]="@".$mencionado;
                else
                    $arrAlias[]=$mencionado;
            }   
        }
        $salida = implode(" ", $arrAlias);
        return $salida;
    }

    function url($url){
        $url = preg_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_+.~#?&//=]+)','<a href="\0">\0</a>', $url);
        $url = preg_replace('(((f|ht){1}tps://)[-a-zA-Z0-9@:%_+.~#?&//=]+)','<a href="\0">\0</a>', $url);
        $url = preg_replace('/w{3}.[a-zA-Z0-9_-]*.[a-z]*.[a-z]*$/','\1<a href="http://\0">\0</a>', $url);
        $url = preg_replace('([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})','<a href="mailto:\0">\0</a>', $url);
        return $url;
    }
   
    function extraerYoutube($txt){
        //$txt = "Jesús R Cabrera S- @jrcsDev http://www.youtube.com/watch?v=zhzEBKLt4DI&feature=g-all-lik http://www.youtube.com/watch?v=iqXc-stEsvQ&feature=g-all-lik youtu.be/sI4qLB8uCQM http://www.youtube.com/watch?feature=endscreen&v=sI4qLB8uCQM&NR=1";
        $txt = " ".$txt;
        $patron = "/\s(https?:\/\/)?(www\.)?(youtube.com\/[\w\?&\=]+v=([\w_\-]+)|youtu.be\/([\w_\-]+))/i";
        preg_match_all($patron, $txt, $coincidencias);

        $video="";  
        $vid = array();
        foreach ($coincidencias[4] as $id_video){
            if ($id_video!="")
                $vid[]=$id_video;
        }
        foreach ($coincidencias[5] as $id_video){
            if ($id_video!="")
                $vid[]=$id_video;
        }
        foreach ($vid as $id_video){
            if ($video!="")
                $video."";
            //$video.="<object style=\"height: 390px; width: 640px\"><param name=\"movie\" value=\"http://www.youtube.com/v/$id_video?version=3&feature=player_detailpage\"><param name=\"allowFullScreen\" value=\"true\"><param name=\"allowScriptAccess\" value=\"always\"><embed wmode=\"transparent\" src=\"http://www.youtube.com/v/$id_video?version=3&feature=player_detailpage\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" allowScriptAccess=\"always\" width=\"640\" height=\"360\"></embed></object>";
            $video.="<div class=\"video-youtube\"><object style=\"height: 224px; width: 398\"><param name=\"wmode\" value=\"transparent\"><param name=\"movie\" value=\"http://www.youtube.com/v/$id_video?version=3&feature=player_detailpage\"><param name=\"allowFullScreen\" value=\"true\"><param name=\"allowScriptAccess\" value=\"always\"><embed wmode=\"transparent\" src=\"http://www.youtube.com/v/$id_video?version=3&feature=player_detailpage\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" allowScriptAccess=\"always\" width=\"398\" height=\"224\"></object></div>";
        }
        if (empty($video))
            $video=false;
        return $video;
    }
    function noCache() {
        header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }
    function cadenaAzar(){
        $n = 20;
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $string = "";  
        $l = strlen($characters)-1;

        for ($i = 0; $i < $n; $i++) {
            $string .= $characters[rand(0, $l)];
        }
        return $string;
    }
    function obtenerIP(){
        $realip=0;
        if ($_SERVER) {
        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif ( isset($_SERVER["HTTP_CLIENT_IP"]) ) {
        $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
        $realip = $_SERVER["REMOTE_ADDR"];
        }
        } else {
        if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
        $realip = getenv( 'HTTP_X_FORWARDED_FOR' );
        } elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
        $realip = getenv( 'HTTP_CLIENT_IP' );
        } else {
        $realip = getenv( 'REMOTE_ADDR' );
        }
        }
        return $realip;
    }
?>

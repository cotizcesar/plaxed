<?php
    $sesion = (isset($_SESSION['xyz12345_conectado'])) ? $_SESSION['xyz12345_conectado'] : false;
    if ($sesion == "esto_es_para_navegar"){
        $usr_id = $_SESSION['xyz12345_id'];
        $r_ses = mysql_query("SELECT alias, nombre, usuario_activo FROM usuario WHERE usuario_id='$usr_id'");
        if (mysql_num_rows($r_ses)==1){
            $rs_ses = mysql_fetch_array($r_ses);
            $usr_alias = trim($rs_ses[0]);
            $usr_nombre = trim($rs_ses[1]);
            $usr_activo = $rs_ses[2];
            if ($usr_activo!=1 || empty($usr_alias) || empty($usr_nombre)){
                header("location: ./salida");
                exit();
            }
        }
        else{
            $sesion=false;
        }
    }
    else
    {
        $sesion = false;
    }
?>
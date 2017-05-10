<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    mysql_query("DELETE FROM usuario_online WHERE usuario_id='$usr_id'");
    unset($_SESSION['xyz12345_conectado']);
    unset($_SESSION['xyz12345_id']);
    header('location: ./');
?>

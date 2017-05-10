<?php
    if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    if ($modulo=="stats"){
        $r_st = mysql_query("select alias, nombre, posts, puntos from usuario where posts>0 order by puntos desc, posts desc limit 5");
        echo "<table border=\"1\">
        <tr>
            <td>ALIAS</td>
            <td>NOMBRE</td>
            <td>POSTS</td>
            <td>PUNTOS</td>
        </tr>";
        while ($rs_st=mysql_fetch_array($r_st)){
            echo "
            <tr>
                <td>$rs_st[0]</td>
                <td>$rs_st[1]</td>
                <td>$rs_st[2]</td>
                <td>$rs_st[3]</td>
            </tr>
            ";

            
        }
        echo "</table>";
        exit();
    }
?>
<?php
    if ($usr_id==5){
        echo "-.- ... Ni lo pienses...";
        exit();
    }
    $SERVIDOR=$SERVIDOR."feedback/";
    $accion = (isset($parametros[0])) ? $parametros[0] : false;
    $actividad_id = 0;
    if ($parametros){
        $accion = $parametros[0];
        // Validacion de procesos
        if ($accion=="actividad_registrar"){
            include("feedback.agregar.php");
            exit();
        }
        if ($accion=="actividad_comentar"){
            include("feedback.actividad.php");
            exit();
        }
        if ($accion == "salir"){
            header("location: ../");
        }
    }
        
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <base href="<?php echo $SERVIDOR; ?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="../css/feedback.css">
        <script type="text/javascript" src="../js/spval-1.js"></script>
        <script type="text/javascript" src="../js/jquery-1.8.1.min.js"></script>
        <title>Desarrollo Plaxed (alpha): Control de Actividades</title>
    </head>
    <body>
        <div id="id-contenido">
            <header>
                <hgroup>
                    <h1>Desarrollo Plaxed (Alpha)</h1>
                    <h2>Listado de Actividades</h2>
                </hgroup>
                <nav>
                    <div id="div-navegacion">
                        <a href="./">Inicio</a>
                        <a href="./salir">Salir</a>
                    </div>
                </nav>
                <section>
                    <div id="div-section">
                        <?php
                            $ruta = "feedback.principal.php";
                            if ($accion=="actividad_agregar"){
                                $ruta = "feedback.agregar.php";
                            }
                            if ($accion=="actividad"){
                                $ruta = "feedback.actividad.php";
                            }

                            include($ruta);
                        ?>
                    </div>
                </section>
            </header>
        </div>
    </body>
</html>

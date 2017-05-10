<?php
	$accion = $parametros[1];
	if ($accion == "actualizar"){
		$respuesta = array("respuesta"=>"ok", "mensaje"=>"El perfil ha sido actualizado");
		try {
			$plaxed->actualizarPerfil($_POST, $sesion->id);
		} catch (Exception $e) {
			$respuesta = array("respuesta"=>"error", "mensaje"=>$e->getMessage());
		}
		echo json_encode($respuesta);
	}
?>
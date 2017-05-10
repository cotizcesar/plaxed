<form action="./clave" method="post">
	<input name="clave" id="clave" placeholder="Clave aqui..." >
	<br><br><br>
	<input type="submit" value="Enviar">
</form>
<?php
	if (isset($_POST)){
		$clave = isset($_POST['clave']) ? $_POST['clave'] : "";
		if (!empty($clave)){
			$clave = sha1(md5($clave));
			echo "<br><br><br>La clave encriptada es: ".$clave;	
		}
	}
?>
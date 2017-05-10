<head>
    <title>Plaxed.com</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="ROBOTS" content="NOODP" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <base href="<?php echo $SERVIDOR; ?>" />
<?php
	$r_val = mysql_query("SELECT solicitudes_pendientes_id, correo, dato1, dato2 FROM solicitudes_pendientes WHERE correo='$parametros[0]' and dato1='$parametros[1]' and dato2='$parametros[2]' and tipo='confirmar_cuenta'");
	if (mysql_num_rows($r_val)==1):
		$rs_val = mysql_fetch_array($r_val);
		mysql_query("UPDATE usuario SET usuario_activo='1', usuario_confirmado='1' WHERE correo='$rs_val[1]'");
		mysql_query("DELETE FROM solicitudes_pendientes WHERE solicitudes_pendientes_id='$rs_val[0]'");
?>
<script type="text/javascript">
	window.onload = function(){
		alert('Tu cuenta ha sido verificada. Ahora puedes iniciar sesión...');
		location.href='./';
	}
</script>
<?php
	else:
?>
<script type="text/javascript">
	window.onload = function(){
		alert('No existe cuenta por verificar...');
		location.href='./';
	}
</script>
<?php
	endif;
?>
</head>
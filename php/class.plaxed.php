<?php
	include("php/phpmailer/class.phpmailer.php");
    include("php/phpmailer/class.smtp.php");
	class cPlaxed{
		var $conex;	
		var $APP;
		var $usr;
		function __construct($id){
			$this->APP = new cAPP();
			$this->conectar();
			$this->usr = new cSesion($id);
		}		 
		private function conectar(){
			if ($this->APP->servidor=='localhost'){
				if (!$this->conex = mysql_connect('localhost', 'root', ''))
					throw new Exception("Error al conectar");
				if (!mysql_select_db('plaxedco_nuevo', $this->conex))
					throw new Exception("Error al accesar a la base de datos");
			}
			else{
				if (!$this->conex = mysql_connect('localhost', 'plaxedco', '501878Plaxed+'))	
					throw new Exception("Error al conectar");
				if (!mysql_select_db('plaxedco_nuevo', $this->conex))
					throw new Exception("Error al accesar a la base de datos");
			}
		}
		function registrarPost($p){
			//if ($this->usr->id!=2 && $this->usr->id!=9 && $this->usr->id!=11)
			//	throw new Exception("Emmm... cómo te explico? ... No puedes. Ya no.");
			if (!isset($p['txt']) || !isset($p['modulo'])){
				$ar=fopen("envio-formato-invalido.txt","a");
	            fputs($ar,$this->usr->alias." envio: ".print_r($p)."\n");
	            fclose($ar);
				throw new Exception("Formato inválido.");
			}
			$txt = $p['txt'];
			$txt = trim($txt);
			$txt = str_replace("%2B", '"', $txt);
			$enRespuesta = $p['en_respuesta'];
			$cantidad=mb_strlen($txt, 'utf8');
			$hora=date("Y-m-d H:i:s");

        	//formato de la publicacion
            if (empty($txt) || $cantidad>200)
				throw new Exception("La publicación no cumple con el formato.");
            
			//tiempo minimo entre publicaciones
			$r_tiempo = mysql_query("SELECT publicacion_id FROM publicacion WHERE TIMESTAMPDIFF(SECOND, fecha, '$hora')<6 AND usuario_id='".$this->usr->id."'");
			if (mysql_num_rows($r_tiempo)>0)
				throw new Exception("Estás publicacion demasiado rápido.");	

			$r_diarios = mysql_query("SELECT COUNT(publicacion_id) FROM publicacion WHERE DATE(fecha)=DATE('$hora') AND usuario_id='".$this->usr->id."'");
			$rs_diarios = mysql_fetch_array($r_diarios);
			$postDiarios = $rs_diarios[0];
			if ($postDiarios>=1000)
				throw new Exception("Has llegado al límite de publicaciones diarias.");

			$existeUpload = false;
			if (isset($_FILES['archivo'])){
				if(function_exists('curl_init')){
					$nombreTmp = $_FILES['archivo']['tmp_name'];  
				    $nombre = $_FILES['archivo']['name'];  
				    $tipo = $_FILES['archivo']['type']; 
				    $tam = $_FILES['archivo']['size'];
				    //
				    if (!preg_match("/^image\/(png|jpeg|gif|x-png|pjpeg)$/i", $tipo)){
				    	$ar=fopen("upload-tipo-invalido.txt","a");
			            fputs($ar,$usr_alias." tipo: $tipo\n");
			            fclose($ar);
				        echo json_encode(array("respuesta"=>"error", "mensaje"=>"Tipo de archivo inválido! no se permite $tipo"));
				        exit();
				    }
				    if ($tam>PLAXED_UPLOAD_SIZE_MAX){
				        echo json_encode(array("respuesta"=>"error", "mensaje"=>"El tamaño de la imagen no debe exceder 1MB."));
				        exit();
				    }
				    //
				    $handle = fopen($nombreTmp, "r");
    				$data = fread($handle, filesize($nombreTmp));
    				// $data is file data
				    $pvars   = array('image' => base64_encode($data), 'key' => IMGUR_KEY);
				    $timeout = 30;
				    $curl    = curl_init();
				    curl_setopt($curl, CURLOPT_URL, 'http://api.imgur.com/2/upload.json');
				    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
				    curl_setopt($curl, CURLOPT_POST, 1);
				    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				    curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
				    $json = json_decode(curl_exec($curl));
    				curl_close ($curl);

    				if (!$json){
				        echo json_encode(array("respuesta"=>"error", "mensaje"=>"No se pudo cargar la imagen."));
				        exit();
				    }
				    if (method_exists($json, 'error')){
				        echo json_encode(array("respuesta"=>"error", "mensaje"=>"No se pudo cargar la imagen."));
				        exit();
				    }
				    $existeUpload = true;
				    $adjuntoOriginal = $json->upload->links->original;
				    $adjuntoMiniatura = $json->upload->links->small_square;
				}
				else{
					throw new Exception("La configuración del Servidor no permite subir archivos.");
				}
			}
			$conversacion=0;
			$r=mysql_query("SELECT conversacion_id FROM publicacion where publicacion_id='$enRespuesta'");
			if (mysql_num_rows($r)>0){
				$rs=mysql_fetch_array($r);
				$conversacion=$rs[0];
			}			
			// Si no hay conversación
			if ($conversacion==0){
				mysql_query("INSERT INTO conversacion (usuario_id) VALUES ('".$this->usr->id."')");
				$conversacion=mysql_insert_id();
			}

			if (mysql_error())
				throw new Exception("Ha ocurrido un error relacionado a la conversación.");

			$r_anterior=mysql_query("SELECT contenido FROM publicacion WHERE usuario_id='".$this->usr->id."' ORDER BY publicacion_id DESC LIMIT 1");
			$rs_anterior=mysql_fetch_array($r_anterior);

			if ($txt==$rs_anterior[0])
				throw new Exception("Esto ya ha sido publicado.");	

			if (!get_magic_quotes_gpc())
				$txt=mysql_real_escape_string($txt);
			
			$adjuntoValor = ($existeUpload) ? 1 : 0;
			mysql_query("INSERT INTO publicacion (conversacion_id,respuesta_id,usuario_id,contenido,fecha,adjunto) VALUES ('$conversacion', '$enRespuesta', '".$this->usr->id."', '$txt', '$hora', '$adjuntoValor')");
			$nuevoId = mysql_insert_id();

			if ($existeUpload){
				mysql_query("INSERT INTO publicacion_adjunto (usuario_id, publicacion_id,original,miniatura) VALUES ('".$this->usr->id."','$nuevoId','$adjuntoOriginal', '$adjuntoMiniatura')");
			}

			$menciones = obtenerMenciones($txt, $this->usr->alias, $this->usr->alias, false);
			$menciones = explode(" ", $menciones);
			foreach ($menciones as $mencionado){
				$r_um=mysql_query("SELECT usuario_id,alias, correo, nombre FROM usuario WHERE alias='$mencionado'");
				if (mysql_num_rows($r_um)==1){
					$rs_um=mysql_fetch_array($r_um);
					$r_mp = mysql_query("SELECT usuario_destino_id FROM mencion where publicacion_id='$nuevoId' AND usuario_destino_id='$rs_um[0]'");
					if (mysql_num_rows($r_mp)==0){
						mysql_query("INSERT INTO mencion (usuario_origen_id,usuario_destino_id,publicacion_id) VALUES ('".$this->usr->id."','$rs_um[0]','$nuevoId')");
						$mencion_id=mysql_insert_id();
						mysql_query("INSERT INTO notificacion (publicacion_id,usuario_origen_id,usuario_destino_id,tipo,destino_id,visto,fecha) VALUES ('$nuevoId','".$this->usr->id."','$rs_um[0]','mencion','$mencion_id','0','$hora')");
						//include("correo.menciones.php"); //Incluir este archivo activa el envio de notificaciones en el correo para cada mencion
					}
				}				
			}
			if (mysql_error())
				throw new Exception("Ha ocurrido un error relacionado a las menciones.");

			mysql_query("UPDATE usuario SET posts=(posts+1) WHERE usuario_id='".$this->usr->id."'");
			if (mysql_error())
				throw new Exception("Ha ocurrido un error relacionado al total de posts.");

		}

		function enviarPuntuacion($p){
			$publicacion_id=$p['publicacion_id'];
			$signo=$p['signo'];
			$signo=($signo=='y' || $signo=='n') ? $signo : false;
			$usrId = $this->usr->id;
			if (empty($publicacion_id) || !$signo)
				throw new Exception("Datos incompletos.");

			$fecha=date("Y-m-d H:i:s");
			$r_lim=mysql_query("SELECT usuario_id FROM publicacion_voto WHERE DATE(fecha)=DATE('$fecha') AND usuario_id='$usrId'");
			$numVotosHoy = mysql_num_rows($r_lim);
			if ($numVotosHoy>=10)
				throw new Exception("Has llegado al límite de puntos diarios.");
			$numVotosRestantes = (10-$numVotosHoy)-1;

			$r_pub=mysql_query("SELECT usuario_id,puntos FROM publicacion WHERE publicacion_id='$publicacion_id'");
			if (mysql_num_rows($r_pub)==0)
				throw new Exception("La publicación ha sido eliminada.");

			$rs_pub = mysql_fetch_array($r_pub);
			$usrPostId = $rs_pub[0];
			$puntos = $rs_pub[1];
			if ($usrPostId==$usrId)
				throw new Exception("No puedes puntuar sobre tu propia publicación.");

			$r_voto=mysql_query("SELECT publicacion_voto_id FROM publicacion_voto WHERE publicacion_id='$publicacion_id' AND usuario_id='$usrId'");
			if (mysql_num_rows($r_voto)>0)
				throw new Exception("Ya has puntuado sobre esta publicación.");

			$r_puntou=mysql_query("SELECT puntos FROM usuario WHERE usuario_id='$usrPostId'");
			$rs_puntou = mysql_fetch_array($r_puntou);
			$puntosu = $rs_puntou[0];

        	if ($signo=='y'){
        		$signo='+';
        		$puntos++;
        		$puntosu++;
        	}
        	else{
        		$signo='-';
        		$puntos--;
        		$puntosu--;
        	}
        	$fecha=date("Y-m-d H:i:s");
        	mysql_query("INSERT INTO publicacion_voto (publicacion_id,usuario_id,usuario_destino_id,voto,fecha) 
        				VALUES ('$publicacion_id','$usrId','$usrPostId','$signo','$fecha')");
        	if (mysql_error())
        		throw new Exception("Ocurrió un error al intentar registrar la puntuación.");
        	$destino_id = mysql_insert_id();
        	mysql_query("UPDATE usuario SET puntos='$puntosu' WHERE usuario_id='$usrPostId'");
        	if (mysql_error())
        		throw new Exception("Ocurrió un error al intentar actualizar la puntuación global.");
        	mysql_query("UPDATE publicacion SET puntos='$puntos' WHERE publicacion_id='$publicacion_id'");
        	if (mysql_error())
        		throw new Exception("Ocurrió un error al intentar actualizar la puntuación del post.");
        	// Crear la notificacion
        	mysql_query("INSERT INTO notificacion (publicacion_id,usuario_origen_id,usuario_destino_id,tipo,destino_id,visto,fecha) 
                               VALUES ('$publicacion_id','$usrId','$usrPostId','puntuacion','$destino_id','0','$fecha')");
        	if (mysql_error())
        		throw new Exception("Ocurrió un error al registrar la notificación.");

        	return array("post"=>$puntos,"disponibles"=>$numVotosRestantes);
		}

		function eliminarPost($p){
			$usrId = $this->usr->id;
			$publicacion_id = (isset($p['publicacion_id'])) ? $p['publicacion_id'] : false;
			if (!$publicacion_id)
				throw new Exception("Error! el envío de datos ha fallado.");

			$r=mysql_query("SELECT publicacion_id,usuario_id FROM publicacion WHERE publicacion_id='$publicacion_id'");
			if (mysql_num_rows($r)==0)
				throw new Exception("La publicación no existe.");
			$rs = mysql_fetch_array($r);
			if ($usrId!=$rs[1])
				throw new Exception("No puedes eliminar la publicación de otro usuario.");

			mysql_query("DELETE FROM publicacion WHERE publicacion_id='$publicacion_id'");
			mysql_query("UPDATE usuario SET posts=(posts-1) WHERE usuario_id='$usrId'");
		}

		function actualizarPerfil($p, $id){
			$nombre = isset($p['fnombre']) ? $p['fnombre'] : false;
			$biografia = isset($p['fbiografia']) ? $p['fbiografia'] : false;
			$ubicacion = isset($p['fubicacion']) ? $p['fubicacion'] : false;
			$clave1 = isset($p['fclave1']) ? $p['fclave1'] : false;
			$clave2 = isset($p['fclave2']) ? $p['fclave2'] : false;
			$tags = isset($p['ftags']) ? $p['ftags'] : false;
			$tags = str_replace(" ", "", $tags);

			if  (!$nombre)
				throw new Exception("El campo [Nombre] es obligatorio. $nombre");
			if  (!$tags)
				throw new Exception("El campo [Etiquetas] es obligatorio.");
			if (mb_strlen($biografia, 'utf8')>200)
				throw new Exception("El campo [Biografía] no debe exceder los 200 caracteres.");
			$opcional="";
			if ($clave1){
				if ($clave1==$clave2){
					$nuevaClave = sha1(md5($clave1));
					$opcional=",clave='$nuevaClave'";
				}
				else{
					throw new Exception("Los campos [Clave] y [Confirmación de clave] no coinciden.");
				}
			}
			$nombre=tagsOff($nombre);
			mysql_query("UPDATE usuario set nombre='$nombre',biografia='$biografia',ubicacion='$ubicacion',tags='$tags'$opcional WHERE usuario_id='$id'");
			if (mysql_error())
				throw new Exception("Ocurrió un error al intentar actualizar.");
		}
		function solicitarConexion($p){
			// 0 : reposo
			// 1 : pendiente
			//if (!$this->APP->esLocal())
			//	throw new Exception("Ya va! vamos con calma XD");			 	
			$idDestino = (isset($p['idusuario'])) ? $p['idusuario'] : false;
			$idOrigen = $this->usr->id;
			$conectar = false;
			$fecha = date("Y-m-d H:i:s");
			$existeSolicitud = false;
			if ($idDestino==$idOrigen)
				throw new Exception("Error! No seas tan #ForeverAlone.");

			if (!$idDestino)
				throw new Exception("Error en la solicitud.");

			$r_bloq = mysql_query("SELECT bloqueo_id FROM bloqueo WHERE usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino'");
			if (mysql_num_rows($r_bloq)>0){
				throw new Exception("No puedes crear conexión con un usuario que has bloqueado.");
			}

			$r_bloq = mysql_query("SELECT bloqueo_id FROM bloqueo WHERE usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen'");
			if (mysql_num_rows($r_bloq)>0){
				throw new Exception("Imposible solicitar conexión.");
			}			

			$r_cact = mysql_query("SELECT conexion_id FROM conexion WHERE (usuario1_id='$idOrigen' AND usuario2_id='$idDestino') OR (usuario1_id='$idDestino' AND usuario2_id='$idOrigen')");
			if (mysql_num_rows($r_cact)>0){
				throw new Exception("Ya existe una conexión con este usuario.");
			}
			$r_soc = mysql_query("SELECT estado FROM solicitud WHERE usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino'");
			if (mysql_num_rows($r_soc)>0){
				$rs_soc = mysql_fetch_array($r_soc);
				$estado = $rs_soc[0];
				if ($estado == 1)
					throw new Exception("Ya has enviado la solicitud.");
				$existeSolicitud = true;
			}			
			
			// Verificar si tengo una conexion pendiente con este usuario, en ese caso, se conecta de una vez
			$r_conex = mysql_query("SELECT estado FROM solicitud WHERE usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen'");
			if (mysql_num_rows($r_conex)>0){
				$rs_conex = mysql_fetch_array($r_conex);
				$estado = $rs_conex[0];
				if ($estado == 1){
					$conectar = true;
				}
			}
			$fecha = date('Y-m-d H:i:s');
			if ($conectar){
				mysql_query("INSERT INTO conexion (usuario1_id,usuario2_id,fecha) VALUES ('$idDestino','$idOrigen','$fecha')");
				mysql_query("UPDATE solicitud SET estado='0' WHERE (usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino') OR (usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen')");
				mysql_query("UPDATE usuario SET conexiones=(conexiones+1) WHERE usuario_id='$idOrigen' OR usuario_id='$idDestino'");
				//notificacion
				mysql_query("INSERT INTO notificacion (usuario_origen_id, usuario_destino_id,destino_id,tipo,fecha) 
								VALUES ('$idOrigen','$idDestino','$idDestino','conexion','$fecha')");
				mysql_query("INSERT INTO notificacion (usuario_origen_id, usuario_destino_id,destino_id,tipo,fecha, visto) 
								VALUES ('$idDestino','$idOrigen','$idOrigen','conexion','$fecha', '1')");
				return array("respuesta"=>"ok", "mensaje"=>"Se ha creado la conexión.", "codigo"=>"1");
			}
			else{
				if ($existeSolicitud){
					mysql_query("UPDATE solicitud SET estado='1' WHERE usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino'");
				}
				else{
					mysql_query("INSERT INTO solicitud (usuario_origen_id,usuario_destino_id,estado,fecha) VALUES ('$idOrigen','$idDestino','1','$fecha')");
				}
				//se debe enviar la notificacion de la solicitud
				mysql_query("INSERT INTO notificacion (usuario_origen_id, usuario_destino_id,destino_id,tipo,fecha) 
								VALUES ('$idOrigen','$idDestino','$idDestino','conexion-solicitud','$fecha')");
				return array("respuesta"=>"ok", "mensaje"=>"Se ha enviado la solicitud.", "codigo"=>"2");
			}		
		}
		function cancelarConexion($p){
			$idDestino = (isset($p['idusuario'])) ? $p['idusuario'] : false;
			$idOrigen = $this->usr->id;
			if (!$idDestino)
				throw new Exception("Ha ocurrido un error al intentar esta operación.");

			$r_sol = mysql_query("SELECT estado FROM solicitud WHERE usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino' AND estado='1'");
			if (mysql_num_rows($r_sol)>0){
				mysql_query("UPDATE solicitud SET estado='0' WHERE usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino'");
				//borrar notificacion
				mysql_query("DELETE FROM notificacion WHERE usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino' and tipo='conexion-solicitud'");
				return array("respuesta"=>"ok", "mensaje"=>"Se ha cancelado la solicitud.");
			}else
			{	throw new Exception("No existe solicitud a cancelar.");
			}
		}
		function rechazarConexion($p){
			$idDestino = (isset($p['idusuario'])) ? $p['idusuario'] : false;
			$idOrigen = $this->usr->id;
			if (!$idDestino)
				throw new Exception("Ha ocurrido un error al intentar esta operación.");	
			
			$r_conex = mysql_query("SELECT estado,solicitud_id FROM solicitud 
								WHERE usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen' AND estado='1'");
			if (mysql_num_rows($r_conex)>0){
				//borramos la notificacion
				mysql_query("DELETE FROM notificacion WHERE tipo='conexion-solicitud' AND usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen'");
				//ponemos en reposo la solicitud de ambos
				mysql_query("UPDATE solicitud SET estado='0' WHERE (usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino') OR (usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen')");
				return array("respuesta"=>"ok", "mensaje"=>"Se ha rechazado la solicitud");
			}
			else{
				throw new Exception("No existe solicitud a cancelar.");	
			}
		}
		function eliminarConexion($p){
			$idDestino = (isset($p['idusuario'])) ? $p['idusuario'] : false;
			$idOrigen = $this->usr->id;

			if (!$idDestino)
				throw new Exception("Ha ocurrido un error al intentar esta operación.");	

			$r_conex = mysql_query("SELECT conexion_id FROM conexion WHERE (usuario1_id='$idOrigen' AND usuario2_id='$idDestino') OR (usuario1_id='$idDestino' AND usuario2_id='$idOrigen')");
			if (mysql_num_rows($r_conex)>0){
				$rs_conex = mysql_fetch_array($r_conex);
				mysql_query("DELETE FROM conexion WHERE conexion_id = '$rs_conex[0]'");
				mysql_query("UPDATE usuario SET conexiones=(conexiones-1) WHERE usuario_id='$idOrigen' OR usuario_id='$idDestino'");
				// se eliminan las notificaciones
				mysql_query("DELETE FROM notificacion WHERE (tipo='conexion-solicitud' OR tipo='conexion')
									AND ((usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino') 
										OR (usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen'))");
				$salida = array("respuesta"=>"ok", "mensaje"=>"Se ha eliminado la conexión.");
			}
			else{
				$salida = array("respuesta"=>"error", "mensaje"=>"No existe ninguna conexión.");
			}
			return $salida;

		}
		function aceptarConexion($p){
			$idDestino = (isset($p['idusuario'])) ? $p['idusuario'] : false;
			$idOrigen = $this->usr->id;
			if (!$idDestino)
				throw new Exception("Ha ocurrido un error al intentar esta operación.");

			$r_sol = mysql_query("SELECT solicitud_id FROM solicitud WHERE estado='1' 
									AND usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen'");
			if (mysql_num_rows($r_sol)>0){
				
				$r_conex=mysql_query("SELECT conexion_id FROM conexion WHERE (usuario1_id='$idOrigen' AND usuario2_id='$idDestino') 
										OR (usuario1_id='$idDestino' AND usuario2_id='$idOrigen')");
				if (mysql_num_rows($r_conex)!=0)
					throw new Exception("Ya existe la conexión.");
				$fecha=date("Y-m-d H:i:s");
				mysql_query("INSERT INTO conexion (usuario1_id,usuario2_id,fecha) VALUES ('$idDestino','$idOrigen','$fecha')");
				mysql_query("UPDATE solicitud set estado='0'
								WHERE (usuario_origen_id='$idOrigen' AND usuario_destino_id='$idDestino') 
								OR (usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen')");
				//se elimina la notificacion de solicitud para mi
				mysql_query("UPDATE notificacion SET visto='1' WHERE usuario_origen_id='$idDestino' AND usuario_destino_id='$idOrigen' AND tipo='conexion-solicitud'");
				//se crea la notificacion de aceptado para el invitador
				mysql_query("INSERT INTO notificacion (usuario_origen_id,usuario_destino_id,destino_id,tipo,fecha) 
							VALUES ('$idOrigen','$idDestino','$idOrigen','conexion','$fecha')");
				mysql_query("INSERT INTO notificacion (usuario_origen_id,usuario_destino_id,destino_id,tipo,fecha,visto) 
							VALUES ('$idDestino','$idOrigen','$idDestino','conexion','$fecha', '1')");
				//Se suma una conexion a cada usuario
				mysql_query("UPDATE usuario SET conexiones=(conexiones+1) WHERE usuario_id='$idOrigen' OR usuario_id='$idDestino'");

				return array("respuesta" => "ok", "mensaje"=>"Se ha establecido la conexión.");
			}
			else{
				throw new Exception("No existe ninguna petición.");	
			}
			
		}

		function descartarNotificaciones(){
			mysql_query("UPDATE notificacion SET visto='1' WHERE usuario_destino_id='".$this->usr->id."' AND tipo!='conexion-solicitud'");
			if (mysql_error())
				throw new Exception("Ha ocurrido un error al intentar esta operación.");	
		}
		function obtenerSesion($id=0){
			return new cSesion($id);
		}
	}
	class cSesion{
		var $conectado = false;
		var $id = 0;
		var $alias = "";
		var $nombre = "";
		var $activo = false;
		var $biografia = "";
		var $posts;
		var $puntos;
		var $ubicacion;
		var $tags;
		var $correo;
		var $conexiones = 0;
		var $puntosDisponibles = 0;
		function __construct($id=0){
	        $this->id = $id;
	        $r_ses = mysql_query("SELECT alias, nombre, usuario_activo, biografia, posts, puntos, ubicacion, tags, correo, conexiones FROM usuario WHERE usuario_id='$this->id'");
	        if (mysql_num_rows($r_ses)==1){
	            $rs_ses = mysql_fetch_array($r_ses);
	            $this->alias = trim($rs_ses[0]);
	            $this->nombre = trim($rs_ses[1]);
	            $this->activo = ($rs_ses[2]==1) ? true : false;
	            $this->conectado = true;
	            $this->biografia = $rs_ses[3];
	            $this->posts = $rs_ses[4];
	            $this->puntos = $rs_ses[5];
	            $this->ubicacion = $rs_ses[6];
	            $this->tags = $rs_ses[7];
	            $this->correo = $rs_ses[8];
	            $this->conexiones = $rs_ses[9];
	            //
	            
	            $fecha=date("Y-m-d H:i:s");        		
        		$r_lim=mysql_query("SELECT usuario_id FROM publicacion_voto WHERE DATE(fecha)=DATE('$fecha') AND usuario_id='".$this->id."'");
        		$this->puntosDisponibles = 10 - mysql_num_rows($r_lim);
        		if ($this->puntosDisponibles<0)
        			$this->puntosDisponibles=0;
        			
        		//$this->puntosDisponibles = "Infinito";

	        }
		}
	}
	class cAPP{
		var $servidorBase;
		var $servidor;
		function __construct(){
			$this->configuraServidor();
		}
		private function configuraServidor(){
			if ($_SERVER['HTTP_HOST']=="localhost"){
				$this->servidorBase = "http://localhost/plaxedco/";
			}        		
        	else{
        		$this->servidorBase = "http://www.plaxed.com/";
        	}
        	$this->servidor = $_SERVER['HTTP_HOST'];
		}

		function esLocal(){
			if ($_SERVER['HTTP_HOST']=="localhost"){
				return true;
			}
			else{
				return false;
			}
		}
	}
	class NotificacionCorreo extends PHPMailer{
		var $fecha = "";
		var $pie = "";
		var $pieAlt = "";
		function __construct(){
			$this->Mailer = "smtp";
			$this->IsSMTP(); 
			$this->CharSet = "UTF-8";
			$this->Port = 465; // 26 o 465
			// si el SMTP necesita autenticación
			$this->SMTPAuth = true;
			//$this->SMTPSecure = "ssl"; //quito esto?
			$this->Username = "plaxedco";
			$this->Password = "501878Plaxed+";
			$this->Host = "ssl://box696.bluehost.com";
			$this->fecha=date("d/m/Y")." a las ".date("h:i:sa");
			$this->pie.= "<br><br><br><a href=\"http://www.plaxed.com/\">Plaxed.com</a> - Todos los Derechos Reservados ".date("Y");
      		$this->pie.= "<br><br><h6>Enviado: ".$this->fecha."</h6>";
      		$this->pieAlt.= "\n\n\n\nhttp://www.plaxed.com/ - Todos los Derechos Reservados ".date("Y");
      		$this->pieAlt.= "\n\n<h6>Enviado: ".$this->fecha."</h6>";
		}
		function Asunto($txt){
			$this->Subject = $txt;	
		}
		function CorreoOrigen($txt){
			$this->From = $txt;	
		}
		function NombreOrigen($txt){
			$this->FromName = $txt;
		}
		function Cuerpo($html){
			$this->MsgHTML($html.$this->pie);
		}
		function CuerpoAlternativo($txt){
			$this->AltBody=$txt.$this->pieAlt;
		}
		function AgregarDestino($correo, $nombre=""){
			if (empty($nombre)){
				$this->AddAddress($correo);
			}
			else{
				$this->AddAddress($correo, $nombre);
			}
			
		}
		function Enviar(){
			return $this->Send();
		}
	}
?>

<?php
	if (!isset($variableSeguridad)){
        echo "error! ruta inválida...";
        exit();
    }
    include("php/Rest.inc.php");
	//$apiPeticion = $parametros;
	class API extends REST {
		private $db = NULL;
		function __construct($db){
			parent::__construct();
			$this->db = $db;
		}

		public function processApi(){
			$url = $_REQUEST['url'];
			$url = explode("/", $url);
			array_shift($url);
			$url = implode("/", $url);
			$func = strtolower(trim(str_replace("/","",$url)));
			if (empty($func)){

				$this->response($this->json($salida), 200);
			}
			//$func = strtolower(trim(str_replace("/","",$_REQUEST['url'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}

		private function login(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$usuario = $this->_request['usuario'];		
			$clave = $this->_request['clave'];
			
			// Input validations
			if(!empty($usuario) and !empty($clave)){
				$clave = sha1(md5($clave));
				$sql = mysql_query("SELECT clave, alias, nombre, correo, fecha_registro,
											puntos, posts, conexiones, biografia, ubicacion, 
											tags, fecha_nacimiento FROM usuario WHERE alias='$usuario'");
				if(mysql_num_rows($sql) > 0){
					$result = mysql_fetch_array($sql);
					if ($result[0] == $clave){
						// If success everythig is good send header as "OK" and user details
						$salida = array("respuesta"=>"ok", 
										"mensaje"=>"El login ha sido satisfactorio.",
										"usuario"=>array("alias"=>"$result[1]",
															"nombre"=>"$result[2]",
															"correo"=>"$result[3]",
															"fecha_registro"=>"$result[4]",
															"puntos"=>"$result[5]",
															"posts"=>"$result[6]",
															"conexiones"=>"$result[7]",
															"biografia"=>"$result[8]",
															"ubicacion"=>"$result[9]",
															"tags"=>"$result[10]",
															"fecha_nacimiento"=>"$result[11]"
															));	
						$this->response($this->json($salida), 200);
					}
					else{
						$salida = array("respuesta"=>"error", "mensaje"=>"Clave incorrecta.");
						$this->response($this->json($salida), 200);	// If no records "No Content" status
					}
				}
				else{
					$salida = array("respuesta"=>"error", "mensaje"=>"El usuario no existe.");
					$this->response($this->json($salida), 200);	// If no records "No Content" status
				}
			}
			else{
				// If invalid inputs "Bad Request" status message and reason
				$this->response('', 400);	
			}
		}
		private function info(){
			$zonah = date('Z');
			$zonah = ($zonah/60)/60;
			// Usuarios
			$r=mysql_query("SELECT count(*) FROM usuario");
			$rs=mysql_fetch_array($r);
			$cantidadUsuarios = (int)$rs[0];
			// Posts
			$r=mysql_query("SELECT count(*) FROM publicacion");
			$rs=mysql_fetch_array($r);
			$cantidadPosts = (int)$rs[0];
			$salida = array("api"=>array("version"=>"1.0",
										"fase"=>"Alfa"
										),
							"servidor"=>array("nombre"=>"Plaxed",
												"zona_horaria"=>$zonah,
												"max_upload_tam"=>PLAXED_UPLOAD_SIZE_MAX,
												"usuarios_registrados"=>$cantidadUsuarios,
												"publicaciones"=>$cantidadPosts,
												"registro_habilitado"=>PLAXED_REGISTRO_HABILITADO,
												"correo_soporte"=>PLAXED_SOPORTE_CORREO,
												"asesor"=>"@roeltz"
										)
							);
			$this->response($this->json($salida), 200);
		}
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}
	$API = new API($BDD);
	$API->processApi();
?>
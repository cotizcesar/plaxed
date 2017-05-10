var publicarMensaje = function(enRespuesta){
    enRespuesta = enRespuesta || '';
    if (enRespuesta){
        idRespuesta = enRespuesta;
        var resUsuario=$('#p-username-'+enRespuesta).html();
        resUsuario=resUsuario.substr(2);
        $('#text_mensaje').val(resUsuario + ' ');
    }
    else{
        idRespuesta = 0;
        $('#text_mensaje').val('');
    }
	$('#div-absoluto-publicar').css('display','block');
	$('#div-publicar').css('display','block');
	$('#text_mensaje').focus();
    if (enRespuesta){
        $('#text_mensaje').setCursorPosition(resUsuario.length+1);
    }
};

var ocultarDivAbsoluto = function(){
	$('#div-absoluto-publicar').css('display','none');
	$('#div-publicar').css('display','none');
	$('#divnotificaciones').css('display','none');
};

var abrirNotificaciones = function(){
    $('#div-absoluto-publicar').css('display','block');
    $('#divnotificaciones').css('display','block');
};

var enviarPublicacion = function(){
    var enRespuesta = idRespuesta;
	var txt = $('#text_mensaje').val();
	var restante=200 - txt.length;
	if (restante>=0 && restante<200){
		var estadoEnvio = $('#respuesta_envio').val();
		if (estadoEnvio == 'reposo'){
            $('#btn_enviar').val('Plaxeando...');
			$('#btn_enviar').attr('disabled','disabled');
			$('#text_mensaje').attr('disabled','disabled');			
			if (!ConexionOcupada){
				ConexionOcupada = true;
				$('#respuesta_envio').val('enviando');
				txt = encodeURIComponent(txt);
				txt=txt.replace(/\+/g,'%2B');
				ajaxEnviar('respuesta_envio', 'index.php?enviarPost', 'txt='+txt+'&en_respuesta='+enRespuesta+'&modulo='+moduloActual);   
			}
			else{
				setTimeout("enviarPublicacion()", 3000);
			}
		}
	}
	else{
		$('#text_mensaje').focus();
	}
};

var cuentaCaracteres = function(texto){
    var restante = 200 - texto.length;
    var mensaje = '';
    if (restante >= 0){
        mensaje = 'Te quedan ' + restante + ' caracteres';
        $('#span_cuenta').css('color','#555');
    }
    else{
        mensaje = 'Te excediste en ' + restante + ' caracteres';
        $('#span_cuenta').css('color','#f00');
    }
    $('#span_cuenta').html(mensaje);
};

var refrescarTimeLine = function(){
    var estadoEnvio = $('#respuesta_envio').val();
    if (estadoEnvio == 'reposo'){
        var fragmento = document.createDocumentFragment();
        $.each(mensajesPendientes, function(indice, elemento){
            var nuevoDiv = document.createElement("div");
            $(nuevoDiv).attr('id',elemento.div_id);
            $(nuevoDiv).addClass('msj');
            $(nuevoDiv).html(elemento.contenido);
            if (moduloActual == 'c'){
                //$(nuevoDiv).app($('div.cl div:first'));
                $('#cl').append($(nuevoDiv));
            }
            else{
                $(nuevoDiv).insertBefore($('div.cl div:first'));
            }
        });
        mensajesPendientes = Array();
        document.title = tituloPagina;
        $('#div_aviso_mensajes').html('');
        $('#div_aviso_mensajes').css('display','none');
        $('#respuesta_refrescar').val('reposo');
    }
};

var chequearActualizacion = function(automatico){
    //  && moduloActual!='c'
	if (moduloActual!='p'){
		var estadoEnvio = $('#respuesta_envio').val();
		if (!automatico){
			automatico = false;
		}
        if (moduloActual=='u'){
            solicitarPublicaciones(automatico,'index.php?recuperarPosts','ultimoIdPublicacion='+ultimoIdPublicacion+'&modulo='+moduloActual+'&usuarioActual='+usuarioActual);
        }
        else if (moduloActual=='c'){
            solicitarPublicaciones(automatico,'index.php?recuperarPosts','PostIdBottom='+PostIdBottom+'&modulo='+moduloActual+'&conversacionActual='+conversacionActual);
        }
        else if (moduloActual=='tema'){
            solicitarPublicaciones(automatico,'index.php?recuperarPosts','ultimoIdPublicacion='+ultimoIdPublicacion+'&temaActual='+temaActual+'&modulo='+moduloActual);    
        }
        else{
            solicitarPublicaciones(automatico,'index.php?recuperarPosts','ultimoIdPublicacion='+ultimoIdPublicacion+'&modulo='+moduloActual);    
        }		
	}
};

var manejarActualizacion = function(){
    estadoEnvio = $('#respuesta_envio').val();
    
    timerContador++;
    if (moduloActual=='not'){

    }
    else{
        estadoRefrescar = $('#respuesta_refrescar').val();
        if (estadoRefrescar == 'pendiente'){
            //$('#div_aviso_mensajes').html('<a href="javascript:refrescarTimeLine();"> + '+mensajesPendientes.length+' Mensajes</a>').css('display','block');
        }
        if (!ConexionOcupada){
            if (estadoEnvio != 'reposo'){
                if (estadoEnvio == 'enviado'){
                    ocultarDivAbsoluto();
                    $('#text_mensaje').val('');
                    $('#text_mensaje').removeAttr('disabled');
                    $('#btn_enviar').val('Plaxear!');
                    $('#btn_enviar').removeAttr('disabled');
                    cuentaCaracteres($('#text_mensaje').val());
                    ConexionOcupada=true;
                    chequearActualizacion(true);
                    window.clearInterval(timerActualizacion);
                    timerContador=0;
                    timerActualizacion = setInterval(manejarActualizacion, 3000);
                }
                if (estadoEnvio == 'no_enviado'){
                    $('#btn_enviar').val('Plaxear!');
                    $('#btn_enviar').removeAttr('disabled');
                    $('#text_mensaje').removeAttr('disabled');
                    $('#text_mensaje').focus();
                }
                if (estadoEnvio!='enviando' && estadoEnvio!='reposo'){
                    $('#respuesta_envio').val('reposo');
                }
            }
            else{
                if (timerContador == 5){
                    chequearActualizacion(false);
                }
            }
        }
    }       

    if (timerContador == 5){
        timerContador = 0;
    }
};

var puntuar = function(id, signo){
	if (!Puntuando){
		Puntuando = true;
		if (!ConexionOcupada){
			ConexionOcupada=true;
			ajaxPuntuar(id, 'index.php?enviarPuntuacion', 'publicacion_id='+ id + '&signo='+signo);
		}
		else{
			setTimeout('puntuar('+id+','+signo+')',3000);
		}

	}
	else{
		alert('Debe esperar hasta que se envíe la otra puntuación.');
	}
};

var borrar = function(id){
	if (!ConexionOcupada){
		ConexionOcupada=true;
		ajaxEliminarPost('div_msj_'+id, 'index.php?eliminarPost', 'publicacion_id='+id);
	}
	else{
		setTimeout('borrar('+id+')',3000);
	}
};

var preguntaBorrar = function(id){
	if (!Borrando){
		var resp = confirm('Desea eliminar esta publicación?');
		if (resp==true){
			Borrando = true;
			borrar(id);
		}
	}
	else{
		alert('Debe esperar hasta que se elimine el otro elemento.');
	}
};

var masPlaxs = function(){
    if (moduloActual=='' || moduloActual=='index.php'){
        $('#lmore').html('<img src="images/template/loader_posts.gif">');
        if (!ConexionOcupada){
            ConexionOcupada = true;
            ajaxMasPlaxs();
        }
        else{
            setTimeout('masPlaxs()',3000);
        }
    }
    else{
        alert('Aun no disponible en este modulo...');
    }
}
$(document).ready(function(){
    $(':input[placeholder]').placeholder();
    estadoEnvio=$('#respuesta_envio').val();
    $('#text_mensaje').on('keypress keydown keyup',function(){
        cuentaCaracteres($('#text_mensaje').val());
    })
    $('#div-absoluto-publicar').on({
        'keydown': function(e){
            if (e.which == 27){
                if (estadoEnvio != 'enviando')
                    ocultarDivAbsoluto();
            }
        },
        'mousedown': function(e){
            if(e.which == 1){
                if (e.target.id == 'div-absoluto-publicar' && estadoEnvio!='enviando')
                    ocultarDivAbsoluto();
            }
        }
    })
});

var masNotificaciones = function(){
    alert('aun no disponible -.-');
};
var jsPlaxed = (function(){
    var timerContador = 0;
    var timerActualizacion = "";
    var ultimoIdPublicacion = 0;
    var PostIdBottom = 0;
    var mensajesPendientes = Array();
    var moduloActual="";
    var ConexionOcupada = false;
    var Puntuando = false;
    var Borrando = false;
    var tituloPagina = document.title;
    var idRespuesta = 0;
    var usuarioActual = "";
    var conversacionActual = "";
    var temaActual = "";
    var busquedaActual = "";
    var archivoAdjunto = 0;
    var txtPost = '';
    var adjuntoOriginal = '';
    var adjuntoMiniatura = '';
    var estadoEnvio = 'reposo';
    var estadoRefrescar = 'reposo';
    var Conectando = false;
    var Preguntando = false;
    var previewFoto = function(){
        var rutaPerfil = $(this).attr('src');
        if (rutaPerfil!='./images/users/user-48x48.png'){
            var Alto = $(this).attr('alto');
            var Ancho = $(this).attr('ancho');
            var usr = $(this).attr('usuario');
            nuevoAlto = Alto;
            nuevoAncho = Ancho;
            if (Alto>160 || Ancho>160){
                //rutaPerfil = rutaPerfil.replace("48x48","perfil");
                rutaPerfil = './images/users/user-'+usr+'-original.png';
                if (Alto>Ancho){
                    if (nuevoAlto>480)
                        nuevoAlto = 480;
                    nuevoAlto = (nuevoAlto*Ancho)/Alto;
                }
                else{
                    if (nuevoAncho>640)
                        nuevoAncho = 640;
                    nuevoAlto = (nuevoAncho*Alto)/Ancho;
                }
            }else{
                rutaPerfil = rutaPerfil.replace("48x48","perfil");
            }
        
            $('#div-preview-foto-perfil').css({
                'width': nuevoAncho+'px',
                'height': nuevoAlto+'px',
                'background-image':'url(' + rutaPerfil + ')',
                'display': 'block',
                'left': '50%',
                'top': '30px',
                'margin-top': '0',
                'margin-left': '-'+(nuevoAncho/2)+'px'
            });
            $('#div-capa-absoluta').css('display','block');
        }                    
    }
    var setProgressBar = function(estado){
        if (estado == 'mostrar'){
            $('.div-progress').css('width', '0');
            $('.div-progress-bar').css('visibility', 'visible');
        }
        else if (estado == 'ocultar'){
            $('.div-progress-bar').css('visibility', 'hidden');
        }
        else{
            var valor = (parseFloat(estado)*80)/100;
            $('.div-progress').css('width', valor);
        }
    }
    var lnkBorrarPost = function(){
        var idBorrar = $(this).attr('msjId');
        if (!Borrando){
            vPregunta('¿Desea eliminar esta publicación?', ['Eliminar:eliminar','Cancelar:cancelar'], function(respuesta){
                if (respuesta=='eliminar'){
                    borrar(idBorrar);
                }
            });                
        }
        else{
            vAlerta('Debe esperar a que se elimine la otra publicación.')
        }
    }
    var lnkConex = function(){
        var alias = $(this).attr('useralias');
        var nombre = $(this).attr('usernombre');
        var id = $(this).attr('userid');
        vPregunta('¿Desea crear la conexión con el usuario @'+alias+' ('+nombre+')?',['Conectar:conectar','Rechazar:rechazar','Cancelar:cancelar'], function(respuesta){
            if (respuesta!="cancelar"){
                $('a.lnk-conex[userid='+id+']').unbind();
                $('a.lnk-conex[userid='+id+']').remove();
            }
            if (respuesta=="conectar"){
                ajaxAceptarConexion(id);
            }
            else if (respuesta=="rechazar"){
                ajaxRechazarConexion(id);
            }
        });                   
    }
    var lnkDescartarNotificaciones = function(){
        vPregunta('¿Desea descartar las notificaciones y marcarlas como leídas?', ['Descartar:descartar','Cancelar:cancelar'], function(respuesta){
            if (respuesta == 'descartar'){
                descartarNotificaciones();
            }
        });
    }
    var iniciarBusqueda = function(termino){
        termino=$.trim(termino);
        if (termino.length<2) return;
        if (/^#\w/.test(termino)){
            //alert('tema');
            var tema=termino.replace('#','');
            document.location.href='./tema/'+tema;
        }
        else if(/^@\w/.test(termino)){
            vAlerta('Solo falta implementar la búsqueda de @usuarios');
        }
        else{
            document.location.href='./buscar/'+termino;
        }
    }
    var vPregunta = function(msj, botones, callback){
        //if (Preguntando)
        //    return false;
        //Preguntando = true;
        $('#boxqst .bxbtns a').remove();
        botones = botones || "";
        callback = callback || "";
        if (botones!=""){
            var btnBar = document.getElementById('bxbtns');
            for (i=0; i<botones.length; i++){
                var infoBtn = botones[i].split(":");
                var btn = document.createElement('a');
                btn.setAttribute('href', 'javascript:;');
                btn.setAttribute('id', 'btn-res-'+infoBtn[1]);
                btn.setAttribute('class', 'act');
                btn.setAttribute('respuesta', infoBtn[1]);
                var lbl = document.createTextNode(infoBtn[0]);
                btn.appendChild(lbl);
                btnBar.appendChild(btn);
                $('#btn-res-'+infoBtn[1]).click(function(){
                    $('#boxqst').css('display','none');
                    $('#boxqst .bxbtns').css('display','none');
                    $('#boxqst #boxalr-ttl').html('');
                    $('#boxqst #boxalr-txt').html('');
                    //Preguntando = false;
                    callback($(this).attr('respuesta'));
                });
            }
            $('#boxqst .bxbtns').css('display','block');
            $('#boxqst #boxalr-txt').html(msj);
            $('#boxqst #boxalr-ttl').html('Confirmar');
            $('#boxqst').css('display','block');
        }
    }
    var vAlerta = function(msj, titulo){        
        titulo = titulo || 'Alerta';
        $('#boxalr .bxbtns').css('display','none');    
        $('#boxalr #boxalr-txt').html(msj);
        $('#boxalr #boxalr-ttl').html(titulo);
        $('#boxalr').css('display','block');
        setTimeout(vAlertaOcultar, 4000);
    };
    
    var vAlertaOcultar = function(){
        $('#boxalr').hide("slow");
        $('#boxalr #boxalr-txt').html('');
        $('#boxalr #boxalr-ttl').html('');
    }
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
            vAlerta('Debe esperar hasta que se envíe la otra puntuación.');
        }
    };
    var publicarMensaje = function(enRespuesta){
        enRespuesta = enRespuesta || '';
        if (enRespuesta){
            idRespuesta = enRespuesta;
            var resUsuario=$('#div_msj_'+enRespuesta).attr('menciones');
            if (resUsuario!='')
                resUsuario = resUsuario + ' ';
            $('#text_mensaje').val(resUsuario);
        }
        else{
            idRespuesta = 0;
            $('#text_mensaje').val('');
        }
        setDivPublicar();
        if (enRespuesta){
            $('#text_mensaje').setCursorPosition(resUsuario.length+1);
        }
    };
    var setDivPublicar = function(parametro){
        parametro = parametro || '';
        if (parametro=='cerrar'){
            $('#div-capa-absoluta').css('display','none');
            $('#div-publicar').css('display','none');
        }
        else if (parametro=='bloquear'){
            $('#btn-enviar').val('Plaxeando...');
            $('#btn-enviar').attr('disabled','disabled');
            $('#text_mensaje').attr('disabled','disabled');  
            $('#link-quitar-foto').css('visibility','hidden');
        }
        else if (parametro=='desbloquear'){
            $('#btn-enviar').val('Plaxear!');
            $('#btn-enviar').removeAttr('disabled');
            $('#text_mensaje').removeAttr('disabled');
            $('#link-quitar-foto').css('visibility','visible   ');
            $('#text_mensaje').focus();            
        }
        else if (parametro=='ocultar'){
            $('#btn-enviar').val('Plaxear!');
            $('#btn-enviar').removeAttr('disabled');
            $('#text_mensaje').removeAttr('disabled');
            $('#link-quitar-foto').css('visibility', 'hidden');
        }
        else{
            $('#link-quitar-foto').css('visibility','hidden');
            $('#file-foto').css('display','block');
            limpiarHTML('file-foto');
            $('#div-capa-absoluta').css('display','block');
            $('#div-publicar').css('display','block');
            $('#text_mensaje').focus();
        }            
    }

    var masNotificaciones = function(){
        vAlerta('aun no disponible -.-');
    };
    var masPlaxs = function(){
        if (moduloActual=='' || moduloActual=='u' || moduloActual=='buscar' || moduloActual=='etiquetas' || moduloActual=='tema'){ //  || moduloActual=='index.php'
            $('#lmore').html('<img src="images/template/loader_posts.gif">');
            if (!ConexionOcupada){
                ConexionOcupada = true;
                ajaxMasPlaxs();
            }
            else{
                setTimeout(masPlaxs(),3000);
            }
        }
        else{
            vAlerta('Aun no disponible en este modulo...');
        }
    };
    var ocultarDivAbsoluto = function(){
        $('#div-capa-absoluta').css('display','none');
        $('#div-publicar').css('display','none');
        $('#div-eliminar').css('display','none');
        $('#div-preview-foto-perfil').css('display','none');
        $('#div-notificaciones').css('display','none');
        $('#div-pregunta').css('display','none');
        $('#div-conexion').css('display','none');
    };
    var abrirNotificaciones = function(){
        $('#div-capa-absoluta').css('display','block');
        $('#div-notificaciones').css('display','block');
    }; 
    var enviarPublicacion = function(){
        var enRespuesta = idRespuesta;
        var txt = $('#text_mensaje').val();
        txt = $.trim(txt);
        var restante=200 - txt.length;
        if (restante>=0 && restante<200){
            setDivPublicar('bloquear');
            if (estadoEnvio == 'reposo'){                
                if (!ConexionOcupada){
                    ConexionOcupada = true;
                    estadoEnvio = 'enviando';
                    txtPost = txt;                    
                    if ($('#file-foto').val()!=''){
                        $('#form-upload').submit();
                    }
                    else{
                        txt = encodeURIComponent(txt);
                        txt=txt.replace(/\+/g,'%2B');
                        ajaxEnviar('index.php?enviarPost', 'txt='+txt+'&en_respuesta='+idRespuesta+'&modulo='+moduloActual);
                    }
                    
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
    var chequearActualizacion = function(automatico){
        if (moduloActual!='p'){
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
            else if (moduloActual=='buscar'){
                solicitarPublicaciones(automatico,'index.php?recuperarPosts','ultimoIdPublicacion='+ultimoIdPublicacion+'&busquedaActual='+busquedaActual+'&modulo='+moduloActual);    
            }
            else{
                solicitarPublicaciones(automatico,'index.php?recuperarPosts','ultimoIdPublicacion='+ultimoIdPublicacion+'&modulo='+moduloActual);    
            }       
        }
    };
    var refrescarTimeLine = function(){
        if (estadoEnvio == 'reposo'){
            $.each(mensajesPendientes, function(indice, elemento){
                if (moduloActual == 'c'){
                    $('#cl').append(elemento);
                }
                else{
                    $(elemento).insertBefore($('div.cl div:first'));
                }
            });
            $('.lnkVoto').unbind('click');
            $('.lnkVoto').click(function(){                
                puntuar($(this).attr('msjId'),$(this).attr('msjVoto'));
            });
            $('.lnkBorrar').unbind('click');
            $('.lnkBorrar').click(lnkBorrarPost);

            $('.lnkResponder').unbind('click');
            $('.lnkResponder').click(function(){
                publicarMensaje($(this).attr('msjId'));
            });
            $('.avatar48 img').unbind('dblclick');
            $('.avatar48 img').dblclick(previewFoto);
            mensajesPendientes = Array();
            document.title = tituloPagina;
            $('#div_aviso_mensajes').html('');
            $('#div_aviso_mensajes').css('display','none');
            estadoRefrescar = 'reposo';
        }
    };
    var manejarActualizacion = function(){
        timerContador++;
        if (moduloActual=='not'){

        }
        else{
            if (!ConexionOcupada){
                if (estadoEnvio != 'reposo'){
                    if (estadoEnvio == 'ok'){
                        cuentaCaracteres($('#text_mensaje').val());
                        ConexionOcupada=true;
                        chequearActualizacion(true);
                        window.clearInterval(timerActualizacion);
                        timerContador=0;
                        timerActualizacion = setInterval(manejarActualizacion, 3000);
                    }
                    if (estadoEnvio == 'error'){
                        $('#btn-enviar').val('Plaxear!');
                        $('#btn-enviar').removeAttr('disabled');
                        $('#text_mensaje').removeAttr('disabled');
                        $('#text_mensaje').focus();
                    }
                    if (estadoEnvio!='enviando' && estadoEnvio!='reposo'){
                        estadoEnvio = 'reposo';
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
    var borrar = function(id){
        if (!ConexionOcupada){
            ConexionOcupada=true;
            ajaxEliminarPost('div_msj_'+id, 'index.php?eliminarPost', 'publicacion_id='+id);
        }
        else{
            setTimeout(function(){borrar(id);},3000);
        }
    };
    var renderizarPost = function(post){
        //console.log(post)
        // Div principal
        var divMsj = document.createElement('div');
        divMsj.setAttribute('class', 'msj');
        divMsj.setAttribute('id', 'div_msj_' + post.publicacion.id);
        divMsj.setAttribute('menciones', post.publicacion.menciones);
            var aAncla = document.createElement('a');
            aAncla.setAttribute('class','ancla');
            aAncla.setAttribute('name','post-' + post.publicacion.id);
        divMsj.appendChild(aAncla);
            // Div avatar
            var divAvatar = document.createElement('div');
            divAvatar.setAttribute('class', 'avatar48');
                var imgAvatar = document.createElement('img');
                imgAvatar.setAttribute('src', './images/users/' + post.autor.avatar48);
                imgAvatar.setAttribute('usuario', post.autor.id);
                imgAvatar.setAttribute('alto', post.autor.avatarAlto);
                imgAvatar.setAttribute('ancho', post.autor.avatarAncho);
            divAvatar.appendChild(imgAvatar);
        divMsj.appendChild(divAvatar);
            // Div Top Msj
            var divTopMsj = document.createElement('div');
            divTopMsj.setAttribute('class', 'topmsj');
                var aUsuario = document.createElement('a');
                aUsuario.setAttribute('href', './u/' + post.autor.alias);
                aUsuario.appendChild(document.createTextNode(post.autor.nombre));
            divTopMsj.appendChild(aUsuario);
                var pUsuario = document.createElement('p');
                pUsuario.setAttribute('id', 'p-username-' + post.publicacion.id);
                pUsuario.setAttribute('class', 'usern');
                pUsuario.appendChild(document.createTextNode('- @'+post.autor.alias));
            divTopMsj.appendChild(pUsuario);
        divMsj.appendChild(divTopMsj);
            // Div Texto
            var divTexto = document.createElement('div');
            divTexto.setAttribute('id', 'div-mensaje-' + post.publicacion.id);
            divTexto.setAttribute('class', 'text');
            var contenido = post.publicacion.contenido;
            if (post.publicacion.youtube){
                contenido+= post.publicacion.youtube;
            }
            divTexto.innerHTML = contenido;
        divMsj.appendChild(divTexto);
            // Div mnav
            var divMnav = document.createElement('div');
            divMnav.setAttribute('id', 'mnav_' + post.publicacion.id);
            divMnav.setAttribute('class', 'mnav');
                if (!post.publicacion.mio){
                    var aRepetir = document.createElement('a');
                    aRepetir.setAttribute('class', 'rpt lnkRepetir sprite');
                    aRepetir.setAttribute('title', 'Repetir');
                    aRepetir.setAttribute('href', 'javascript:;');
                    aRepetir.setAttribute('msjid', post.publicacion.id);
                    divMnav.appendChild(aRepetir);
                }else{
                    var aBorrar = document.createElement('a');
                    aBorrar.setAttribute('class', 'del lnkBorrar sprite');
                    aBorrar.setAttribute('title', 'Borrar');
                    aBorrar.setAttribute('href', 'javascript:;');
                    aBorrar.setAttribute('msjid', post.publicacion.id);
                    divMnav.appendChild(aBorrar);
                }
                var aResponder = document.createElement('a');
                aResponder.setAttribute('class', 'rsp lnkResponder sprite');
                aResponder.setAttribute('title', 'Responder');
                aResponder.setAttribute('href', 'javascript:;');
                aResponder.setAttribute('msjid', post.publicacion.id);
            divMnav.appendChild(aResponder);
                if (!post.publicacion.mio){
                    var aVotoMenos = document.createElement('a');
                    aVotoMenos.setAttribute('class', 'minus lnkVoto sprite');
                    aVotoMenos.setAttribute('title', 'Negativo');
                    aVotoMenos.setAttribute('msjvoto', 'n');
                    aVotoMenos.setAttribute('href', 'javascript:;');
                    aVotoMenos.setAttribute('id', 'minus_' + post.publicacion.id);
                    aVotoMenos.setAttribute('msjid', post.publicacion.id);
                    divMnav.appendChild(aVotoMenos);
                    var aVotoMas = document.createElement('a');
                    aVotoMas.setAttribute('class', 'plus lnkVoto sprite');
                    aVotoMas.setAttribute('title', 'Positivo');
                    aVotoMas.setAttribute('href', 'javascript:;');
                    aVotoMas.setAttribute('msjvoto', 'y');
                    aVotoMas.setAttribute('id', 'plus_' + post.publicacion.id);
                    aVotoMas.setAttribute('msjid', post.publicacion.id);
                    divMnav.appendChild(aVotoMas);
                }
        divMsj.appendChild(divMnav);
            // div MSjBottom
            var divMsjBottom = document.createElement('div');
            divMsjBottom.setAttribute('id', 'msj_bottom');
            divMsjBottom.setAttribute('class', 'msj_bottom');
                var divFecha = document.createElement('div');
                divFecha.setAttribute('id', 'msj_fecha');
                divFecha.setAttribute('class', 'msj_fecha');
                    var aFecha = document.createElement('a');
                    aFecha.setAttribute('href', './p/' + post.publicacion.id);
                    aFecha.setAttribute('title', post.publicacion.fechatt);
                    aFecha.appendChild(document.createTextNode(post.publicacion.fecha));
                divFecha.appendChild(aFecha);
            divMsjBottom.appendChild(divFecha);        
                var divConvers = document.createElement('div');
                divConvers.setAttribute('class', 'link_conversacion');
                    var aConvers = document.createElement('a');
                    aConvers.setAttribute('href', './c/' + post.publicacion.conversacion_id);
                    aConvers.appendChild(document.createTextNode('Conversación'));
                divConvers.appendChild(aConvers);
            divMsjBottom.appendChild(divConvers)
                var divPuntos = document.createElement('div');
                divPuntos.setAttribute('id', 'msj_puntos_' + post.publicacion.id);
                divPuntos.setAttribute('class', 'msj_puntos');
                    var pPuntos = document.createElement('p');
                        pPuntos.appendChild(document.createTextNode('Puntos: '))
                            var spanPuntos = document.createElement('span');
                            var pts = post.publicacion.puntos;
                            var cls = '';
                            if (pts>0){
                                cls = 'txt_verde';
                            }
                            else if (pts<0){
                                cls = 'txt_rojo';
                            }
                            else{
                                cls = 'txt_neutro';    
                            }
                            spanPuntos.setAttribute('class', cls);
                            spanPuntos.appendChild(document.createTextNode(pts))
                        pPuntos.appendChild(spanPuntos);
                divPuntos.appendChild(pPuntos);                
            divMsjBottom.appendChild(divPuntos)
                if (post.publicacion.adjunto){
                    var divImagen = document.createElement('div');
                    divImagen.setAttribute('class', 'link_imagen');
                        var aImagen = document.createElement('a');
                        aImagen.setAttribute('href', post.publicacion.adjunto.original);
                        aImagen.setAttribute('target', '_blank');
                        aImagen.appendChild(document.createTextNode('Ver Imagen'));
                    divImagen.appendChild(aImagen);
                    divMsjBottom.appendChild(divImagen)
                }
        divMsj.appendChild(divMsjBottom);
        return divMsj;
    }
    //////// AJAX //////////
    var ajaxEnviar = function(pagina,datos){
        $.ajax({
            url: pagina,
            type: 'POST',
            data: datos,
            success: function(data, textStatus, xhr){
                var pub = $.parseJSON(data);
                if (pub.respuesta=="error"){
                    vAlerta(pub.mensaje);
                }
                else{
                    setDivPublicar('ocultar');
                    ocultarDivAbsoluto();
                }
                estadoEnvio = pub.respuesta;
                ConexionOcupada=false;
                archivoAdjunto = 0;                
            },
            ajaxSend: function(){
                estadoEnvio = 'enviando';
            },
            error: function(xhr, textStatus, errorThrown){
                vAlerta('Ha ocurrido un error. Es posible que no se haya publicado.');
                estadoEnvio = 'error';
                ConexionOcupada=false;
                setDivPublicar('desbloquear');
            }
        });
    };
    var ajaxConectar = function(idusuario){
        $.ajax({
            url: 'index.php?solicitarConexion',
            type: 'POST',
            data: 'idusuario='+idusuario,
            success: function(data, textStatus, xhr){
                //console.log(data);
                var resp = $.parseJSON(data);
                if (resp.respuesta=='ok'){                    
                    if (resp.codigo==2){
                        $('#medeus').html('<a class="cancel" id="cancel" idusuario="'+idusuario+'" href="javascript:;" title="Cancelar solicitud">Cancelar</a>');
                        $('#cancel').click(function(){
                            ajaxCancelarConexion(usuarioActual);
                        });
                    }
                    else{
                        $('#medeus').html('<a class="descon" id="descon" idusuario="'+idusuario+'" href="javascript:;">Desconectar</a>')
                        $('#descon').click(function(){
                            ajaxDesconectar(idusuario);
                        });
                    }                        
                }
                Conectando = false;
                vAlerta(resp.mensaje);
            },
            error: function(xhr, textStatus, errorThrown){
                vAlerta('Ha ocurrido un error. Es posible que no se haya completado la operación.');
            }
        });
    };
    var ajaxDesconectar = function(idusuario){
        vPregunta('¿Desea eliminar la conexión con el usuario ' + $('.nodeus').html() + '?', ['Eliminar:eliminar','Cancelar:cancelar'], function(respuesta){
            if (respuesta=='eliminar'){
                $.ajax({
                    url: 'index.php?eliminarConexion',
                    type: 'POST',
                    data: 'idusuario='+idusuario,
                    success: function(data, textStatus, xhr){
                        var resp = $.parseJSON(data);
                        if (resp.respuesta=="ok"){
                            $('#medeus').html('<a class="conect" id="conect" idusuario="'+idusuario+'" href="javascript:;" title="Solicitar conexión.">Conectar</a>');
                            $('#conect').click(function(){
                                ajaxConectar(idusuario);
                            });
                        }
                        vAlerta(resp.mensaje);
                    },
                    error: function(xhr, textStatus, errorThrown){
                        vAlerta('Ha ocurrido un error. Es posible que no se haya completado la operación.');
                    }
                });
            }
        });
    };
    var ajaxRechazarConexion = function(idusuario){
        $.ajax({
            url: 'index.php?rechazarConexion',
            type: 'POST',
            data: 'idusuario='+idusuario,
            success: function(data, textStatus, xhr){
                var resp = $.parseJSON(data);
                if (resp.respuesta=="ok"){
                    $('#div-conexion').css('display','none');
                    $('a.lnk-conex[userid='+idusuario+']').unbind();
                }
                vAlerta(resp.mensaje);
            },
            error: function(xhr, textStatus, errorThrown){
                vAlerta('Ha ocurrido un error. Es posible que no se haya completado la operación.');
            }
        });
    };
    var ajaxCancelarConexion = function(idusuario){
        $.ajax({
            url: 'index.php?cancelarConexion',
            type: 'POST',
            data: 'idusuario='+idusuario,
            success: function(data, textStatus, xhr){
                var resp = $.parseJSON(data);
                if (resp.respuesta=='ok'){
                    $('#medeus').html('<a class="conect" id="conect" idusuario="'+idusuario+'" href="javascript:;" title="Solicitar conexión.">Conectar</a>');
                    $('#conect').click(function(){
                        ajaxConectar(idusuario);
                    });
                }
                vAlerta(resp.mensaje);
            },
            error: function(xhr, textStatus, errorThrown){
                vAlerta('Ha ocurrido un error. Es posible que no se haya completado la operación.');
            }
        });
    };
    var ajaxAceptarConexion = function(idusuario){
        $.ajax({
            url: 'index.php?aceptarConexion',
            type: 'POST',
            data: 'idusuario='+idusuario,
            success: function(data, textStatus, xhr){
                var resp = $.parseJSON(data);
                if (resp.respuesta=='ok'){
                    $('#div-conexion').css('display','none');
                    $('a.lnk-conex[userid='+idusuario+']').unbind();
                    $('a.lnk-conex[userid='+idusuario+']').remove();
                }
                vAlerta(resp.mensaje);
            },
            error: function(xhr, textStatus, errorThrown){
                vAlerta('Ha ocurrido un error. Es posible que no se haya completado la operación.');
            }
        });
    };
    var solicitarPublicaciones = function(actualizar,pagina,datos){
        $.ajax({
            url: pagina,
            type: 'POST',
            data: datos,
            success: function(data, textStatus, xhr){
                var respuesta = data;
                var objRespuesta = $.parseJSON(respuesta);
                var posts=objRespuesta.posts;
                var notificaciones = objRespuesta.notificaciones;
                var cMensajes = objRespuesta.usuario.c_mensajes;
                var cConexiones = objRespuesta.usuario.c_conexiones;
                var cPuntos = objRespuesta.usuario.c_puntos;
                var cPuntosDisponibles = objRespuesta.usuario.c_puntos_disponibles;
                var cNotificaciones = objRespuesta.usuario.c_notificaciones;
                var perfilActual = objRespuesta.perfil;
                var usuariosActivos = objRespuesta.activos;
                for (i=0; i<posts.length; i++){
                    if (!$('#div_msj_'+posts[i].publicacion.id).length){
                        mensajesPendientes.push(renderizarPost(posts[i]));
                        if (moduloActual=='c'){
                            PostIdBottom = posts[i].publicacion.id;
                        }
                        else{
                            ultimoIdPublicacion = posts[i].publicacion.id;    
                        }
                    }                
                }
                if (actualizar){
                    refrescarTimeLine();
                    ConexionOcupada=false;
                }
                else{
                    if (mensajesPendientes.length>0){
                        document.title = '(' + mensajesPendientes.length + ') ' + tituloPagina;
                        estadoRefrescar='pendiente';
                        $('#div_aviso_mensajes').html('<a href="javascript:;" id="lnkMensajesPendientes"> + '+mensajesPendientes.length+' Mensajes</a>').css('display','block');
                        $('#lnkMensajesPendientes').click(function(){
                            refrescarTimeLine();
                        });                        
                    }                   
                }
                //se borran los activos
                //usuariosActivos
                if ($('#ul-activos').length){
                    var ulActivos = document.getElementById('ul-activos');
                    while(ulActivos.hasChildNodes()){
                        ulActivos.removeChild(ulActivos.firstChild);  
                    }
                    //se cargan de nuevo
                    for (i=0; i<usuariosActivos.length; i++){
                        var liNuevo = document.createElement("li");
                        liNuevo.innerHTML = '<a title="'+usuariosActivos[i].alias+'" href="./u/' + usuariosActivos[i].alias + '"><img src="./images/users/'+ usuariosActivos[i].avatar+'"></a>';
                        ulActivos.appendChild(liNuevo);
                    }
                }

                //se borran las notificaciones
                var ulNotificaciones = document.getElementById('ul-notificaciones');
                while(ulNotificaciones.hasChildNodes()){
                    ulNotificaciones.removeChild(ulNotificaciones.firstChild);  
                }                
                //se cargan de nuevo
                for (i=0; i<notificaciones.length; i++){
                    var liNuevo = document.createElement("li");
                    var notContenido = notificaciones[i].contenido;
                    liNuevo.innerHTML = notContenido;
                    if (notificaciones[i].visto=="no"){
                        liNuevo.className="not-vista";
                    }
                    ulNotificaciones.appendChild(liNuevo);
                }

                //se vuelve a asociar el evento
                $('.lnk-conex').click(lnkConex);

                if (moduloActual=='u'){
                    $('.botpun span').html(perfilActual.puntos);
                    $('.botpos span').html(perfilActual.posts);
                    $('.botcon span').html(perfilActual.conexiones);
                }
                else{
                    $('#info-puntos').html(cPuntosDisponibles);
                    $('#span_c_mensajes').html('<a href="javascript:;">' + cMensajes + ' Publicaciones</a>');
                    $('#span_c_puntos').html('<a href="javascript:;">' + cPuntos + ' Puntos</a>');                    
                    $('#span_c_conexiones').html('<a href="javascript:;">' + cConexiones + ' Panas</a>');                    
                }
                $('.not').attr('title',cNotificaciones+' notificaciones no leídas');
                    
                if (cNotificaciones>99){
                    cNotificaciones = "99+";
                }
                $('#span_c_notificaciones').html(cNotificaciones);
            },
            error: function(xhr, textStatus, errorThrown){
                ConexionOcupada=false;
                console.log('Error al contactar el servidor');
            }
        });
    };
    var ajaxEliminarPost = function(id,pagina,datos){
        $.ajax({
            url: pagina,
            type: 'POST',
            data: datos,
            success: function(data, textStatus, xhr){
                var del = $.parseJSON(data);
                if (del.respuesta=='ok'){
                    $('#'+id).remove();
                }
                else{
                    vAlerta(del.mensaje);
                }
                Borrando = false;
                ConexionOcupada = false;
            },
            error: function(xhr, textStatus, errorThrown){
                vAlerta('Error! Es posible que no se haya eliminado el post');
                Borrando = false;
                ConexionOcupada = false;
            }
        });
    };
    var descartarNotificaciones = function(){
        $.ajax({
            url: 'index.php?descartarNotificaciones',
            type: 'POST',
            success: function(data, textStatus, xhr){
                var resp = $.parseJSON(data);
                if (resp.respuesta=='ok'){
                    $('div.not-nueva').each(function(index) {
                        $(this).removeClass('not-nueva');
                    });
                }
                vAlerta(resp.mensaje);
            },
            error: function(xhr, textStatus, errorThrown){
                vAlerta('Error! es posible que no se haya realizado el cambio.')
            }
        });
    }
    var ajaxPuntuar = function(id,pagina,datos){
        $.ajax({
            url: pagina,
            type: 'POST',
            data: datos,
            success: function(data, textStatus, xhr){
                var punto = $.parseJSON(data);             
                if (punto.respuesta=='error'){
                    vAlerta(punto.mensaje);
                }
                else{
                    var ptsPost = punto.puntos.post;
                    var ptsDisponibles = punto.puntos.disponibles;
                    var cls = 'txt_neutro';
                    $('#minus_'+id).remove();
                    $('#plus_'+id).remove();
                    if (ptsPost>0){
                        cls = 'txt_verde';
                        ptsPost = '+' + ptsPost;
                    }
                    if (ptsPost<0){
                        cls = 'txt_rojo';
                    }
                    $('#msj_puntos_'+id+' p span').attr('class', cls);
                    $('#msj_puntos_'+id+' p span').html(ptsPost);
                    $('#info-puntos').html(ptsDisponibles);
                }
                Puntuando = false;
                ConexionOcupada = false;
            },
            error: function(xhr, textStatus, errorThrown){
                Puntuando = false;
                ConexionOcupada = false;
                vAlerta('Error! Es posible que no se haya registrado la puntuación');
            }
        });
    };
    var ajaxMasPlaxs = function(){
        $.ajax({
            url: 'index.php?masPlaxs',
            type: 'POST',
            data: 'bottomId='+PostIdBottom+'&moduloActual='+moduloActual+'&usuarioActual='+usuarioActual+'&busquedaActual='+busquedaActual+'&temaActual='+temaActual,
            success: function(data, textStatus, xhr){
                var posts = $.parseJSON(data);
                if (posts.length>0){
                    for (i=0; i<posts.length; i++){
                        $('#cl').append(renderizarPost(posts[i]));
                        PostIdBottom = posts[i].publicacion.id;
                    }
                    $('#lmore').html('<a href="javascript:;" id="lnkMasPlaxs">Cargar más plaxs...</a>');
                    $('#lnkMasPlaxs').click(function(){
                        masPlaxs();
                    });
                    $('.lnkResponder').unbind('click');
                    $('.lnkResponder').click(function(){
                        publicarMensaje($(this).attr('msjId'));
                    });
                    $('.lnkVoto').unbind('click');
                    $('.lnkVoto').click(function(){                
                        puntuar($(this).attr('msjId'),$(this).attr('msjVoto'));
                    });
                    $('.lnkBorrar').unbind('click');
                    $('.lnkBorrar').click(lnkBorrarPost);

                    $('.avatar48 img').unbind('dblclick');
                    $('.avatar48 img').dblclick(previewFoto);
                }
                else{
                    $('#lmore').html('<a href="javascript:;">Ya no existen más posts...</a>');
                }
                ConexionOcupada = false;
            },
            error: function(xhr, textStatus, errorThrown){
                ConexionOcupada = false;
                vAlerta('Ha ocurrido un error!');
            }
        });
    };
    //////// AQUI LA PARTE PUBLICA ////////////
    return {
        setUltimoIdPublicacion: function(valor){
            ultimoIdPublicacion = valor;
        },
        setPostIdBottom: function(valor){
            PostIdBottom = valor;
        },
        setModuloActual: function(valor){
            moduloActual = valor;
        },
        setUsuarioActual: function(valor){
            usuarioActual = valor;
        },
        setBusquedaActual: function(valor){
            busquedaActual = valor;
        },
        setConversacionActual: function(valor){
            conversacionActual = valor;
        },
        setTemaActual: function(valor){
            temaActual = valor;
        },
        iniciar: function(){
            $(':input[placeholder]').placeholder();
            $('#text_mensaje').on('keypress keydown keyup',function(){
                cuentaCaracteres($('#text_mensaje').val());
            })
            $('#div-capa-absoluta').on({
                'keydown': function(e){
                    if (e.which == 27){
                        if (estadoEnvio != 'enviando')
                            ocultarDivAbsoluto();
                    }
                },
                'mousedown': function(e){
                    if(e.which == 1){
                        if (e.target.id == 'div-capa-absoluta' && estadoEnvio!='enviando')
                            ocultarDivAbsoluto();
                    }
                }
            })
            $('#btn-enviar').click(function(){
                enviarPublicacion();
            });
            $('#lnkAbrirNotificaciones').click(function(){
                abrirNotificaciones();
            });
            $('#lnkMasPlaxs').click(function(){
                masPlaxs();
            });
            $('#lnkMasNotificaciones').click(function(){
                masNotificaciones();
            });
            $('#lnkPublicarMensaje').click(function(){
                publicarMensaje();
            });
            $('.lnkVoto').click(function(){                
                puntuar($(this).attr('msjId'),$(this).attr('msjVoto'));
            });
           
            $('.lnkBorrar').click(lnkBorrarPost);
            $('.lnkResponder').click(function(){
                publicarMensaje($(this).attr('msjId'));
            });
            $('#a-marca-notif').click(lnkDescartarNotificaciones);
            
            $('.lnk-conex').click(lnkConex);
            timerActualizacion = setInterval(manejarActualizacion,3000);
            $('.avatar48 img').dblclick(previewFoto);
            
            $('#busqueda').keypress(function(event){
                if (event.which==13){
                    iniciarBusqueda(($(this).val()));
                }
            });
            $('#link-quitar-foto').click(function(){
                archivoAdjunto = 0;
                $('#file-foto').css('display','block');
                $('#link-quitar-foto').css('visibility','hidden');
                limpiarHTML('file-foto');
            });
            $('#conect').on('click', function(){
                if (!Conectando){
                    Conectando = true;
                    ajaxConectar($(this).attr('idusuario'));    
                }             
                else{
                    vAlerta('Por favor espere la respuesta.');
                }   
            });
            $('#cancel').click(function(){
                ajaxCancelarConexion(usuarioActual);
            });
            $('#descon').click(function(){
                ajaxDesconectar(usuarioActual);
            });
            $(document).bind('drop dragover', function (e) {
                e.preventDefault();
            });
            $('#file-foto').click(function(e){
                if (archivoAdjunto!=0)
                    return false;
            });
            $('#file-foto').on('change', function(){
                if ($(this).val()!=''){
                    $('#link-quitar-foto').css('visibility','visible');
                    $('#file-foto').css('display','none');
                }
                else{
                    $('#link-quitar-foto').css('visibility','hidden');
                    $('#file-foto').css('display','block');
                }
            });
            
            //menciones
            $('#text_mensaje').triggeredAutocomplete({
                hidden: '#hidden_inputbox',
                source: arregloAutocompletar,
                trigger: "@" 
            });

            //Upload
            $('#form-upload').ajaxForm({
                beforeSend: function() {
                    //
                },
                beforeSubmit: function(arr, $form, options) {
                    if (window.File && window.FileReader && window.FileList && window.Blob) {
                        var tam = arr[0].value.size;
                        var tipo = arr[0].value.type;
                        var error = false;
                        if (parseFloat(tam)>1048576 && !error){
                            error = true;
                            alert('El tamaño no debe exceder 1MB.');
                        }
                        if (!/^image\/(png|gif|jpeg)$/i.test(tipo) && !error){
                            error = true;
                            alert('Solo se permiten archivos PNG, JPG y GIF. El tuyo es ' + tipo + '. #Fin');
                        }
                        if (error){
                            estadoEnvio = 'error';                        
                            setDivPublicar('desbloquear');
                            ConexionOcupada=false;
                            return false;
                        }
                    }                                            
                    arr[arr.length]={name:"txt",type:"file",value:txtPost};
                    arr[arr.length]={name:"en_respuesta",type:"file",value:idRespuesta};
                    arr[arr.length]={name:"modulo",type:"file",value:moduloActual};
                    setProgressBar('mostrar');
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    //no pondre porcentaje, pondre un gif
                    setProgressBar(percentComplete);
                },
                complete: function(xhr) {
                    var resp = $.parseJSON(xhr.responseText);
                    if (resp.respuesta=="ok"){
                        estadoEnvio = 'reposo';
                        $('#file-foto').css('display','block');
                        limpiarHTML('file-foto');
                        setDivPublicar('ocultar');
                        ocultarDivAbsoluto();
                        estadoEnvio = resp.respuesta;
                        ConexionOcupada=false;
                        archivoAdjunto = 0;  
                    }else{
                        estadoEnvio = 'error';                        
                        setDivPublicar('desbloquear');
                        vAlerta(resp.mensaje);
                    }
                    setProgressBar('ocultar');
                    ConexionOcupada=false;                    
                }
            }); 
        }        
    }
})();
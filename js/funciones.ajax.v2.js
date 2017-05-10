var ajaxEnviar = function(id,pagina,datos){
    $.ajax({
        url: pagina,
        type: 'POST',
        data: datos,
        success: function(data, textStatus, xhr){
            $('#'+id).val(data);
            ConexionOcupada=false;
        },
        ajaxSend: function(){
            $('#'+id).val('enviando');
        },
        error: function(xhr, textStatus, errorThrown){
            alert(textStatus);
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
            var cPuntos = objRespuesta.usuario.c_puntos;
            var cNotificaciones = objRespuesta.usuario.c_notificaciones;

            for (i=0; i<posts.length; i++){
                if (!$('#'+posts[i].div_id).length){
                    mensajesPendientes.push(posts[i]);
                    if (moduloActual=='c'){
                        PostIdBottom = posts[i].publicacion_id;
                    }
                    else{
                        ultimoIdPublicacion = posts[i].publicacion_id;    
                    }
                }                
            }
            if (actualizar){
                refrescarTimeLine();
                ConexionOcupada=false;
            }
            else{
                if (posts.length>0){
                    document.title = '(' + mensajesPendientes.length + ') ' + tituloPagina;
                    document.getElementById('respuesta_refrescar').value='pendiente';
                    $('#div_aviso_mensajes').html('<a href="javascript:refrescarTimeLine();"> + '+mensajesPendientes.length+' Mensajes</a>').css('display','block');
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
                //liNuevo.appendChild(document.createTextNode(notContenido));
                ulNotificaciones.appendChild(liNuevo);

                $('#span_c_mensajes').html('<a href="javascript:;">' + cMensajes + ' Publicaciones</a>');
                $('#span_c_puntos').html('<a href="javascript:;">' + cPuntos + ' Puntos</a>');
                $('#span_c_notificaciones').html('<a href="javascript:;" onclick="abrirNotificaciones();">' + cNotificaciones + '</a>');
            }
        }
    });
};

var ajaxEliminarPost = function(id,pagina,datos){
    $.ajax({
        url: pagina,
        type: 'POST',
        data: datos,
        success: function(data, textStatus, xhr){
            var resp = data;
            if (resp=='ok'){
                $('#'+id).remove();
            }
            else{
                alert('Ocurrió un error! No se pudo eliminar')
            }
            Borrando = false;
            ConexionOcupada = false;
        }
    });
};

var ajaxPuntuar = function(id,pagina,datos){
    $.ajax({
        url: pagina,
        type: 'POST',
        data: datos,
        success: function(data, textStatus, xhr){
            var resp = data;
            if (resp!='error'){
                $('#minus_'+id).remove();
                $('#plus_'+id).remove();
                $('#msj_puntos_'+id).html(resp);
            }
            else{
                alert('Ocurrió un error :(');
            }
            Puntuando = false;
            ConexionOcupada = false;
        }
    });
};

var ajaxMasPlaxs = function(){
    $.ajax({
        url: 'index.php?masPlaxs',
        type: 'POST',
        data: 'bottomId='+PostIdBottom,
        success: function(data, textStatus, xhr){
            var posts = $.parseJSON(data);
            //alert(data);
            for (i=0; i<posts.length; i++){
                var nuevoDiv = document.createElement("div");
                //alert(posts[i].div_id)
                $(nuevoDiv).attr('id',posts[i].div_id);
                $(nuevoDiv).addClass('msj');
                $(nuevoDiv).html(posts[i].contenido);
                $('#cl').append($(nuevoDiv));
                PostIdBottom = posts[i].publicacion_id;
            }
            $('#lmore').html('<a href="javascript:;" onclick="masPlaxs();">Cargar mas plaxs...</a>');
            ConexionOcupada = false;
        },
        error: function(xhr, textStatus, errorThrown){
            ConexionOcupada = false;
        }
    });
};
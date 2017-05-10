$(document).ready(function(){

    $('.file-img').change(function(){
        var patronImg = /(png|jpg|jpeg)$/i;
        var archivo = $(this).val();
        if (archivo){
            if (patronImg.test(archivo)){
                $('.div-iframe-foto').css('display','block');
                $('#form-foto').submit();
            }
            else{
                $('.file-img').val('');
                alert('Solo se permiten imágenes en formato:\n\n- png\n- jpg\n- jpeg');
            }
        }
    });
    var cerrar_iFrame = function(){
        $('#iframe-foto').css('display', 'none');
    };
    $('#btn-submit').on('click', function(){
        var resp=supervalidacion(document.getElementById('form-perfil'));
        if (resp){
            var campos = $('#form-perfil').serialize();
            $('#form-perfil input,textarea').attr('disabled','disabled');
            $.post('./opciones/perfil/actualizar', campos, function(data){
                var resp = $.parseJSON(data);
                if (resp.respuesta=="ok"){
                     $('#form-perfil input[type=password]').val('');
                }
                $('#form-perfil input,textarea').removeAttr('disabled');
                alert(resp.mensaje);                
            });
        }
        return false;
    })
})
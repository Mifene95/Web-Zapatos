$(document).ready(function(){
    // Pintar estrellas
    $('.star').click(function(){
        var contenedor = $(this).parent();
        var puntos = $(this).data('v');
        var idZapato = $(this).closest('.contenedor-votos').data('id');

        contenedor.addClass('votado');
        $(this).css('color', 'gold').prevAll().css('color', 'gold');
        $(this).nextAll().css('color', '#ccc');

        
        $.post('../inc/guardar_voto.php', { id: idZapato, puntos: puntos });
    });

    //Boton comentar
    $('.btn-comentar').click(function(){
    let idZapato = $(this).closest('.contenedor-votos').data('id');
    let user = $(this).data('user-name');
    let msg = prompt("Escribe tu comentario:");
    
    if(msg) {
        let btn = $(this);
        let contenedorVotos = btn.closest('.contenedor-votos');

        $.post('../inc/guardar_voto.php', { id: idZapato, comentario: msg }, function(response){

            let nuevoComentario = `
                <div class="comentario-individual" style="border-bottom: 1px solid #eee; margin-bottom: 5px;">
                <button class="btn-borrar-comentario" data-id="${response.trim()}"> 🗑️ </button>
                    <strong>${user}:</strong> 
                    <span>${msg}</span>
                </div>
            `;

            let cajaResena = contenedorVotos.find('.resena-box');
            
            // Mostrar sin comentarios si esta vacia la caja
            if(cajaResena.find('p').length > 0 && cajaResena.text().includes("Sin comentarios")){
                cajaResena.empty();
            }

            //Agregar comentario 
            cajaResena.append(nuevoComentario);
        });
    }
});
    //Borrar comentario
    $(document).on('click', '.btn-borrar-comentario', function(){
    let boton = $(this);
    let id_comentario = boton.data("id");
    let caja_comentario = boton.closest('.comentario-individual');
    
    if(confirm("¿Seguro que quieres borrar el comentario?")){
        $.get('../inc/eliminar_comentario.php', { id: id_comentario }, function(respuesta){
            console.log("Intentando borrar comentario con ID:", id_comentario);
            console.log("Respuesta exacta del PHP: '" + respuesta + "'");
            
            if(respuesta.trim() === "borrado_ok"){
                caja_comentario.fadeOut(400, function(){
                    $(this).remove();
                });
            } else {
                alert("No se pudo borrar: " + respuesta);
            }
        }); 
    }
});

    //Editar comentario
$(document).on('click', '.texto-comentario.es-editable', function() {
    let boton = $(this);
    let id_comentario = boton.data('id');
    let spanTexto = $(`.texto-comentario[data-id='${id_comentario}']`);
    let textoActual = spanTexto.text();
    
    let nuevoTexto = prompt("Edita el comentario:", textoActual);

    if (nuevoTexto !== null && nuevoTexto.trim() !== "" && nuevoTexto !== textoActual){
        $.post('../inc/editar_comentario.php', {
            id: id_comentario,
            texto: nuevoTexto
        }, function(respuesta){
            if(respuesta.trim() === "editado_ok"){
                spanTexto.text(nuevoTexto); 
            } else {
                alert("Error desde el servidor: " + respuesta);
            }
        }); 
    } 
}); 

    //Boton dropdwon
$(document).ready(function(){
    $('#user-name-click').click(function(e){
        e.stopPropagation(); 
        $('#dropdown-menu').toggleClass('show-menu');
    });

    $(document).click(function(){
        $('#dropdown-menu').removeClass('show-menu');
    });
});

var tipoEdicion = ""; 

// Abrir Modal al clicar en opciones del dropdown
$('.btn-perfil').click(function(e){
    e.preventDefault();
    //Tipo de modal
    tipoEdicion = $(this).data('tipo'); 
    let titulo = $(this).text();
    
    $('#modal-titulo').text(titulo);
    // Si es password, cambiamos el tipo de input
    $('#input-modal').attr('type', tipoEdicion === 'email' ? 'email' : (tipoEdicion === 'pass' ? 'password' : 'text'));
    $('#modalPerfil').fadeIn(200);
});

// Cerrar Modal
$('#btn-cerrar-modal').click(function(){
    $('#modalPerfil').fadeOut(200);
});

// Enviar cambio a la Base de Datos
$('#btn-guardar-perfil').click(function(){
    let nuevoValor = $('#input-modal').val();

    if(nuevoValor.trim() === ""){
        alert("El campo no puede estar vacío");
        return;
    }
    
    //Validacion de email
    if(tipoEdicion === 'email'){
        let regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!regexEmail.test(nuevoValor)){
            alert("Por favor, introduce un correo electrónico válido");
            return;
        }
    }

    $.post('../inc/actualizar_perfil.php', {
        tipo: tipoEdicion,
        valor: nuevoValor
    }, function(respuesta){
        if(respuesta.trim() === "ok"){
            alert("¡Actualizado con éxito!");
            location.reload(); // Recargamos para ver los cambios (como el nombre en el header)
        } else {
            alert("Error: " + respuesta);
        }
    });
});



$(document).ready(function() {
        //FUNCIÓN EDITAR
    $('.btn-editar-user').click(function() {
        //COJEMOS EL ID
        const idUsuario = $(this).closest('tr').data('id');
        $('#edit-id').val(idUsuario);
        $('#modalEditar').fadeIn(200);
    });

    // --- 2. FUNCIÓN BLOQUEAR/ACTIVAR ---
    $('.btn-bloquear').click(function() {
        const idUsuario = $(this).closest('tr').data('id');
        const boton = $(this);

        $.post('../inc/acciones_admin.php', { accion: 'bloquear', id: idUsuario }, function(response) {
            if (response.trim() === 'ok') {
                // En lugar de alert, recargamos para que cambie el color del circulito de estado
                location.reload();
            }
        });
    });

    // --- 3. FUNCIÓN ELIMINAR ---
    $('.btn-eliminar-user').click(function() {
        const idUsuario = $(this).closest('tr').data('id');
        const fila = $(this).closest('tr');

        if (confirm("⚠️ ¿Estás SEGURO de eliminar este usuario? Esta acción no se puede deshacer.")) {
            $.post('../inc/acciones_admin.php', { accion: 'eliminar', id: idUsuario }, function(response) {
                if (response.trim() === 'ok') {
                    
                    fila.fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    alert("Error al eliminar: " + response);
                }
            });
        }
    });

});

//FUNCION CREAR NUEVO USUARIO
$('.btn-upload').click(function() {
    $('#modalCrearUsuario').fadeIn(200);

});


//CANCELAR MODAL EDICION
$('#btn-cancelar-editar').click(function() {
    $('#modalEditar').fadeOut(200);
});

//CANCELAR MODAL CREACION
$('#btn-cancelar-crear').click(function() {
    $('#modalCrearUsuario').fadeOut(200);
});

//GUARDAR MODO EDICION

$('#btn-guardar-cambios').click(function() {
    let id = $('#edit-id').val();
    let nombre = $('#edit-nombre').val();
    let email = $('#edit-email').val();
    let password = $('#edit-pass').val();
    let rol = $('input[name="nuevo-rol"]:checked').val();

    if (email.length > 0 && !email.includes("@")) {
    alert("Introduce un email válido");
    return; 
}
    
    $.ajax ({
        url: '../inc/acciones_admin.php',
        method: 'POST',
        data: {
            accion: 'editar-completo',
            id: id,
            nombre: nombre,
            email: email,
            password: password,
            rol: rol
        },
        dataType: 'json', 
        success: function(respuesta) {
            
            if(respuesta.success) {
                alert(respuesta.message);
                location.reload();
            } else {
                alert("Error: " + respuesta.message);
            }
        },
        error: function(xhr) {
            try {
                let errorDetalle = JSON.parse(xhr.responseText);
                alert("Error: " + errorDetalle.message + (errorDetalle.error ? " (" + errorDetalle.error + ")" : ""));
            } catch (e) {
                alert("Error crítico en el servidor. Revisa la consola.");
                console.log(xhr.responseText);
            }
        }
    });
});

//GUARDAR MODO CREACION USUARIO

$('#btn-guardar-usuario').click(function(){
    let nombre = $('#nuevo-nombre').val();
    let correo = $('#nuevo-email').val();
    let password = $('#nueva-pass').val();
    let rol = $('input[name="nuevo-rol"]:checked').val();

    if (nombre === "" || correo === "" || password === "" || !rol) {
        alert("¡Error! Todos los campos son obligatorios para crear un usuario.");
        return; 
    }

    if (correo.length > 0 && !correo.includes("@")) {
    alert("Introduce un email válido");
    return; 
}

    $.ajax({
        url: '../inc/acciones_admin.php',
        method: 'POST',
        data: {
        accion: 'crear',
        nombre: nombre,
        correo: correo,
        password: password,
        rol: rol
        },
        dataType: 'json',
        success: function(respuesta){

            if (respuesta.success){
                alert(respuesta.mensaje);
                location.reload();
            }else{
                alert("Error " + respuesta.mensaje)
            }
        },
        error: function(xhr) {
            try {
                let errorDetalle = JSON.parse(xhr.responseText);
                alert("Error: " + errorDetalle.message + (errorDetalle.error ? " (" + errorDetalle.error + ")" : ""));
            } catch (e) {
                alert("Error crítico en el servidor. Revisa la consola.");
                console.log(xhr.responseText);
            }
        }
    }
    )
});

});




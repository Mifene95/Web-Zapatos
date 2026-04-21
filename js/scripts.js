$(document).ready(function(){
    // Pintar estrellas
    $('.star').click(function(){
        var contenedor = $(this).parent();
        var puntos = $(this).data('v');
        var idZapato = $(this).closest('.contenedor-votos').data('id');

        contenedor.addClass('votado');
        $(this).css('color', 'gold').prevAll().css('color', 'gold');
        $(this).nextAll().css('color', '#ccc');

        // ENVIAR A LA BASE DE DATOS
        $.post('../inc/guardar_voto.php', { id: idZapato, puntos: puntos });
    });

    $('.btn-comentar').click(function(){
    let idZapato = $(this).closest('.contenedor-votos').data('id');
    let user = $(this).data('user-name');
    let msg = prompt("Escribe tu comentario:");
    
    if(msg) {
        let btn = $(this);
        let contenedorVotos = btn.closest('.contenedor-votos');

        $.post('../inc/guardar_voto.php', { id: idZapato, comentario: msg }, function(response){

            // 2. Creamos solo el HTML del comentario individual
            let nuevoComentario = `
                <div class="comentario-individual" style="border-bottom: 1px solid #eee; margin-bottom: 5px;">
                <button class="btn-borrar-comentario" data-id="${response.trim()}"> 🗑️ </button>
                    <strong>${user}:</strong> 
                    <span>${msg}</span>
                </div>
            `;

            // 3. Buscamos el div .resena-box que YA EXISTE en el PHP y le añadimos el nuevo comentario
            let cajaResena = contenedorVotos.find('.resena-box');
            
            // Mostrar sin comentarios si esta vacia la caja
            if(cajaResena.find('p').length > 0 && cajaResena.text().includes("Sin comentarios")){
                cajaResena.empty();
            }

            cajaResena.append(nuevoComentario);
        });
    }
});

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

});
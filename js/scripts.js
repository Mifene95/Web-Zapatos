$(document).ready(function(){
    // Efecto Hover y Click (Ya lo tenías, añadimos el envío de datos)
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
    let msg = prompt("Escribe tu comentario:");
    
    if(msg) {
        let btn = $(this);
        // Usamos AJAX para guardar en segundo plano
        $.post('../inc/guardar_voto.php', { id: idZapato, comentario: msg }, function(){
            // En lugar de "Guardado", inyectamos el formato exacto de la BBDD
            btn.after('<p style="font-size:12px; color:blue;">Reseña: ' + msg + '</p>');
            btn.remove(); // Eliminamos el botón por completo para que quede igual que al cargar
        });
    }
});
});
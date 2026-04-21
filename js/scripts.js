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
    console.log(user);
    let msg = prompt("Escribe tu comentario:");
    
    if(msg) {
        let btn = $(this);
        let contenedorVotos = btn.closest('.contenedor-votos');

        $.post('../inc/guardar_voto.php', { id: idZapato, comentario: msg }, function(){
            // 1. Eliminamos el botón de comentar
            btn.remove();

            // 2. Creamos el HTML exacto que usa el CSS .resena-box
            let htmlResena = `
                <div class="resena-box">
                    <p><strong>Reseña de ${user}: </strong> ${msg}</p>
                </div>
            `;

            // 3. Lo añadimos al final del contenedor de votos
            contenedorVotos.append(htmlResena);
        });
    }
});
});
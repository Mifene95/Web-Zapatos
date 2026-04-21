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
            // 1. YA NO BORRAMOS EL BOTÓN (btn.remove() eliminado)
            // Así el usuario puede comentar varias veces o diferentes usuarios pueden hacerlo.

            // 2. Creamos solo el HTML del comentario individual
            let nuevoComentario = `
                <div class="comentario-individual" style="border-bottom: 1px solid #eee; margin-bottom: 5px;">
                    <strong>${user}:</strong> 
                    <span>${msg}</span>
                </div>
            `;

            // 3. Buscamos el div .resena-box que YA EXISTE en el PHP y le añadimos el nuevo comentario
            let cajaResena = contenedorVotos.find('.resena-box');
            
            // Si la caja está vacía (decía "Sin comentarios aún"), quitamos ese texto antes
            if(cajaResena.find('p').length > 0 && cajaResena.text().includes("Sin comentarios")){
                cajaResena.empty();
            }

            cajaResena.append(nuevoComentario);
        });
    }
});
});
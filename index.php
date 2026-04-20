<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web de Zapatos - Compartir Imágenes</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Estilos básicos para que no se vea tan vacío */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #1a73e8; }
        .upload-section { border: 2px dashed #1a73e8; padding: 20px; text-align: center; border-radius: 8px; background: #f8faff; margin-bottom: 30px; }
        input[type="file"] { margin-bottom: 15px; }
        input[type="submit"] { background-color: #1a73e8; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #1557b0; }
        .gallery-title { border-bottom: 2px solid #eee; padding-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h1>👟 ComparteTusZapatos</h1>
    <p style="text-align:center;">Sube y comparte tus mejores zapatos con la comunidad.</p>

    <div class="upload-section">
        <h3>Subir Nueva Imagen</h3>
        
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            
            <input type="file" name="foto_zapato" id="foto_zapato" accept="image/*" required>
            <br>
            
            <input type="submit" value="Publicar Zapato">
            
        </form>
    </div>

    <div class="gallery-section">
        <h2 class="gallery-title">Galería Reciente</h2>
        <div id="contenedor-zapatos">
            <p style="color: #666;">Todavía no hay zapatos subidos. ¡Sé el primero!</p>
        </div>
    </div>
</div>

<script src="js/main.js"></script>

</body>
</html>
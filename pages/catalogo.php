<?php
session_start();
require '../inc/db.php';

//Comprueba si estas logeado
if (!isset($_SESSION['rol'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Zapatos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <span>Bienvenido, <strong><?php echo $_SESSION['nombre']; ?></strong></span>
        <a href="../inc/logout.php" class="btn-logout">Cerrar Sesión</a>
    </header>

    <div class="container">
        <div class="upload-section">
            <h3>Subir nuevo zapato</h3>
            <p>Comparte una foto del producto:</p>
            <form action="../upload.php" method="POST" enctype="multipart/form-data">
                <legend>Nombre del post</legend>
                <input type="text" name="titulo_zapato" placeholder="Ej: Zapato de boda" required>

                <input type="file" name="foto_zapato" required>
                <button type="submit" class="btn-upload">Publicar Zapato</button>
            </form>
        </div>

        <h2 class="titulo">Nuestros Zapatos</h2>
        
        <div class="galeria">
            <?php
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
            while ($row = $stmt->fetch()) {
                echo "<div class='zapato-card'>";

                echo "<h3>" . htmlspecialchars($row['titulo']) . "</h3>";
                    // Imagen del zapato
                    echo "<img src='../uploads/{$row['filename']}' alt='Zapato'>";

                    // BOTÓN ELIMINAR ZAPATO 
                    if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador' || $_SESSION['user_id'] == $row['user_id']) {
                        echo "<div class='admin-actions'>";
                            echo "<a href='../inc/eliminar.php?id={$row['id']}' class='btn-eliminar' 
                            onclick='return confirm(\"¿Estás seguro de eliminar este zapato?\")'>
                            🗑️ Eliminar
                            </a>";
                        echo "</div>";
                    }
                    
                    // SECCIÓN DE VOTOS Y RESEÑAS
                    if ($_SESSION['rol'] === 'user' || $_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador') {
                        echo "<div class='contenedor-votos' data-id='{$row['id']}'>";
                            
                            // Estrellas
                            echo "<div class='estrellas'>";
                            for ($i = 1; $i <= 5; $i++) {
                                $color = ($i <= $row['estrellas']) ? 'gold' : '#ccc';
                                echo "<span class='star' data-v='$i' style='color:$color'>★</span>";
                            }
                            echo "</div>";
                            
                            // Comentarios
                            if (!empty($row['comentario_texto'])) {
                                echo "<div class='resena-box'>";
                                    echo "<p><strong>Reseña:</strong> " . htmlspecialchars($row['comentario_texto']) . "</p>";
                                    
                                    // Eliminar comentario (Solo Admin)
                                    if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador') {
                                        echo "<a href='../inc/eliminar_comentario.php?id={$row['id']}' class='link-eliminar-resena'
                                                onclick='return confirm(\"¿Eliminar este comentario?\")'>
                                                [Eliminar Reseña]
                                            </a>";
                                    }
                                echo "</div>";
                            } else {
                                
                                if ($_SESSION['rol'] === 'user' || $_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador') {
                                    echo "<button class='btn-comentar'>Comentar</button>";
                                }
                            }
                        echo "</div>"; // Cierre contenedor-votos
                    }
                echo "</div>"; // Cierre zapato-card
            }
            ?>
        </div>
    </div>

    <script src="../js/scripts.js"></script>
</body>
</html>
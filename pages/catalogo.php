<?php
session_start();
require '../inc/db.php';

// Comprueba si estás logueado
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
        <span>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
        <a href="../inc/logout.php" class="btn-logout">Cerrar Sesión</a>
    </header>

    <div class="container">
        <div class="upload-section">
            <h3>Subir nuevo zapato</h3>
            <form action="../upload.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="titulo_zapato" placeholder="Título del zapato" required>
                <input type="file" name="foto_zapato" required>
                <button type="submit" class="btn-upload">Publicar Zapato</button>
            </form>
        </div>

        <h2 class="titulo">Nuestros Zapatos</h2>

        <div class="galeria">
            <?php
            // Consulta bbdd
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");

            while ($row = $stmt->fetch()) {
                echo "<div class='zapato-card'>";

                // 1. Título del zapato
                echo "<h3 class='zapato-titulo'>" . htmlspecialchars($row['titulo'] ?? 'Sin título') . "</h3>";

                // 2. Imagen
                echo "<img src='../uploads/{$row['filename']}' alt='Zapato'>";

                // 3. Acciones 
                echo "<div class='admin-actions'>";
                if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador') {
                    // El Admin ve ambos botones
                    echo "<a href='editar_zapato.php?id={$row['id']}' class='btn-editar'>✏️ Editar</a>";
                    echo "<a href='../inc/eliminar.php?id={$row['id']}' class='btn-eliminar' 
                                onclick='return confirm(\"¿Estás seguro de eliminar este zapato?\")'>🗑️ Eliminar</a>";
                } elseif ($_SESSION['user_id'] == $row['user_id']) {
                    // El usuario normal solo ve eliminar si es suyo
                    echo "<a href='../inc/eliminar.php?id={$row['id']}' class='btn-eliminar' 
                                onclick='return confirm(\"¿Quieres eliminar tu publicación?\")'>🗑️ Eliminar</a>";
                }
                echo "</div>";

                // 4. Sección de Votos y Reseñas
                echo "<div class='contenedor-votos' data-id='{$row['id']}'>";

                // Estrellas
                echo "<div class='estrellas'>";
                for ($i = 1; $i <= 5; $i++) {
                    $color = ($i <= $row['estrellas']) ? 'gold' : '#ccc';
                    echo "<span class='star' data-v='$i' style='color:$color'>★</span>";
                }
                echo "</div>";

                // Reseña
                if (!empty($row['comentario_texto'])) {
                    echo "<div class='resena-box'>";
                    echo "<p><strong>Reseña:</strong> " . htmlspecialchars($row['comentario_texto']) . "</p>";

                    if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador') {
                        echo "<a href='../inc/eliminar_comentario.php?id={$row['id']}' class='link-eliminar-resena'>[Eliminar Reseña]</a>";
                    }
                    echo "</div>";
                } else {
                    echo "<button class='btn-comentar' data-user-name='{$_SESSION['nombre']}'>Comentar</button>";
                }
                echo "</div>";

                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script src="../js/scripts.js"></script>
</body>

</html>
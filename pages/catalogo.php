<?php
session_start();
require '../inc/db.php';

if (!isset($_SESSION['rol'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Catálogo</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header style="background: #333; color: white; padding: 10px; display: flex; justify-content: space-between;">
        <span>Bienvenido, <strong><?php echo $_SESSION['nombre']; ?></strong> (<?php echo $_SESSION['rol']; ?>)</span>
        <a href="../inc/logout.php" style="color: white; text-decoration: none; background: #555; padding: 5px 10px; border-radius: 3px;">Cerrar Sesión</a>
    </header>

    <div style="padding: 20px;">
        <div style="background: #e2e3e5; border: 1px solid #d6d8db; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
            <h3>Subir nuevo zapato</h3>
            <p>Comparte una foto del producto:</p>
            <form action="../upload.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="foto_zapato" required>
                <button type="submit" style="cursor:pointer;">Publicar Zapato</button>
            </form>
        </div>

        <h2>Nuestros Zapatos</h2>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <?php
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
            while ($row = $stmt->fetch()) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; background: white; border-radius: 8px; width: 220px;'>";
                echo "<img src='../uploads/{$row['filename']}' width='200' style='border-radius: 4px;'><br>";

                // BOTÓN ELIMINAR ZAPATO (Admin o dueño)
                // Corregido: El paréntesis estaba mal puesto aquí
                if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador' || $_SESSION['user_id'] == $row['user_id']) {
                    echo "<div style='margin-top: 10px;'>";
                        echo "<a href='../inc/eliminar.php?id={$row['id']}' 
                        style='color: red; text-decoration: none; font-size: 12px; font-weight: bold;' 
                        onclick='return confirm(\"¿Estás seguro de eliminar este zapato?\")'>
                        [X] Eliminar Zapato
                        </a>";
                    echo "</div>";
                }
                
                // SECCIÓN DE VOTOS Y RESEÑAS
                if ($_SESSION['rol'] === 'user' || $_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador') {
                    echo "<div class='contenedor-votos' data-id='{$row['id']}'>";
                        
                        // Estrellas
                        echo "<div class='estrellas' style='cursor: pointer; font-size: 24px;'>";
                        for ($i = 1; $i <= 5; $i++) {
                            $color = ($i <= $row['estrellas']) ? 'gold' : '#ccc';
                            echo "<span class='star' data-v='$i' style='color:$color'>★</span>";
                        }
                        echo "</div>";
                        
                        // Comentarios
                        if (!empty($row['comentario_texto'])) {
                            echo "<div style='background: #f9f9f9; padding: 5px; border-radius: 4px; margin-top: 10px; border-left: 2px solid blue;'>";
                                echo "<p style='font-size:12px; color:blue; margin: 0;'>";
                                echo "<strong>Reseña:</strong> " . htmlspecialchars($row['comentario_texto']);
                                echo "</p>";
                                
                                // Botón eliminar comentario (Solo Admin/Administrador)
                                if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador') {
                                    echo "<a href='../inc/eliminar_comentario.php?id={$row['id']}' 
                                            style='color: red; text-decoration: none; font-size: 10px; display: block; margin-top: 5px;'
                                            onclick='return confirm(\"¿Eliminar este comentario?\")'>
                                            [Eliminar Reseña]
                                        </a>";
                                }
                            echo "</div>";
                        } else {
                            // Solo el user puede comentar si no hay reseña
                            if ($_SESSION['rol'] === 'user') {
                                echo "<button class='btn-comentar' style='margin-top: 10px;'>Comentar</button>";
                            }
                        }
                    echo "</div>"; // Cierre contenedor-votos
                }
                echo "</div>"; // Cierre div de la tarjeta
            }
            ?>
        </div>
    </div>

    <script src="../js/scripts.js"></script>
</body>
</html>
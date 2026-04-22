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
        <span class="span-bienvenida">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></span>
        <a href="../inc/logout.php" class="btn-logout">Cerrar Sesión</a>
    </header>

    <div class="container">
        <div class="upload-section">
            <h2 style="margin-bottom: 10px;">Subir nuevo zapato</h2>
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
                    echo "<a href='../inc/eliminar.php?id={$row['id']}'class='btn-eliminar' 
                                onclick='return confirm(\"¿Estás seguro de eliminar este zapato?\")'>🗑️ Eliminar</a>";
                } elseif ($_SESSION['user_id'] == $row['user_id']) {
                    // El usuario normal solo ve eliminar si es suyo
                    echo "<a href='../inc/eliminar.php?id={$row['id']}' class='btn-eliminar' 
                                onclick='return confirm(\"¿Quieres eliminar tu publicación?\")'>🗑️ Eliminar</a>";
                }
                echo "</div>";

                // 4. Sección de Votos y Reseñas
                echo "<div class='contenedor-votos' data-id='{$row['id']}'>";

                // ESTRELLAS
                echo "<div class='estrellas'>";
                for ($i = 1; $i <= 5; $i++) {
                    $color = ($i <= $row['estrellas']) ? 'gold' : '#ccc';
                    echo "<span class='star' data-v='$i' style='color:$color'>★</span>";
                }
                echo "</div>";

                // RESEÑAS
                echo "<div class='resena-box' style='background: #f9f9f9; padding: 10px; border-radius: 5px; margin-top: 10px;'>";

                $stmt_com = $pdo->prepare("SELECT * FROM comentarios WHERE id_zapato = ? ORDER BY fecha DESC");
                $stmt_com->execute([$row['id']]);
                $comentarios = $stmt_com->fetchAll();

                if (count($comentarios) > 0) {
                    foreach ($comentarios as $com) {

                        echo "<div class='comentario-individual' style='border-bottom: 1px solid #eee; margin-bottom: 5px'>";

                        //Comprobamos si es admin o dueño para mostrar boton borrar
                        $esAdmin = ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador');
                        $esDueño = ($_SESSION['user_id'] == $com['id_usuario']);
                        $claseExtra = $esAdmin ? 'es-admin' : '';
                        if ($esAdmin || $esDueño) {
                            echo "<button class='btn-borrar-comentario' data-id='{$com['id']}'> 🗑️ </button>";
                        }
                        //Comprobar si puede editar
                        $claseEditable = ($esAdmin || $esDueño) ? 'es-editable' : '';
                        echo "<strong>" . htmlspecialchars($com['nombre_usuario']) . ":</strong> ";
                        echo "<span class='texto-comentario $claseEditable' data-id='{$com['id']}'>"
                            . htmlspecialchars($com['comentario_texto']) .
                            "</span>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='sin-comentarios' style='color: gray;'>Sin comentarios aún.</p>";
                }
                echo "</div>";

                echo "<button class='btn-comentar' data-user-name='{$_SESSION['nombre']}' style='margin-top:10px;'>Añadir comentario</button>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script src="../js/scripts.js"></script>
</body>

</html>
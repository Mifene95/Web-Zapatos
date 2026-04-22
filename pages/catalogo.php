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
<div id="modalPerfil" class="modal-overlay">
    <div class="modal-content">
        <h3 id="modal-titulo">Editar Perfil</h3>
        <div class="modal-body">
            <p class="modal-label">Introduce el nuevo dato:</p>
            <input type="text" id="input-modal" class="modal-input" placeholder="Escribe aquí...">
        </div>
        <div class="modal-footer">
            <button id="btn-cerrar-modal" class="btn-secundario">Cancelar</button>
            <button id="btn-guardar-perfil" class="btn-primario">Guardar Cambios</button>
        </div>
    </div>
</div>

<body>
    <header>
        <span class="span-bienvenida">
            Bienvenido,
            <div class="user-dropdown" style="display: inline-block; position: relative;">
                <strong id="user-name-click" class="user-trigger">
                    <span class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="icon-chevron">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </strong>

                <div id="dropdown-menu" class="dropdown-content">
                    <a href="#" class="btn-perfil" data-tipo="nombre">
                        <svg style="margin-right:10px" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Cambiar Nombre
                    </a>

                    <a href="#" class="btn-perfil" data-tipo="email">
                        <svg style="margin-right:10px" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Cambiar Correo
                    </a>

                    <a href="#" class="btn-perfil" data-tipo="pass">
                        <svg style="margin-right:10px" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        Cambiar Password
                    </a>

                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador')): ?>
                        <a href="../pages/panel_admin.php">
                            <svg style="margin-right:10px" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Gestionar Usuarios
                        </a>
                    <?php endif; ?>

                    <a href="../inc/logout.php" style="color: #d63031; border-top: 1px solid #f1f2f6;">
                        <svg style="margin-right:10px" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </span>
        <a href="../inc/logout.php" class="btn-logout">Cerrar Sesión</a>
    </header>



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

                $stmt_com = $pdo->prepare("
                SELECT *, 
                (SELECT username FROM users WHERE users.id = comentarios.id_usuario) AS username 
                FROM comentarios 
                WHERE id_zapato = ? 
                ");
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
                        echo "<strong>" . htmlspecialchars($com['username']) . ":</strong> ";
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
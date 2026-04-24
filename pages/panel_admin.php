<?php
session_start();
require '../inc/db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// SEGURIDAD: Si no es admin, fuera.
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    header("Location: ../index.php");
    exit();
}

// ---  CONSULTA CON SUBCONSULTA ---

$stmt = $pdo->prepare("
    SELECT *, 
    (SELECT nombre_rol FROM roles WHERE roles.id = users.role_id) AS nombre_rol 
    FROM users 
    WHERE id != ? 
    ORDER BY id DESC
");
$stmt->execute([$_SESSION['user_id']]);
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Gestión de Usuarios</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/scripts.js"></script>
</head>

<div id="modalEditar" class="modal-overlay">
    <div class="modal-content">
        <h3 id="modal-titulo">Gestión de Usuario</h3>

        <div class="modal-body" style="text-align: left;"> <input type="hidden" id="edit-id">

            <label class="modal-label" style="margin-bottom: 5px;">Nombre de Usuario:</label>
            <input type="text" id="edit-nombre" class="modal-input" placeholder="Nuevo nombre">

            <label class="modal-label" style="margin-bottom: 5px;">Correo Electrónico:</label>
            <input type="email" id="edit-email" class="modal-input" placeholder="Nuevo email">

            <label class="modal-label" style="margin-bottom: 5px;">Nueva Contraseña:</label>
            <input type="password" id="edit-pass" class="modal-input" placeholder="Nueva Contraseña">

            <div style="display: flex; gap: 20px; margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                <label style="cursor:pointer;">
                    <input type="radio" name="nuevo-rol" id="admin" value="1" checked> Administrador
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="nuevo-rol" id="user" value="2"> Usuario Estándar
                </label>
            </div>
        </div>

        <div class="modal-footer">
            <button id="btn-cancelar-editar" class="btn-secundario" style="flex:1;">Cancelar</button>
            <button id="btn-guardar-cambios" class="btn-primario" style="flex:1;">Guardar</button>
        </div>
    </div>
</div>

<div id="modalCrearUsuario" class="modal-overlay">
    <div class="modal-content">
        <h3 id="modal-titulo">Creación de Usuario</h3>
        <div>
            <label class="modal-label" style="margin-bottom: 5px;">Nombre de Usuario:</label>
            <input type="text" id="nuevo-nombre" class="modal-input" placeholder="Nuevo nombre">

            <label class="modal-label" style="margin-bottom: 5px;">Correo Electrónico:</label>
            <input type="email" id="nuevo-email" class="modal-input" placeholder="Nuevo email">

            <label class="modal-label" style="margin-bottom: 5px;">Nueva Contraseña:</label>
            <input type="password" id="nueva-pass" class="modal-input" placeholder="Nueva Contraseña">

            <label class="modal-label" style="margin-bottom: 5px;">Selecciona Rol:</label>
            <div style="display: flex; gap: 20px; margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                <label style="cursor:pointer;">
                    <input type="radio" name="nuevo-rol" id="admin" value="1" checked> Administrador
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="nuevo-rol" id="user" value="2"> Usuario Estándar
                </label>
            </div>
        </div>


        <div class="modal-footer">
            <button id="btn-cancelar-crear" class="btn-secundario" style="flex:1;">Cancelar</button>
            <button id="btn-guardar-usuario" class="btn-primario" style="flex:1;">Guardar</button>
        </div>
    </div>
</div>

<body>

    <header>
        <a href="catalogo.php" style="color: white; text-decoration: none; font-weight: bold;">← Volver al Catálogo</a>
        <span class="span-bienvenida">Panel Administrativo</span>
        <a href="../inc/logout.php" class="btn-logout">Cerrar Sesión</a>
    </header>

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="titulo">Gestión de Usuarios</h2>
            <button class="btn-upload" style="width: auto; padding: 10px 20px;">+ Crear Nuevo Usuario</button>
        </div>

        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr data-id="<?php echo $u['id']; ?>">
                        <td>#<?php echo $u['id']; ?></td>
                        <td style="font-weight: bold;"><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <span class="badge-rol">
                                <?php echo strtoupper($u['nombre_rol'] ?? 'Sin Rol'); ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($u['estado'] == 1): ?>
                                <span class="status-pill status-online"></span> Activo
                            <?php else: ?>
                                <span class="status-pill status-offline"></span> Inactivo
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="flex">
                                <button class="btn-accion btn-editar-user" title="Editar datos">✏️</button>
                                <button class="btn-accion btn-bloquear" title="Bloquear/Desbloquear">🚫</button>
                                <button class="btn-accion btn-eliminar-user" title="Eliminar definitivamente">🗑️</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>
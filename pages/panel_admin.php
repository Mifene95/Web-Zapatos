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

// --- PASO 1: LA CONSULTA CON SUBCONSULTA ---
// Cambiamos el SELECT * por esto:
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

<body>

    <header>
        <a href="catalogo.php" style="color: white; text-decoration: none; font-weight: bold;">← Volver al Catálogo</a>
        <span class="span-bienvenida">Panel Administrativo</span>
        <div></div>
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
                                <span class="status-pill status-offline"></span> Bloqueado
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn-accion btn-editar-user" title="Editar datos">✏️</button>
                            <button class="btn-accion btn-bloquear" title="Bloquear/Desbloquear">🚫</button>
                            <button class="btn-accion btn-eliminar-user" title="Eliminar definitivamente">🗑️</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>
<?php
session_start();
require 'db.php';

if (!isset($_SESSION['rol'])) {
    die("Acceso denegado.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscamos el zapato para ver quién es el dueño
    $stmt = $pdo->prepare("SELECT filename, user_id FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();

    if ($producto) {
        // REGLA DE ORO: Solo borra si es Admin O si es el dueño
        if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador' || $_SESSION['user_id'] == $producto['user_id']) {
            
            $rutaImagen = "../uploads/" . $producto['filename'];
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }

            $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $delete->execute([$id]);
        } else {
            die("No tienes permiso para borrar este zapato.");
        }
    }
}

header("Location: ../pages/catalogo.php");
exit();
?>


<?php
session_start();
require 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Comprobamos usuario
if (!isset($_SESSION['rol']) || !isset($_SESSION['user_id'])) {
    echo "error_sesion";
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscamos dueño
    $stmt = $pdo->prepare("SELECT id_usuario FROM comentarios WHERE id = ?");
    $stmt->execute([$id]);
    $comentario = $stmt->fetch(); // Usamos fetch para un solo registro

    if ($comentario) {
        // Comprobar permisos
        $esAdmin = ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'administrador');
        $esDueño = ($_SESSION['user_id'] == $comentario['id_usuario']);

        if ($esAdmin || $esDueño) {
            $delete = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
            $delete->execute([$id]);
            echo "borrado_ok";
        } else {
            echo "error_permiso";
        }
    } else {
        echo "error_no_existe";
    }
}
exit();

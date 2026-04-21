<?php
session_start();
require 'db.php';

// Verificamos que quien entra sea un rol con permisos
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    die("No tienes permiso para borrar comentarios.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
    $stmt->execute([$id]);
}
echo "borrado_ok";
exit();

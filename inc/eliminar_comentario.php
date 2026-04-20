<?php
session_start();
require 'db.php';

// Verificamos que quien entra sea un rol con permisos
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    die("No tienes permiso para borrar comentarios.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ponemos el comentario a NULL para "borrarlo" sin borrar el producto
    $stmt = $pdo->prepare("UPDATE products SET comentario_texto = NULL WHERE id = ?");
    $stmt->execute([$id]);
}

// Volvemos al catálogo
header("Location: ../pages/catalogo.php");
exit();
?>
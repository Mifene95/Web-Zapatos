<?php
session_start();
require 'db.php';


// Verificamos que el usuario esté logueado 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {

    $id_zapato = $_POST['id'] ?? null;
    $id_usuario = $_SESSION['user_id'];
    $nombre = $_SESSION['nombre'];

    if (!$id_zapato) {
        die("Error: ID de zapato no recibido");
    }

    if (isset($_POST['comentario']) && !empty($_POST['comentario'])) {
        $texto = $_POST['comentario'];

        // INSERT
        $sql = "INSERT INTO comentarios (id_zapato, id_usuario, comentario_texto) 
                VALUES (?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_zapato, $id_usuario, $texto]);

        //Actualizar el boton para borrar sin recargar
        $id_nuevo = $pdo->lastInsertId();
        echo $id_nuevo;
        exit;
    }

    // Puntos = estrellas
    if (isset($_POST['puntos'])) {
        $puntos = $_POST['puntos'];
        $stmt = $pdo->prepare("UPDATE products SET estrellas = ? WHERE id = ?");
        $stmt->execute([$puntos, $id_zapato]);
        exit;
    }
} else {
    echo "ERROR: Sesión no iniciada o acceso denegado";
}

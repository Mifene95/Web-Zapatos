<?php
session_start();
require 'db.php';

if ($_SESSION['rol'] !== "admin" && $_SESSION["rol"] !== "administrador") {
    die("No tienes permiso de administrador");
}

if (isset($_POST["id"]) && isset($_POST["texto"])) {

    $id = $_POST["id"];
    $nuevoTexto = trim($_POST['texto']);

    if (!empty($nuevoTexto)) {
        try {
            $sql = 'UPDATE comentarios SET comentario_texto = ? WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nuevoTexto, $id]);

            echo 'editado_ok';
            exit();
        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getMessage();
            exit();
        }
    }
}

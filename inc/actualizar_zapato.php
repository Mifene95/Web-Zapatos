<?php
session_start();
require 'db.php';

// 1. Seguridad: Solo el Admin puede ejecutar este script
if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador') {
    die("Acceso denegado. No tienes permisos de administrador.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nuevoTitulo = $_POST['titulo_zapato'];
    
    try {
        // 2. Obtener los datos actuales (para saber el nombre de la foto vieja)
        $stmt = $pdo->prepare("SELECT filename FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();

        if (!$producto) {
            die("Error: El producto no existe.");
        }

        // 3. ¿Ha subido una imagen nueva?
        if (!empty($_FILES['nueva_foto']['name'])) {
            
            // Validar tipo de archivo
            $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['nueva_foto']['type'], $permitidos)) {
                die("Error: Solo se permiten imágenes JPG, PNG o GIF.");
            }

            // Generar nuevo nombre y mover archivo
            $nombreArchivo = time() . "_" . basename($_FILES["nueva_foto"]["name"]);
            $rutaFinal = "../uploads/" . $nombreArchivo;

            if (move_uploaded_file($_FILES["nueva_foto"]["tmp_name"], $rutaFinal)) {
                // Borrar la foto vieja del servidor para ahorrar espacio
                $fotoVieja = "../uploads/" . $producto['filename'];
                if (file_exists($fotoVieja)) {
                    unlink($fotoVieja);
                }

                // Actualizar DB con NUEVA imagen y título
                $sql = "UPDATE products SET titulo = ?, filename = ? WHERE id = ?";
                $pdo->prepare($sql)->execute([$nuevoTitulo, $nombreArchivo, $id]);
            }
        } else {
            // 4. Si NO hay foto nueva, solo actualizar el título
            $sql = "UPDATE products SET titulo = ? WHERE id = ?";
            $pdo->prepare($sql)->execute([$nuevoTitulo, $id]);
        }

        // 5. Éxito: Volver al catálogo
        header("Location: ../pages/catalogo.php");
        exit();

    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}
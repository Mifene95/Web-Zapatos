<?php
// 1. Mostrar errores para saber qué falla
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'inc/db.php'; // Asegúrate de que este archivo existe en la carpeta inc

// 2. Revisar si hay sesión
if (!isset($_SESSION['user_id'])) {
    die("Error: No hay una sesión activa. ID de usuario no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 3. Configurar carpeta
    $directorio = "uploads/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombreArchivo = time() . "_" . basename($_FILES["foto_zapato"]["name"]);
    $rutaFinal = $directorio . $nombreArchivo;

    $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['foto_zapato']['type'], $permitidos)) {
    die("Error: Solo se permiten imágenes JPG, PNG o GIF.");
}
    // 4. Subir archivo
    if (move_uploaded_file($_FILES["foto_zapato"]["tmp_name"], $rutaFinal)) {
        
        try {
            // 5. Insertar usando el ID de la sesión
            $sql = "INSERT INTO products (filename, user_id) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombreArchivo, $_SESSION['user_id']]);
            
            // REDIRECCIÓN DIRECTA: Sin mensajes ni esperas
            header("Location: pages/catalogo.php");
            exit();

        } catch (PDOException $e) {
            die("Error de Base de Datos: " . $e->getMessage());
        }
        
    } else {
        die("Error: No se pudo mover el archivo a /uploads. Revisa permisos de carpeta.");
    }
}
?>
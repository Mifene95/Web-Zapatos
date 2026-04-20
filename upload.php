<?php
require 'inc/db.php';
session_start();

// Simulamos usuario admin por ahora
$_SESSION['user_id'] = 1; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $directorio = "uploads/"; // Aquí usamos tu carpeta
    
    // Creamos un nombre único para la imagen
    $nombreArchivo = time() . "_" . basename($_FILES["foto_zapato"]["name"]);
    $rutaFinal = $directorio . $nombreArchivo;
    
    // Comprobar si es imagen real
    $esImagen = getimagesize($_FILES["foto_zapato"]["tmp_name"]);
    
    if($esImagen !== false) {
        // Movemos el archivo de la memoria temporal a tu carpeta 'uploads'
        if (move_uploaded_file($_FILES["foto_zapato"]["tmp_name"], $rutaFinal)) {
            
            // Guardamos la ruta en la base de datos
            $sql = "INSERT INTO products (filename, user_id) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombreArchivo, $_SESSION['user_id']]);
            
            echo "<h2>¡Zapato subido con éxito!</h2>";
            echo "<a href='index.php'>Volver atrás</a>";
        } else {
            echo "Error al mover el archivo a la carpeta uploads.";
        }
    }
}
?>
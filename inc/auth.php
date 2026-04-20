<?php
session_start();
require 'db.php'; // Como están en la misma carpeta 'inc', no lleva ../

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $pass === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre']  = $user['username'];
        $_SESSION['rol']     = $user['role']; 

        header("Location: ../pages/catalogo.php");
        exit();
    } else {
        echo "Correo o contraseña incorrectos. <a href='../index.php'>Volver</a>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
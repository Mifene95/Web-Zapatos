<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $estado = $_POST['estado'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        if ($user["estado"] === 0) {
            echo "<script>
                alert('Tu cuenta está desactivada. Contacta con el administrador.');
                window.location.href = '../index.php';
            </script>";
            exit();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre']  = $user['username'];
        $_SESSION['estado'] = $user['estado'];

        if ($user['role_id'] == '1') {
            $_SESSION['rol'] = "admin";
        } else {
            $_SESSION['rol'] = "user";
        }


        header("Location: ../pages/catalogo.php");
        exit();
    } else {
        echo "<script>alert('Correo o contraseña incorrectos.');
        window.location.href = '../index.php';
        </script>";
    }
} else {
    header("Location: ../index.php");
    exit();
}

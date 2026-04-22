<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) exit("No autorizado");

$id = $_SESSION['user_id'];
$tipo = $_POST['tipo'];
$valor = $_POST['valor'];


$columna = "";
if ($tipo === 'nombre') $columna = "username";
if ($tipo === 'email')  $columna = "email";
if ($tipo === 'pass') {
    $columna = "password";
    $valor = password_hash($valor, PASSWORD_DEFAULT);
}

if ($columna === "") exit("Tipo inválido");

try {
    $stmt = $pdo->prepare("UPDATE users SET $columna = ? WHERE id = ?");
    if ($stmt->execute([$valor, $id])) {
        // Si editamos el nombre, actualizamos la sesión para que el header cambie al instante
        if ($tipo === 'nombre') $_SESSION['nombre'] = $valor;
        echo "ok";
    }
} catch (Exception $e) {
    echo "Error BBDD: " . $e->getMessage();
}

<?php
session_start();
require 'db.php';


if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    die("Acceso denegado");
}

$accion = $_POST['accion'] ?? '';
$id_usuario = $_POST['id'] ?? null;

if (!$id_usuario) die("ID no válido");

switch ($accion) {
    case 'editar_completo':
        // Recogemos y limpiamos los datos
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = trim($_POST['pass'] ?? '');

        $campos = [];
        $valores = [];

        // Solo añadimos a la consulta si el campo NO está vacío
        if (!empty($nombre)) {
            $campos[] = "username = ?";
            $valores[] = $nombre;
        }
        if (!empty($email)) {
            $campos[] = "email = ?";
            $valores[] = $email;
        }
        if (!empty($pass)) {
            $campos[] = "password = ?"; // Texto plano como pediste
            $valores[] = $pass;
        }

        // Si el admin no escribió nada en ningún campo, avisamos
        if (empty($campos)) {
            echo "sin_cambios";
            break;
        }

        // Añadimos el ID al final para el WHERE
        $valores[] = $id_usuario;

        // Construimos la SQL dinámicamente
        $sql = "UPDATE users SET " . implode(", ", $campos) . " WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($valores)) {
            echo "ok";
        } else {
            echo "error";
        }
        break;

    case 'bloquear':
        // Alterna entre 1 y 0 de forma automática
        $stmt = $pdo->prepare("UPDATE users SET estado = 1 - estado WHERE id = ?");
        $stmt->execute([$id_usuario]);
        echo "ok";
        break;

    case 'eliminar':
        // Elimina el registro por ID
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_usuario]);
        echo "ok";
        break;

    case 'crear':
        // Por si decides usar el botón de agregar nuevo
        $n = $_POST['nombre'];
        $e = $_POST['email'];
        $p = $_POST['pass'];
        $r = $_POST['rol'];

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, rol, estado) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$n, $e, $p, $r]);
        echo "ok";
        break;
}

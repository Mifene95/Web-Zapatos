<?php
session_start();
require 'db.php';


if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'administrador')) {
    die("Acceso denegado");
}

$accion = $_POST['accion'] ?? '';
$id_usuario = $_POST['id'] ?? null;


switch ($accion) {
    case 'editar-completo':
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $pass = $_POST['password'];
        $rol = $_POST['rol'];

        $sql = 'UPDATE users SET ';
        $campos = [];
        $params = [];

        if (!empty($nombre)) {
            $campos[] = "username = ?";
            $params[] = $nombre;
        }

        if (!empty($email)) {
            $campos[] = "email = ?";
            $params[] = $email;
        }

        if (!empty($pass)) {
            $campos[] = "password = ?";
            $params[] = $pass;
        }

        if (!empty($rol)) {
            $campos[] = "role_id = ?";
            $params[] = $rol;
        }

        if (count($campos) === 0) {
            echo json_encode(['success' => false, 'mensaje' => 'No has modificado ningún campo.']);
            exit;
        }

        $sql .= implode(", ", $campos);
        $sql .= " WHERE id = ?";
        $params[] = $id;


        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute($params);

        //COMPROBAR RESULTADO
        if ($resultado) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => '¡Usuario actualizado!'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo actualizar'
            ]);
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
        $n = $_POST['nombre'];
        $e = $_POST['correo'];
        $p = $_POST['password'];
        $r = $_POST['rol'];
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
        $resultado = $stmt->execute([$n, $e, $p, $r]);

        if ($resultado) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'mensaje' => '¡Usuario creado!'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'mensaje' => 'No se pudo crear el usuario'
            ]);
        }
        break;
}

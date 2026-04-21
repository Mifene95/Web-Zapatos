<?php
session_start();
require 'db.php';

//  ¿Está logueado?
if (!isset($_SESSION['user_id'])) {
    die("Debes iniciar sesión");
}

if (isset($_POST["id"]) && isset($_POST["texto"])) {
    $id = $_POST["id"];
    $nuevoTexto = trim($_POST['texto']);
    $user_id_sesion = $_SESSION['user_id'];
    $rol_sesion = $_SESSION['rol'];

    if (!empty($nuevoTexto)) {
        try {
            //  Buscamos el comentario 
            $stmtCheck = $pdo->prepare("SELECT id_usuario FROM comentarios WHERE id = ?");
            $stmtCheck->execute([$id]);
            $comentario = $stmtCheck->fetch();

            if (!$comentario) {
                die("Comentario no encontrado");
            }

            // ¿Es admin O es el dueño?
            $esAdmin = ($rol_sesion === "admin" || $rol_sesion === "administrador");
            $esDuenio = ($comentario['id_usuario'] == $user_id_sesion);

            if ($esAdmin || $esDuenio) {

                $sql = 'UPDATE comentarios SET comentario_texto = ? WHERE id = ?';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nuevoTexto, $id]);

                echo 'editado_ok';
                exit();
            } else {
                echo "No tienes permiso para editar este comentario";
                exit();
            }
        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getMessage();
            exit();
        }
    }
}

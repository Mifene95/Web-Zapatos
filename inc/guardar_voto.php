<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $id_zapato = $_POST['id'];
    
    if (isset($_POST['comentario'])) {
        $msg = $_POST['comentario'];
        $stmt = $pdo->prepare("UPDATE products SET comentario_texto = ? WHERE id = ?");
        $stmt->execute([$msg, $id_zapato]);
    } else {
        $puntos = $_POST['puntos'];
        $stmt = $pdo->prepare("UPDATE products SET estrellas = ? WHERE id = ?");
        $stmt->execute([$puntos, $id_zapato]);
    }
    echo "OK";
}
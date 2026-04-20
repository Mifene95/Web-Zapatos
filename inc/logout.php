<?php
session_start();
session_destroy(); // Borra todos los datos de la sesión
header("Location: ../index.php"); // Te manda al login
exit();
?>
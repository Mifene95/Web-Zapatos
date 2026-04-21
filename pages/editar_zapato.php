<?php
session_start();
require '../inc/db.php';

$id_provisional = $_GET['id'] ?? null;

if (!$id_provisional) {
    die("Error: No se especificó qué zapato editar.");
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id_provisional]);
$producto = $stmt->fetch();

if (!$producto) {
    die("Error: El zapato no existe.");
}
?>

<link rel="stylesheet" href="../css/styles.css">

<form action="../inc/actualizar_zapato.php" class="editar-admin" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

    <div style="margin-bottom: 15px;">
        <label>Título del Zapato:</label><br>
        <input type="text" name="titulo_zapato" value="<?php echo htmlspecialchars($producto['titulo']); ?>" required style="width: 100%; padding: 8px;">
    </div>

    <div style="margin-bottom: 15px;">
        <p class="img-titulo">Imagen actual:</p>
        <img src="../uploads/<?php echo $producto['filename']; ?>" width="150" style="border: 1px solid #ccc; padding: 5px;">
    </div>

    <div style="margin-bottom: 15px;">
        <label>Seleccionar nueva imagen (opcional):</label><br>
        <input type="file" name="nueva_foto" accept="image/*">
        <p class="aviso">Si no eliges ninguna, se mantendrá la foto actual.</p>
    </div>

    <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer;">
        Guardar cambios
    </button>
    <a href="catalogo.php" style="margin-left: 10px;">Cancelar</a>
</form>
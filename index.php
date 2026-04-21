<!DOCTYPE html>
<html lang="es">

<head>
    <title>Login - Tienda de Zapatos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="login-page">
    <div class="login-container">

        <h2>Bienvenido 👋</h2>
        <p style="margin-bottom:20px; color:#636e72;">
            Accede a tu cuenta para continuar
        </p>
        <form class="inputs-form" action="inc/auth.php" method="POST">
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>
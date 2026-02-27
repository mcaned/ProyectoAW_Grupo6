<header style="background-color: #333; color: white; padding: 40px; position: relative; text-align: center;">
    <div style="position: absolute; left: 40px; top: 50%; transform: translateY(-50%);">
        <img src="img/logo.png" style="width: 280px;">
    </div>
    <h1>BISTRO FDI</h1>
    <h2 style="font-style: italic; color: #ccc;">¿Te gusta el pisto?</h2>
    <div style="position: absolute; bottom: 10px; right: 20px;">
        <?php if (isset($_SESSION['login'])): ?>
            Bienvenido, <?= $_SESSION['nombre'] ?> | <a href="logout.php" style="color:white;">Salir</a>
        <?php else: ?>
            <a href="login.php" style="color:white;">Login</a> | <a href="registro.php" style="color:white;">Registro</a>
        <?php endif; ?>
    </div>
</header>
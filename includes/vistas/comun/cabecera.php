<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= RUTA_CSS ?>style.css">
    <title>Bistro FDI</title>
</head>
<body>
    <header style="background-color: #333; color: white; padding: 45px 20px; position: relative; text-align: center;">
        
        <div style="position: absolute; left: 40px; top: 50%; transform: translateY(-50%);">
            <a href="index.php">
                <img src="img/logo.png" alt="Logo Bistro FDI" style="width: 280px; height: auto;">
            </a>
        </div>

        <div style="display: inline-block;">
            <h1 style="margin: 0; font-family: serif; font-size: 2.5rem;">BISTRO FDI</h1>
            <h2 style="margin: 0; font-family: serif; font-size: 1.5rem; font-weight: normal; color: #ccc; font-style: italic;">
                ¿Te gusta el pisto?
            </h2>
        </div>
        
        <div style="position: absolute; bottom: 10px; right: 20px; font-size: 0.9rem;">
            <?php if (isset($_SESSION['login']) && $_SESSION['login']): ?>
                Bienvenido, <strong><?= $_SESSION['nombre'] ?></strong>. 
                <a href="logout.php" style="color: white; font-weight: bold; text-decoration: none; margin-left: 10px; border: 1px solid white; padding: 2px 5px;">Salir</a>
            <?php else: ?>
                Usuario desconocido. 
                <a href="login.php" style="color: white; font-weight: bold; text-decoration: none;">Login</a> | 
                <a href="registro.php" style="color: white; font-weight: bold; text-decoration: none;">Registro</a>
            <?php endif; ?>
        </div>
    </header>
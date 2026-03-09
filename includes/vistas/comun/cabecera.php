<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bistro FDI</title>
    <link rel="stylesheet" type="text/css" href="<?= RUTA_APP ?>/css/estilos.css">
</head>
<body>
    
<header class="cabecera-bistro">
    <div class="seccion-cabecera logo-cabecera">
        <img src="<?= RUTA_APP ?>/img/logo.png" alt="Logo">
    </div>

    <div class="seccion-cabecera titulos-cabecera">
        <h1>BISTRO FDI</h1>
        <h2>¿Te gusta el pisto?</h2>
    </div>

    <div class="seccion-cabecera enlaces-cabecera">
        <?php if (isset($_SESSION['login'])): ?> 
            Bienvenido, <?= htmlspecialchars($_SESSION['nombre'])?> | 
            <a href="<?= RUTA_APP ?>/logout.php">Salir</a>
        <?php else: ?>
            <a href="<?= RUTA_APP ?>/login.php">Login</a> | 
            <a href="<?= RUTA_APP ?>/registro.php">Registro</a>
        <?php endif; ?>
    </div>
</header>
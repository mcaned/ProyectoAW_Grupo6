<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Aplicacion.php';

include 'includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    
    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <h1>Menú Exclusivo del Bistro</h1>
        <?php if (isset($_SESSION['login'])): ?>
            <p>Hola <?= $_SESSION['nombre'] ?>, aquí tienes nuestras especialidades del día:</p>
            <ul>
                <li>Pisto Manchego Tradicional - 8€</li>
                <li>Croquetas de la Abuela - 6€</li>
            </ul>
        <?php else: ?>
            <h2 style="color: red;">Acceso Denegado</h2>
            <p>Debes <a href="login.php">iniciar sesión</a> para ver nuestra carta completa.</p>
        <?php endif; ?>
    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
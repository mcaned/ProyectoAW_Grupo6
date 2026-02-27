<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Aplicacion.php';
require_once __DIR__ . '/includes/Usuario.php';

$app = Aplicacion::getInstance(); $app->init();
include 'includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <?php if (isset($_SESSION['login'])): ?>
            <h1>Hola, <?= $_SESSION['nombre'] ?></h1>
            <p>Bienvenido al sistema del Bistro FDI.</p>
        <?php else: ?>
            <h1>Bienvenido</h1>
            <p>Por favor, identifícate para acceder a las funciones.</p>
        <?php endif; ?>
    </main>
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
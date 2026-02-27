<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php'); exit();
}

include 'includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; min-height: 85vh;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main style="flex-grow: 1; padding: 40px;">
        <h1>Panel de Administración</h1>
        <p>Solo visible para administradores.</p>
    </main>
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
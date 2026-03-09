<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

include 'includes/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main class="contenido-central">
        <h1>⚙️ Panel de Administración</h1>
        <div class="contenedor-enlaces-admin">
            <a href="gestion_categorias.php" class="btn-panel-admin">📁 Gestionar Categorías</a>
            <a href="gestion_productos.php" class="btn-panel-admin">🍔 Gestionar Productos</a>
            <a href="pedidos_globales.php" class="btn-panel-admin">📋 Ver Pedidos Globales</a>
        </div>
    </main>
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
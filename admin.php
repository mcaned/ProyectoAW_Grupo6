<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

include 'includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; min-height: 85vh;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main style="flex-grow: 1; padding: 40px;">
        <h1>⚙️ Panel de Administración</h1>
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <a href="gestion_categorias.php" style="padding: 20px; background: #333; color: white; text-decoration: none; border-radius: 10px;">📁 Gestionar Categorías</a>
            <a href="gestion_productos.php" style="padding: 20px; background: #333; color: white; text-decoration: none; border-radius: 10px;">🍔 Gestionar Productos</a>
            <a href="pedidos_globales.php" style="padding: 20px; background: #333; color: white; text-decoration: none; border-radius: 10px;">📋 Ver Pedidos Globales</a>
        </div>
    </main>
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>


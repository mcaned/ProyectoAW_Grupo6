<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

include 'includes/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main class="contenido-central">
        <?php if (isset($_SESSION['mensaje_perfil'])): ?>
            <div class="alerta-exito"><?= $_SESSION['mensaje_perfil'] ?></div>
            <?php unset($_SESSION['mensaje_perfil']); ?>
        <?php endif; ?>
        <h1>⚙️ Panel de Administración</h1>
        <div class="contenedor-enlaces-admin">
            <a href="includes/gestion_categorias.php" class="btn-panel-admin">📁 Gestionar Categorías</a>
            <a href="includes/gestion_productos.php" class="btn-panel-admin">🍔 Gestionar Productos</a>
            <a href="includes/pedidos_globales.php" class="btn-panel-admin">📋 Ver Pedidos Globales</a>
        </div>
    </main>
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
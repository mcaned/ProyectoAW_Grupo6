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
    
    <main class="contenido-central">
        <?php if (isset($_SESSION['mensaje_perfil'])): ?>
            <div class="alerta-exito">
                <span class="check-icon">✓</span>
                <?= $_SESSION['mensaje_perfil'] ?>
            </div>
            <?php unset($_SESSION['mensaje_perfil']); ?>
        <?php endif; ?>

        <h1 class="texto-centrado">⚙️ Panel de Administración</h1>
        
        <ul class="lista-admin">
            <li class="item-admin">
                <a href="includes/gestion_categorias.php" class="enlace-admin">📁 Gestionar Categorías</a>
            </li>
            <li class="item-admin">
                <a href="includes/gestion_productos.php" class="enlace-admin">🍔 Gestionar Productos</a>
            </li>
            <li class="item-admin">
                <a href="includes/pedidos_globales.php" class="enlace-admin">📋 Ver Pedidos Globales</a>
            </li>
            <li class="item-admin">
                <a href="includes/gestion_usuarios.php" class="enlace-admin">👥 Gestionar Usuarios</a>
            </li>
        </ul>
    </main>
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
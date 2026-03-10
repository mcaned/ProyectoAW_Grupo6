<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
require_once __DIR__ . '/includes/clases/usuario.php';

$app = Aplicacion::getInstance(); $app->init();
include 'includes/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    
    <main class="contenido-central">
        <?php if (isset($_SESSION['mensaje_perfil'])): ?>
            <div class="alerta-exito"><?= $_SESSION['mensaje_perfil'] ?></div>
            <?php unset($_SESSION['mensaje_perfil']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['login'])): ?>
            <h1>Hola, <?= $_SESSION['nombre'] ?></h1>
            <p>Bienvenido al sistema del Bistro FDI.</p>
            
            <div class="bloque-bienvenida">
                <h3>¿Tienes hambre?</h3>
                <a href="includes/carta.php" class="btn-oscuro">Ver la Carta y Pedir</a>
            </div>

        <?php else: ?>
            <h1>Bienvenido</h1>
            <p>Por favor, identifícate para acceder a las funciones.</p>
            <a href="login.php" class="btn-oscuro">Ir al Login</a>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
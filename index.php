<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
require_once __DIR__ . '/includes/usuario.php';

$app = Aplicacion::getInstance(); $app->init();
include 'includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    
    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <?php if (isset($_SESSION['login'])): ?>
            <h1>Hola, <?= $_SESSION['nombre'] ?></h1>
            <p>Bienvenido al sistema del Bistro FDI.</p>
            
            <div style="margin-top: 30px; padding: 20px; border: 1px solid #eee; border-radius: 10px; text-align: center; background-color: #f9f9f9;">
                <h3>¿Tienes hambre?</h3>
           <a href="includes/carta.php" style="display: inline-block; background-color: #d32f2f; color: white; padding: 15px 30px; text-decoration: none; font-weight: bold; border-radius: 5px; font-size: 1.2rem;">
                 Ver la Carta y Pedir
            </a>
            </div>

        <?php else: ?>
            <h1>Bienvenido</h1>
            <p>Por favor, identifícate para acceder a las funciones.</p>
            <a href="login.php" style="display: inline-block; background-color: #333; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Ir al Login
            </a>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
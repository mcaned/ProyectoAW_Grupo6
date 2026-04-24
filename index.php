<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
require_once __DIR__ . '/includes/clases/usuarios/usuario.php';

$app = Aplicacion::getInstance(); $app->init();
include 'includes/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    
    <main class="contenido-central texto-centrado">
        <?php if (isset($_SESSION['mensaje_perfil'])): ?>
            <div class="alerta-exito"><?= $_SESSION['mensaje_perfil'] ?></div>
            <?php unset($_SESSION['mensaje_perfil']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['login'])): ?>
            <div >
                <a href="includes/carta.php" class="btn">Ver la Carta y Pedir</a>
            </div>

            <div class="texto-centrado margen-superior">
                <img alt src="img/4.jpeg">
            </div>
            
            

        <?php else: ?>
            <h1>Bienvenid@</h1>
            <p>Por favor, identifícate para acceder a las funciones.</p>
            <a href="login.php" class="btn">Ir al Login</a>
        <?php endif; ?>
    </main>
    
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
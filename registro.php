<?php
require_once __DIR__ . '/includes/config.php'; 
require_once __DIR__ . '/includes/Aplicacion.php';
require_once __DIR__ . '/includes/FormularioRegistro.php';

include 'includes/vistas/comun/cabecera.php'; 
?>

<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h2 class="titulo-serif">Crear cuenta en Bistro FDI</h2>
        
        <?php
            $form = new FormularioRegistro();
            echo $form->gestiona();
        ?>

        <p class="margen-superior">¿Ya tienes cuenta? <a href="login.php">Accede aquí</a>.</p>
    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'includes/vistas/comun/pie.php'; ?>
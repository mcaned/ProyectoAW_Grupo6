<?php

require_once __DIR__ . '/includes/config.php'; 
require_once __DIR__ . '/includes/Aplicacion.php';
require_once __DIR__ . '/includes/FormularioRegistro.php';


include 'includes/vistas/comun/cabecera.php'; 
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh; font-family: sans-serif;">

    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px; border-left: 1px solid #ccc; border-right: 1px solid #ccc;">
        
        <h2 style="font-family: serif; font-size: 1.8rem; margin-top: 0;">Crear cuenta en Bistro FDI</h2>
        
        <?php
          
            $form = new FormularioRegistro();
            echo $form->gestiona();
        ?>

        <p style="margin-top: 20px;">¿Ya tienes cuenta? <a href="login.php">Accede aquí</a>.</p>

    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>

</div>

<?php 
include 'includes/vistas/comun/pie.php'; 
?>
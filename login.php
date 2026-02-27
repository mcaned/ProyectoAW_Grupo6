<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/FormularioLogin.php';

$tituloPagina = 'Login - Bistro FDI';

$form = new FormularioLogin();
$htmlFormulario = $form->gestiona();

include __DIR__ . '/includes/vistas/comun/cabecera.php';
?>

<div style="display: flex; min-height: 80vh;">
    <?php include __DIR__ . '/includes/vistas/comun/sidebarIzq.php'; ?>

    <main style="flex-grow: 1; padding: 40px; background: white;">
        <h1>Acceso al sistema</h1>
        <?= $htmlFormulario ?>
    </main>

    <?php include __DIR__ . '/includes/vistas/comun/sidebarDer.php'; ?>
</div>

<?php 
include __DIR__ . '/includes/vistas/comun/pie.php'; 
?>
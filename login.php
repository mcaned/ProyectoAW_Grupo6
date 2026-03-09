<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/Aplicacion.php';
require_once __DIR__ . '/includes/FormularioLogin.php';

$app = Aplicacion::getInstance(); 
$app->init();

$tituloPagina = 'Login - Bistro FDI';
$form = new FormularioLogin();
$htmlFormulario = $form->gestiona();

include __DIR__ . '/includes/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include __DIR__ . '/includes/vistas/comun/sidebarIzq.php'; ?> 
    <main class="contenido-central">
        <h1>Acceso al sistema</h1>
        <?= $htmlFormulario ?>
    </main>
    <?php include __DIR__ . '/includes/vistas/comun/sidebarDer.php'; ?> 
</div>
<?php include __DIR__ . '/includes/vistas/comun/pie.php'; ?>
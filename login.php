<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
require_once __DIR__ . '/includes/clases/formularioLogin.php';

$app = Aplicacion::getInstance(); 
$app->init();

$tituloPagina = 'Login - Bistro FDI';
$form = new FormularioLogin();
$htmlFormulario = $form->gestiona();

include __DIR__ . '/includes/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include __DIR__ . '/includes/vistas/comun/sideBarIzq.php'; ?> 
    <main class="contenido-central">
        <h1>Acceso al sistema</h1>
        <?= $htmlFormulario ?>
    </main>
    <?php include __DIR__ . '/includes/vistas/comun/sideBarDer.php'; ?> 
</div>
<?php include __DIR__ . '/includes/vistas/comun/pie.php'; ?>
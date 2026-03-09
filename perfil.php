<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
require_once __DIR__ . '/includes/formularioPerfil.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$tituloPagina = 'Mi Perfil - Bistro FDI';
include __DIR__ . '/includes/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include __DIR__ . '/includes/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h1 class="texto-centrado margen-inferior-grande">Mi Perfil</h1>
        
        <?php
            $form = new FormularioPerfil();
            echo $form->gestiona();
        ?>
    </main>

    <?php include __DIR__ . '/includes/vistas/comun/sideBarDer.php'; ?>
</div>


<?php include __DIR__ . '/includes/vistas/comun/pie.php'; ?>

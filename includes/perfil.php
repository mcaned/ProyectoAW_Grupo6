<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/formularioPerfil.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$tituloPagina = 'Mi Perfil - Bistro FDI';
include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h1 class="margen-inferior-grande">Mi Perfil</h1>
        
        <?php
            $form = new FormularioPerfil();
            echo $form->gestiona();
        ?>
    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
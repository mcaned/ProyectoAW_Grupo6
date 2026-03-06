<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
require_once __DIR__ . '/includes/FormularioPerfil.php';

$app = Aplicacion::getInstance();
$app->init();

// Si el usuario no está logueado, lo redirigimos al login
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$tituloPagina = 'Mi Perfil - Bistro FDI';

include __DIR__ . '/includes/vistas/comun/cabecera.php';
?>

<div style="display: flex; min-height: 85vh; background-color: #e0e0e0;">
    <?php include __DIR__ . '/includes/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; padding: 40px; background: white;">
        <h1 style="text-align: center; margin-bottom: 30px;">Mi Perfil</h1>
        
        <?php
            // Instanciamos y renderizamos el formulario de perfil
            $form = new FormularioPerfil();
            echo $form->gestiona();
        ?>
    </main>

    <?php include __DIR__ . '/includes/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/includes/vistas/comun/pie.php'; ?>
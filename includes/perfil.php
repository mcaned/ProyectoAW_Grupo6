<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/usuarios/formularioPerfil.php';

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

    <main class="contenido-central ">
        <h1 class = "texto-centrado">Mi Perfil</h1>
        <?php if ($_SESSION['rol'] === 'camarero'): ?>
            
            <a href="<?= RUTA_APP ?>/gestion_pedidos.php" class="btn"> ATRAS</a>
           
        <?php endif; ?>
         <?php if ($_SESSION['rol'] === 'gerente'): ?>
            
            <a href="<?= RUTA_APP ?> /admin.php" class="btn"> ATRAS</a>
           
        <?php endif; ?>
        <?php if ($_SESSION['rol'] === 'cocinero'): ?>
            
            <a href="<?= RUTA_APP ?> /cocina.php" class="btn"> ATRAS</a>
           
        <?php endif; ?>
        <?php if ($_SESSION['rol'] === 'cliente'): ?>
            
            <a href="<?= RUTA_APP ?>/index.php" class="btn">ATRAS</a>
           
        <?php endif; ?>


        <?php
            $form = new FormularioPerfil();
            echo $form->gestiona();
        ?>
    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>

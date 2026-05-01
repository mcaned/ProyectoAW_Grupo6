<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/usuarios/formularioOferta.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); 
    exit();
}

$id = $_GET['id'] ?? null;

include 'vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">  

    <main class="contenido-central">
        <h1><?= $id ? '📝 Editar Oferta' : '➕ Crear Nueva Oferta' ?></h1>
        
        <?php
            $form = new FormularioOferta($id);
            echo $form->gestiona();
        ?>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'vistas/comun/pie.php'; ?>
<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/usuarios/formularioCategoria.php'; 

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: ../index.php'); 
    exit();
}

$id = $_GET['id'] ?? null;

include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">

    <main class="contenido-central">
        <h1><?= $id ? '📝 Editar Categoría' : '➕ Crear Nueva Categoría' ?></h1>
        <hr><br>
        
        <?php
            // Instanciamos y gestionamos el formulario
            $form = new formularioCategoria($id);
            echo $form->gestiona();
        ?>
    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
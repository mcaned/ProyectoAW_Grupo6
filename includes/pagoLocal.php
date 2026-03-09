<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();
include 'vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h1>Confirmación de Pedido en Local</h1>
        <p>Has seleccionado consumir en el <strong>Bistro FDI</strong>.</p>
        
        <form action="finalizarProceso.php" method="POST">
            <p>Por favor, indica tu mesa:</p>
            <input type="number" name="mesa" min="1" max="50" class="input-formulario" >
            <br><br>
            <button type="submit" class="btn-oscuro" >
                Confirmar y enviar a cocina
            </button>
            
        </form> 
    </main>
</div>

<?php include 'vistas/comun/pie.php'; ?>
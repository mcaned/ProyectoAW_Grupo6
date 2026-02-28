<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();
include 'vistas/comun/cabecera.php';
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <h1>Confirmación de Pedido en Local</h1>
        <p>Has seleccionado consumir en el <strong>Bistro FDI</strong>.</p>
        
        <form action="finalizarProceso.php" method="POST">
            <p>Por favor, indica tu mesa:</p>
            <input type="number" name="mesa" min="1" max="50" required style="padding: 8px; width: 60px;">
            <br><br>
            <button type="submit" style="background: #333; color: white; padding: 10px 25px; border: none; cursor: pointer;">
                Confirmar y enviar a cocina
            </button>
        </form>
    </main>
</div>

<?php include 'vistas/comun/pie.php'; ?>
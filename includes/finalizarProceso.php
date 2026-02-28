<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

// 1. Verificación: Si no hay pedido pendiente, volve_mos al inicio
if (!isset($_SESSION['ultimo_pedido'])) {
    header('Location: ../index.php');
    exit();
}

$conn = $app->conexionBd();
$id_pedido = $_SESSION['ultimo_pedido'];

// 2. Aquí podrías guardar datos extra (como la mesa o la dirección) si fuera necesario
// Por ahora, simplemente confirmamos que el proceso ha terminado con éxito.

// 3. Vaciamos el carrito y el ID del pedido temporal
unset($_SESSION['carrito']);
unset($_SESSION['ultimo_pedido']);

include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px; text-align: center;">
        <div style="background: #d4edda; color: #155724; padding: 30px; border-radius: 8px; border: 1px solid #c3e6cb;">
            <h1 style="font-size: 3rem;">¡Gracias por tu pedido!</h1>
            <p style="font-size: 1.2rem;">Tu pedido <?= $id_pedido ?> ha sido enviado a cocina.</p>
            <p>Puedes consultar el estado de tu pedido en tu perfil de usuario.</p>
            
            <div style="margin-top: 30px;">
                <a href="../index.php" style="background: #333; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Volver al Inicio</a>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
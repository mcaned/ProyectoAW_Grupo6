<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['ultimo_pedido'])) {
    header('Location: ../index.php');
    exit();
}

$conn = $app->conexionBd();
$id_pedido = $_SESSION['ultimo_pedido'];

unset($_SESSION['carrito']);
unset($_SESSION['ultimo_pedido']);

include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central texto-centrado">
        <div>
            <h1 class="titulo-serif" s>¡Gracias por tu pedido!</h1>
            
            <p>
                Tu pedido <strong>#<?= htmlspecialchars($id_pedido) ?></strong> ha sido enviado a cocina.
            </p>
            
            <p class="texto-ayuda">
                Puedes consultar el estado de tu pedido en tu perfil de usuario.
            </p>
            
            <div class="margen-superior-grande">
                <a href="../index.php" class="btn">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
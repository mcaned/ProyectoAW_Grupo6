<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/producto.php';
require_once __DIR__ . '/clases/pedidos.php';

$app = Aplicacion::getInstance();
$app->init(); 

if (!isset($_SESSION['login']) || !isset($_SESSION['idUsuario'])) {
    header('Location: ' . RUTA_APP . '/login.php');
    exit();
}


$id_cliente = $_SESSION['idUsuario']; 
$tipo = $_POST['tipo'] ?? 'Local';

$id_pedido = Pedido::crear($id_cliente, $tipo, $_SESSION['carrito']);


if ($id_pedido) {
    $id_pedido = $conn->insert_id;
    unset($_SESSION['carrito']); 
    $_SESSION['ultimo_pedido'] = $id_pedido;

    $destino = ($tipo === 'Llevar') ? 'pagoDomicilio.php' : 'pagoLocal.php';
    header("Location: $destino");
} else {
    die("Error al procesar el pedido.");
}
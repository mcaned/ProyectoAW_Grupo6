<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/pedidos.php';
require_once __DIR__ . '/clases/lineas_pedido.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'cocinero') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : null;
    $id_usuario = $_SESSION['idUsuario'];
    
    if ($accion === 'tomar_pedido') {
       Pedido::tomarPedido($id_pedido, $id_usuario);
    }
    elseif ($accion === 'alternar_producto') {
        $id_producto = intval($_POST['id_producto']);
        LineaPedido::alternarPreparado($id_pedido, $id_producto);
    }
    elseif ($accion === 'finalizar_pedido') {
        Pedido::finalizarPedido($id_pedido, $id_usuario);
    }

    header('Location: ../cocina.php');
    exit();
}
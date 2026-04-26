<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'cocinero') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = $app->conexionBd();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'tomar_pedido') {
        $id_pedido = intval($_POST['id_pedido']);
        $id_cocinero = $_SESSION['idUsuario'];
        
        $stmt = $conn->prepare("UPDATE Pedidos SET estado = 'Cocinando', id_cocinero = ? WHERE id = ? AND estado = 'En preparación'");
        $stmt->bind_param("ii", $id_cocinero, $id_pedido);
        $stmt->execute();
        $stmt->close();
    }
    elseif ($accion === 'alternar_producto') {
        $id_pedido = intval($_POST['id_pedido']);
        $id_producto = intval($_POST['id_producto']);
        
        $stmt = $conn->prepare("UPDATE Lineas_Pedido SET preparado = NOT preparado WHERE id_pedido = ? AND id_producto = ?");
        $stmt->bind_param("ii", $id_pedido, $id_producto);
        $stmt->execute();
        $stmt->close();
    }
    elseif ($accion === 'finalizar_pedido') {
        $id_pedido = intval($_POST['id_pedido']);
        $id_cocinero = $_SESSION['idUsuario'];
        
        $stmt = $conn->prepare("UPDATE Pedidos SET estado = 'Listo cocina' WHERE id = ? AND id_cocinero = ?");
        $stmt->bind_param("ii", $id_pedido, $id_cocinero);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: ../cocina.php');
    exit();
}
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/pedidos.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || !in_array($_SESSION['rol'], ['camarero', 'gerente', 'cocinero'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = $_POST['id_pedido'];
    $nuevo_estado = $_POST['nuevo_estado'];
    
    if (Pedido::actualizarEstado($id_pedido, $nuevo_estado)) {      
        if ($_SESSION['rol'] === 'cocinero') {
            header('Location: ../cocina.php?status=ok');
        } else {
            header('Location: ../gestion_pedidos.php?status=ok');
        }
    } else {
        echo "Error al actualizar: ";
    }
    exit();
}
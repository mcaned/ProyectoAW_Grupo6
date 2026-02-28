<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init(); 

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'] ?? null;
    $cantidad = (int)($_POST['cantidad'] ?? 1);

    if ($id_producto && $cantidad > 0) {
        
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        //si el producto estaba en el carro, sumamos la cantidas, si no estaba, lo creamos
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto] += $cantidad;
        } else {
            $_SESSION['carrito'][$id_producto] = $cantidad;
        }
    }
}

// Redirigimos de vuelta a la carta para que el usuario siga comprando
header('Location: carta.php?status=added');
exit();
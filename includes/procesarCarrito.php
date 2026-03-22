<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init(); 

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'] ?? null;
    $action = $_POST['action'] ?? 'add'; 
    $cantidad = (int)($_POST['cantidad'] ?? 1);

    if ($id_producto) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        switch ($action) {
            case 'add':
                if (isset($_SESSION['carrito'][$id_producto])) {
                    $_SESSION['carrito'][$id_producto] += $cantidad;
                } else {
                    $_SESSION['carrito'][$id_producto] = $cantidad;
                }
                header("Location: carta.php?añadido=1");
                exit();

            case 'update':
                //(asegurando que no sea menor a 1)
                if ($cantidad > 0) {
                    $_SESSION['carrito'][$id_producto] = $cantidad;
                } else {
                    unset($_SESSION['carrito'][$id_producto]);
                }
                header("Location: carrito.php");
                exit();

            case 'remove':
                unset($_SESSION['carrito'][$id_producto]);
                header("Location: carrito.php");
                exit();
        }
    }
}

header("Location: carta.php");
exit();
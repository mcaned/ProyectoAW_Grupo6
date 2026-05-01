<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/producto.php';
require_once __DIR__ . '/clases/ofertas.php';
require_once __DIR__ . '/clases/gestor_ofertas.php';

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
                validarOfertasSesion();
                header("Location: carta.php?añadido=1");
                exit();

            case 'update':
                if ($cantidad > 0) {
                    $_SESSION['carrito'][$id_producto] = $cantidad;
                } else {
                    unset($_SESSION['carrito'][$id_producto]);
                }
                validarOfertasSesion();
                header("Location: carrito.php");
                exit();

            case 'remove':
                unset($_SESSION['carrito'][$id_producto]);
                validarOfertasSesion();
                header("Location: carrito.php");
                exit();
        }
    }
}

function validarOfertasSesion() {
    if (isset($_SESSION['ofertas_seleccionadas']) && !empty($_SESSION['ofertas_seleccionadas'])) {
        $ofertasValidas = [];
       
        $inventarioTemporal = $_SESSION['carrito'] ?? [];

        foreach ($_SESSION['ofertas_seleccionadas'] as $idO) {
            $ahorro = GestorOfertas::calcularAhorro($inventarioTemporal, (int)$idO);
            
            if ($ahorro > 0) {
                // Si la oferta es válida, la mantenemos
                $ofertasValidas[] = $idO;
                
                // restamos  productos usados por oferta
                $ofertaDoc = Oferta::buscaPorId($idO);
                if ($ofertaDoc) {
                    foreach ($ofertaDoc->getProductos() as $item) {
                        $idP = $item->getIdProducto();
                        $cantNecesaria = $item->getCantidad();
                        if (isset($inventarioTemporal[$idP])) {
                            $inventarioTemporal[$idP] -= $cantNecesaria;
                        }
                    }
                }
            }
        }
        $_SESSION['ofertas_seleccionadas'] = $ofertasValidas;
    }
}

header("Location: carta.php");
exit();
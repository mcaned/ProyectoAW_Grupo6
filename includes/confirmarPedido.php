<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init(); 

if (!isset($_SESSION['login']) || !isset($_SESSION['idUsuario'])) {
    header('Location: ' . RUTA_APP . '/login.php');
    exit();
}


$conn = $app->conexionBd();
$id_cliente = $_SESSION['idUsuario']; 
$tipo = $conn->real_escape_string($_POST['tipo'] ?? 'Local');


$total = 0;
foreach ($_SESSION['carrito'] as $id_prod => $cantidad) {
    $rs = $conn->query(sprintf("SELECT precio_base, iva FROM Productos WHERE id=%d", $id_prod));
    if ($f = $rs->fetch_assoc()) {
        $total += ($f['precio_base'] * (1 + (float)$f['iva']/100)) * $cantidad;
    }
}

$hoy = date('Y-m-d');
$sqlNum = "SELECT MAX(numero_pedido) as ultimo FROM Pedidos WHERE DATE(fecha_hora) = '$hoy'";
$resNum = $conn->query($sqlNum);
$filaNum = $resNum->fetch_assoc();
$nuevo_numero_pedido = ($filaNum['ultimo']) ? $filaNum['ultimo'] + 1 : 1;

$queryPedido = sprintf(
    "INSERT INTO Pedidos (numero_pedido, id_cliente, tipo, estado, total) VALUES (%d, %d, '%s', 'Recibido', %s)",
    $nuevo_numero_pedido,
    $id_cliente,
    $tipo,
    number_format($total, 2, '.', '')
);

if ($conn->query($queryPedido)) {
    $id_pedido = $conn->insert_id;

    foreach ($_SESSION['carrito'] as $id_prod => $cantidad) {
        $queryLinea = sprintf(
            "INSERT INTO Lineas_Pedido (id_pedido, id_producto, cantidad) VALUES (%d, %d, %d)",
            $id_pedido,
            $id_prod,
            $cantidad
        );
        $conn->query($queryLinea);
    }

    $_SESSION['ultimo_pedido'] = $id_pedido;

    if ($tipo === 'Llevar') {
        header('Location: pagoDomicilio.php');
    } else {
        header('Location: pagoLocal.php');
    }
    exit();
} else {
    die("Error SQL: " . $conn->error);
}
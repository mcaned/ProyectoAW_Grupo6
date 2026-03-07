<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php'); exit();
}

$conn = $app->conexionBd();
$idPedido = intval($_GET['id']);

// 1. Obtener datos generales del pedido y del cliente
$queryPedido = "SELECT p.*, u.nombre, u.apellidos, u.email 
                FROM Pedidos p 
                JOIN Usuarios u ON p.id_cliente = u.id 
                WHERE p.id = $idPedido";
$resP = $conn->query($queryPedido);
$pedido = $resP->fetch_assoc();

if (!$pedido) { die("Pedido no encontrado."); }

// 2. Obtener los productos (líneas) de ese pedido
$queryItems = "SELECT lp.cantidad, pr.nombre, pr.precio_base, pr.iva 
               FROM Lineas_Pedido lp 
               JOIN Productos pr ON lp.id_producto = pr.id 
               WHERE lp.id_pedido = $idPedido";
$items = $conn->query($queryItems);

include 'includes/vistas/comun/cabecera.php';
?>

<div style="display: flex; min-height: 85vh; background-color: #f0f0f0;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; padding: 40px; background: white; margin: 20px; border-radius: 10px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>📄 Detalle del Pedido #<?= $pedido['numero_pedido'] ?></h1>
            <!-- Botón para volver a la lista de pedidos -->
            <a href="includes/pedido.php" style="background: #666; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">⬅️ Volver a la Lista</a>
        </div>
        <hr>

        <div style="display: flex; gap: 40px; margin-top: 20px;">
            <div style="flex: 1;">
                <h3>Datos del Cliente</h3>
                <p><strong>Nombre:</strong> <?= $pedido['nombre'] ?> <?= $pedido['apellidos'] ?></p>
                <p><strong>Email:</strong> <?= $pedido['email'] ?></p>
            </div>
            <div style="flex: 1;">
                <h3>Datos del Pedido</h3>
                <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_hora'])) ?></p>
                <p><strong>Tipo:</strong> <?= $pedido['tipo'] ?></p>
                <p><strong>Estado:</strong> <span style="background: #333; color: white; padding: 5px 10px; border-radius: 5px;"><?= $pedido['estado'] ?></span></p>
            </div>
        </div>

        <h3 style="margin-top: 40px;">Productos Solicitados</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #eee;">
                    <th style="padding: 10px; text-align: left;">Producto</th>
                    <th style="padding: 10px;">Precio (IVA inc.)</th>
                    <th style="padding: 10px;">Cantidad</th>
                    <th style="padding: 10px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($it = $items->fetch_assoc()): 
                    $p_final = $it['precio_base'] * (1 + $it['iva']/100);
                    $subtotal = $p_final * $it['cantidad'];
                ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;"><?= $it['nombre'] ?></td>
                    <td style="padding: 10px; text-align: center;"><?= number_format($p_final, 2) ?>€</td>
                    <td style="padding: 10px; text-align: center;"><?= $it['cantidad'] ?></td>
                    <td style="padding: 10px; text-align: center;"><strong><?= number_format($subtotal, 2) ?>€</strong></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-top: 30px;">
            <h2 style="color: #d32f2f;">TOTAL PAGADO: <?= number_format($pedido['total'], 2) ?>€</h2>
        </div>
    </main>
</div>

<?php include 'includes/vistas/comun/pie.php'; ?>

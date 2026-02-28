<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$conn = $app->conexionBd();
include 'vistas/comun/cabecera.php';
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <h1>Tu Carrito de Compra</h1>

        <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="border-bottom: 2px solid #333; text-align: left;">
                        <th style="padding: 10px;">Producto</th>
                        <th style="padding: 10px;">Cantidad</th>
                        <th style="padding: 10px;">Precio Base</th>
                        <th style="padding: 10px;">IVA</th>
                        <th style="padding: 10px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_iva_incluido = 0;
                    foreach ($_SESSION['carrito'] as $id_prod => $cantidad):
                        $query = sprintf("SELECT * FROM Productos WHERE id=%d", $id_prod);
                        $rs = $conn->query($query);
                        if ($f = $rs->fetch_assoc()):
                            $precio_base_total = $f['precio_base'] * $cantidad;
                            $cuota_iva = $precio_base_total * ($f['iva'] / 100);
                            $subtotal_con_iva = $precio_base_total + $cuota_iva;
                            $total_iva_incluido += $subtotal_con_iva;
                    ?>
                        <tr style="border-bottom: 1px solid #ccc;">
                            <td style="padding: 10px;"><?= htmlspecialchars($f['nombre']) ?></td>
                            <td style="padding: 10px;"><?= $cantidad ?></td>
                            <td style="padding: 10px;"><?= number_format($f['precio_base'], 2) ?>€</td>
                            <td style="padding: 10px;"><?= $f['iva'] ?>%</td>
                            <td style="padding: 10px;"><?= number_format($subtotal_con_iva, 2) ?>€</td>
                        </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>

            <div style="text-align: right; font-size: 1.2rem;">
                <p><strong>Total a pagar (IVA incluido): <?= number_format($total_iva_incluido, 2) ?>€</strong></p>
            </div>

            <form action="confirmarPedido.php" method="POST" style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px;">
                <h3>Detalles del envío</h3>
                <div style="margin-bottom: 15px;">
                    <label><strong>Tipo de pedido:</strong></label><br>
                    <input type="radio" name="tipo" value="Local" checked> Consumir en el local
                    <input type="radio" name="tipo" value="Llevar" style="margin-left: 20px;"> Para llevar
                </div>
                
                <button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; font-size: 1rem;">
                    Confirmar y Pagar
                </button>
            </form>

        <?php else: ?>
            <p>El carrito está vacío. <a href="carta.php">Vuelve a la carta</a> para añadir productos.</p>
        <?php endif; ?>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'vistas/comun/pie.php'; ?>
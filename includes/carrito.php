<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$conn = $app->conexionBd();
include 'vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h1>Tu Carrito de Compra</h1>

        <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
            <table class="tabla-gestion">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Base</th>
                        <th>IVA</th>
                        <th>Subtotal</th>
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
                        <tr>
                            <td><?= htmlspecialchars($f['nombre']) ?></td>
                            <td><?= $cantidad ?></td>
                            <td><?= number_format($f['precio_base'], 2) ?>€</td>
                            <td><?= $f['iva'] ?>%</td>
                            <td><strong><?= number_format($subtotal_con_iva, 2) ?>€</strong></td>
                        </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>

            <div class="bloque-total-pedido">
                <p>Total a pagar (IVA incluido): <strong><?= number_format($total_iva_incluido, 2) ?>€</strong></p>
            </div>

            <form action="confirmarPedido.php" method="POST" class="acciones-formulario">
                <h3 class="titulo-serif">Detalles del envío</h3>
                <div class="item-barra-izquierda">
                    <label><strong>Tipo de pedido:</strong></label><br>
                    <input type="radio" name="tipo" value="Local" checked> Consumir en el local
                    <input type="radio" name="tipo" value="Llevar"> Para llevar
                </div>
                
                <button type="submit" class="btn-verde">
                    Confirmar y Pagar
                </button>
            </form>

        <?php else: ?>
            <div class="cocina-vacia">
                <p>El carrito está vacío.</p>
                <a href="carta.php" class="enlace-editar">Vuelve a la carta para añadir productos</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'vistas/comun/pie.php'; ?>
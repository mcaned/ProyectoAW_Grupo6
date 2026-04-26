<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php'); exit();
}

$conn = $app->conexionBd();
$idPedido = intval($_GET['id']);


$queryPedido = "SELECT p.*, u.nombre, u.apellidos, u.email  ,
                        c.nombre as nombre_cocinero, c.avatar_url as avatar_cocinero 
                FROM Pedidos p 
                JOIN Usuarios u ON p.id_cliente = u.id 
                LEFT JOIN Usuarios c ON p.id_cocinero = c.id
                WHERE p.id = $idPedido";
$resP = $conn->query($queryPedido);
$pedido = $resP->fetch_assoc();

if (!$pedido) { die("Pedido no encontrado."); }

$queryItems = "SELECT lp.cantidad, pr.nombre, pr.precio_base, pr.iva ,lp.preparado 
               FROM Lineas_Pedido lp 
               JOIN Productos pr ON lp.id_producto = pr.id 
               WHERE lp.id_pedido = $idPedido";
$items = $conn->query($queryItems);

include 'vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>📄 Detalle del Pedido #<?= $pedido['numero_pedido'] ?></h1>
            <?php if ($_SESSION['rol'] == 'gerente'): ?>
                 <a href="pedidos_globales.php" class="btn-atras">⬅️ Volver a la Lista</a>
            <?php else: ?>
                 <a href="pedido.php" class="btn-atras">⬅️ Volver a Mis Pedidos</a>
            <?php endif; ?>
        </div>
        <hr class="separador">

        <div class="contenedor-info-pedido">
            <div class="columna-info">
                <h3>Datos del Cliente</h3>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($pedido['nombre']) ?> <?= htmlspecialchars($pedido['apellidos']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($pedido['email']) ?></p>
            </div>
            <div class="columna-info">
                <h3>Datos del Pedido</h3>
                <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_hora'])) ?></p>
                <p><strong>Tipo:</strong> <?= htmlspecialchars($pedido['tipo']) ?></p>
                <p><strong>Estado:</strong> <span class="etiqueta-estado"><?= htmlspecialchars($pedido['estado']) ?></span></p>

                 <?php if ($pedido['id_cocinero']): ?>
                    <div>
                        <p><strong>👨‍🍳 Cocinero Asignado:</strong></p>
                        <div>
                            <img src="<?= RUTA_APP ?>/img/<?= $pedido['avatar_cocinero'] ?: 'defecto.png' ?>" class="avatar-usuario">
                            <span ><?= htmlspecialchars($pedido['nombre_cocinero']) ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <h3 class="margen-superior-grande">Productos Solicitados</h3>
        <table class="tabla-detalle">
            <thead>
                <tr>
                    <th>Estado Cocina</th>
                    <th>Producto</th>
                    <th>Precio (IVA inc.)</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($it = $items->fetch_assoc()): 
                    $p_final = $it['precio_base'] * (1 + $it['iva']/100);
                    $subtotal = $p_final * $it['cantidad'];
                ?>
                <tr>
                    <td class="texto-centrado">
                        <?php if ($pedido['estado'] == 'Cocinando' || $pedido['estado'] == 'Listo cocina' || $pedido['estado'] == 'Terminado' || $pedido['estado'] == 'Entregado'): ?>
                            <?= $it['preparado'] ? '<span>✔ Listo</span>' : '<span>⏳ Pendiente</span>' ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($it['nombre']) ?></td>
                    <td class="texto-centrado"><?= number_format($p_final, 2) ?>€</td>
                    <td class="texto-centrado"><?= $it['cantidad'] ?></td>
                    <td class="texto-centrado"><strong><?= number_format($subtotal, 2) ?>€</strong></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="bloque-total-pedido">
            <h2 class="texto-rojo">TOTAL PAGADO: <?= number_format($pedido['total'], 2) ?>€</h2>
        </div>
         
    </main>
    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>
<?php 
$resP->free();
$items->free();
include 'vistas/comun/pie.php'; ?>
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/producto.php';
require_once __DIR__ . '/clases/pedidos.php';
require_once __DIR__ . '/clases/lineas_pedido.php';
require_once __DIR__ . '/clases/usuarios/usuario.php';

$app = Aplicacion::getInstance(); 
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php'); exit();
}


$idPedido = intval($_GET['id']);
$pedido = Pedido::buscaPorId($idPedido);

if (!$pedido) { die("Pedido no encontrado."); }

$cliente = $pedido->getCliente();
$cocinero = $pedido->getCocinero();
$lineas = $pedido->getLineas();


include 'vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>📄 Detalle del Pedido #<?= $pedido->getNumpedido() ?></h1>
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
                <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente->getNombre() . " " . $cliente->getApellidos()) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($cliente->getEmail()) ?></p>
            </div>
            <div class="columna-info">
                <h3>Datos del Pedido</h3>
                <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido->getfechahora())) ?></p>
                <p><strong>Tipo:</strong> <?= htmlspecialchars($pedido->getTipo()) ?></p>
                <p><strong>Estado:</strong> <span class="etiqueta-estado"><?= htmlspecialchars($pedido->getEstado()) ?></span></p>

                 <?php if ($cocinero): ?>
                    <div>
                        <p><strong>👨‍🍳 Cocinero Asignado:</strong></p>
                        <div>
                            <img src="<?= RUTA_APP ?>/img/<?= $cocinero->getAvatar() ?: 'defecto.png' ?>" class="avatar-usuario">
                            <span><?= htmlspecialchars($cocinero->getNombre()) ?></span>
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
                <?php foreach ($lineas as $linea): 
                    $producto = $linea->getProducto();
                    $p_final = $producto->getPrecioFinal();
                    $subtotal = $p_final * $linea->getCantidad();
                ?>
                <tr>
                    <td class="texto-centrado">
                        <?php if (in_array($pedido->getEstado(), ['Cocinando', 'Listo cocina', 'Terminado', 'Entregado'])): ?>
                            <?= $linea->estaPreparado() ? '<span>✔ Listo</span>' : '<span>⏳ Pendiente</span>' ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($producto->getNombre()) ?></td>
                    <td class="texto-centrado"><?= number_format($p_final, 2) ?>€</td>
                    <td class="texto-centrado"><?= $linea->getCantidad() ?></td>
                    <td class="texto-centrado"><strong><?= number_format($subtotal, 2) ?>€</strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="bloque-total-pedido">
            <h2 class="texto-rojo">TOTAL PAGADO: <?= number_format($pedido->getTotal(), 2) ?>€</h2>
        </div>
         
    </main>
    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>
<?php 
include 'vistas/comun/pie.php'; ?>
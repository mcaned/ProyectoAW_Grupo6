<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
require_once __DIR__ . '/includes/clases/producto.php';
require_once __DIR__ . '/includes/clases/pedidos.php';
require_once __DIR__ . '/includes/clases/lineas_pedido.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'cocinero') {
    header('Location: index.php');
    exit();
}
$id_mi_usuario = (int)$_SESSION['idUsuario'];
$pedidosPendientes = Pedido::buscarPedidosPorEstado('En preparación');
$pedidosMios = Pedido::buscarPedidosPorEstado('Cocinando', $id_mi_usuario); 

include 'includes/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">

    <main class="contenido-central flex-col">
        <div class="cabecera-seccion-flexible">
            <h1 class="titulo-serif">👨‍🍳 PANEL DE COCINA</h1>
        </div>
        <hr class="separador">

        <h2 class="texto-rojo">🔥 Mis Fogones (Cocinando)</h2>
        <div class="cuadricula-pedidos">
            <?php if (!empty($pedidosMios)): ?>
                <?php foreach ($pedidosMios as $pedido): ?>
                    <div class="tarjeta">
                        <div class="info-tarjeta">
                            <h2 class="id-pedido">#<?= $pedido->getNumpedido() ?></h2>
                            <span class="tipo-pedido"><?= strtoupper($pedido->getTipo()) ?></span>
                        </div>

                        <div class="lista-productos-cocina">
                            <ul>
                                <?php 
                                $lineas = $pedido->getLineas();
                                $todos_preparados = true;

                                foreach ($lineas as $linea): 
                                    $producto = $linea->getProducto();
                                    if (!$linea->estaPreparado()) $todos_preparados = false;
                                ?>
                                    <li>
                                        <span>
                                            <strong><?= $linea->getCantidad() ?>x</strong> <?= htmlspecialchars($producto->getNombre()) ?>
                                        </span>
                                        <form action="includes/procesar_cocina.php" method="POST">
                                            <input type="hidden" name="accion" value="alternar_producto">
                                            <input type="hidden" name="id_pedido" value="<?= $pedido->getId() ?>">
                                            <input type="hidden" name="id_producto" value="<?= $producto->getId() ?>">
                                            <button type="submit" class="btn">
                                                <?= $linea->estaPreparado() ? 'Deshacer' : '✔ Listo' ?>
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <form action="includes/procesar_cocina.php" method="POST" class="margen-superior">
                            <input type="hidden" name="accion" value="finalizar_pedido">
                            <input type="hidden" name="id_pedido" value="<?= $pedido->getId() ?>">
                            <button type="submit" class="btn-listo" <?= !$todos_preparados ? 'onclick="return confirm(\'Faltan platos. ¿Seguro?\')"' : '' ?>>
                                🛎️ FINALIZAR PEDIDO
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="cocina-vacia">
                    <h3>No tienes pedidos activos.</h3>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="margen-superior">📥 Pedidos a la espera</h2>
        <div class="cuadricula-pedidos">
            <?php if (!empty($pedidosPendientes)): ?>
                <?php foreach ($pedidosPendientes as $pedido): ?>
                    <div class="tarjeta">
                        <div class="info-tarjeta">
                            <h2 class="id-pedido">#<?= $pedido->getNumpedido() ?></h2>
                            <span class="tipo-pedido"><?= strtoupper($pedido->getTipo()) ?></span></div>
                        <form action="<?= RUTA_APP ?>/includes/procesar_cocina.php"  method="POST" class="margen-superior">
                            <input type="hidden" name="accion" value="tomar_pedido">
                            <input type="hidden" name="id_pedido" value="<?= $pedido->getId() ?>">
                            <button type="submit" class="btn-oscuro">
                                👨‍🍳 TOMAR PEDIDO
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="cocina-vacia">
                    <h3>No hay pedidos a la espera.</h3>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'includes/vistas/comun/pie.php'; ?>
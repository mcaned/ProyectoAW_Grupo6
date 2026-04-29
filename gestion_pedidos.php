<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
require_once __DIR__ . '/includes/clases/pedidos.php';
$app = Aplicacion::getInstance(); 
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}
$pedidos = Pedido::buscarPedidosEnGestion();

include 'includes/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <main class="contenido-central flex-col">
        <?php if (isset($_SESSION['mensaje_perfil'])): ?>
            <div class="alerta-exito">
                <span class="check-icon">✓</span>
                <?= $_SESSION['mensaje_perfil'] ?>
            </div>
            <?php unset($_SESSION['mensaje_perfil']); ?>
        <?php endif; ?>

        <h1 class="texto-centrado titulo-serif">Gestión de Pedidos</h1>

        <div class="contenedor-columnas">
            <section class="columna col-pendiente">
                <h2 class="texto-centrado">🔴 PENDIENTE PAGO</h2>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p->getEstado() === 'Recibido'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p->getId() ?></strong>
                                <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST" style="margin:0;">
                                    <input type="hidden" name="id_pedido" value="<?= $p->getId() ?>">
                                    <input type="hidden" name="nuevo_estado" value="En preparación">
                                    <button type="submit" class="btn">COBRAR</button>
                                </form>
                            </div>
                            <p>Total: <strong><?= number_format($p->getTotal(), 2) ?>€</strong></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-cocina">
                <h3 class="texto-centrado">⚪ EN COCINA</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p->getEstado() === 'En preparación'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p->getId() ?></strong>
                            </div>
                            <p class="texto-ayuda">👨‍🍳 En preparación...</p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-extras">
                <h3 class="texto-centrado">🟠 REVISAR EXTRAS</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p->getEstado() === 'Listo cocina'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p->getId() ?></strong>
                            </div>
                            <div class="margen-superior">
                                <label><input type="checkbox" required> Complementos incluidos</label>
                            </div>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST" class="margen-superior">
                                <input type="hidden" name="id_pedido" value="<?= $p->getId() ?>">
                                <input type="hidden" name="nuevo_estado" value="Terminado">
                                <button type="submit" class="btn">LISTO</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-entregar">
                <h3 class="texto-centrado">🔵 ENTREGAR</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p->getEstado() === 'Terminado'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p->getId() ?></strong>
                                <span class="tipo-pedido"><?= $p->getTipo() ?></span>
                            </div>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST" class="margen-superior">
                                <input type="hidden" name="id_pedido" value="<?= $p->getId() ?>">
                                <input type="hidden" name="nuevo_estado" value="Entregado">
                                <button type="submit" class="btn">ENTREGADO</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
        </div>
    </main>
    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>
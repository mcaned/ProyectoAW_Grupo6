<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
$app = Aplicacion::getInstance(); 
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

include 'includes/vistas/comun/cabecera.php';
$conn = $app->conexionBd();

$query = "SELECT * FROM Pedidos WHERE estado IN ('Recibido', 'En preparación', 'Listo cocina', 'Terminado') ORDER BY fecha_hora ASC";
$result = $conn->query($query);
$pedidos = [];
while ($row = $result->fetch_assoc()) {
    $pedidos[] = $row;
}
$result->free();
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
                    <?php if ($p['estado'] === 'Recibido'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p['id'] ?></strong>
                                <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST" style="margin:0;">
                                    <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="nuevo_estado" value="En preparación">
                                    <button type="submit" class="btn">COBRAR</button>
                                </form>
                            </div>
                            <p>Total: <strong><?= number_format($p['total'], 2) ?>€</strong></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-cocina">
                <h3 class="texto-centrado">⚪ EN COCINA</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'En preparación'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p['id'] ?></strong>
                            </div>
                            <p class="texto-ayuda">👨‍🍳 En preparación...</p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-extras">
                <h3 class="texto-centrado">🟠 REVISAR EXTRAS</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'Listo cocina'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p['id'] ?></strong>
                            </div>
                            <div class="margen-superior">
                                <label><input type="checkbox" required> Complementos incluidos</label>
                            </div>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST" class="margen-superior">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
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
                    <?php if ($p['estado'] === 'Terminado'): ?>
                        <div class="tarjeta">
                            <div class="info-tarjeta">
                                <strong class="id-pedido">Pedido #<?= $p['id'] ?></strong>
                                <span class="tipo-pedido"><?= $p['tipo'] ?></span>
                            </div>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST" class="margen-superior">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
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
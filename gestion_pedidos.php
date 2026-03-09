<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

include 'includes/vistas/comun/cabecera.php';
$conn = $app->conexionBd();

$query = "SELECT * FROM pedidos WHERE estado IN ('Recibido', 'En preparación', 'Listo cocina', 'Terminado') ORDER BY fecha_hora ASC";
$result = $conn->query($query);
$pedidos = [];
while ($row = $result->fetch_assoc()) {
    $pedidos[] = $row;
}
?>

<div class="contenedor-principal">
    <main class="contenido-central flex-col">
        <div class="cabecera-seccion-kanban">
            <h1>Gestión de Pedidos</h1>
        </div>

        <div class="contenedor-columnas">
            <section class="columna col-pendiente">
                <h3 class="titulo-columna">🔴 PENDIENTE PAGO</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'Recibido'): ?>
                        <div class="tarjeta-kanban">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            Total: <strong><?= number_format($p['total'], 2) ?>€</strong>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="En preparación">
                                <button type="submit" class="btn-estado-kanban bg-rojo-oscuro">COBRAR</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-cocina">
                <h3 class="titulo-columna">⚪ EN COCINA</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'En preparación'): ?>
                        <div class="tarjeta-kanban">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            <p class="texto-informativo-cocina">👨‍🍳 Los cocineros están trabajando en ello...</p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-extras">
                <h3 class="titulo-columna">🟠 REVISAR EXTRAS</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'Listo cocina'): ?>
                        <div class="tarjeta-kanban">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            <div class="alerta-extras">
                                <label class="label-checkbox"><input type="checkbox" required> Complementos incluidos</label>
                            </div>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="Terminado">
                                <button type="submit" class="btn-estado-kanban bg-naranja">LISTO</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna col-entregar">
                <h3 class="titulo-columna">🔵 ENTREGAR</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'Terminado'): ?>
                        <div class="tarjeta-kanban">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            <small>Tipo: <?= $p['tipo'] ?></small>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="Entregado">
                                <button type="submit" class="btn-estado-kanban bg-azul">ENTREGADO</button>
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
<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'cocinero') {
    header('Location: index.php');
    exit();
}

include 'includes/vistas/comun/cabecera.php';
$conn = $app->conexionBd();
$id_mi_usuario = $_SESSION['idUsuario'];

$queryPendientes = "SELECT * FROM Pedidos WHERE estado = 'En preparación' ORDER BY fecha_hora ASC";
$resPendientes = $conn->query($queryPendientes);

$queryMios = "SELECT * FROM Pedidos WHERE estado = 'Cocinando' AND id_cocinero = $id_mi_usuario ORDER BY fecha_hora ASC";
$resMios = $conn->query($queryMios);
?>

<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central flex-col">
        <div class="cabecera-seccion-flexible">
            <h1 class="titulo-serif">👨‍🍳 PANEL DE COCINA</h1>
        </div>
        <hr class="separador">

        <h2 class="texto-rojo">🔥 Mis Fogones (Cocinando)</h2>
        <div class="cuadricula-pedidos">
            <?php if ($resMios && $resMios->num_rows > 0): ?>
                <?php while ($row = $resMios->fetch_assoc()): ?>
                    <div class="tarjeta" style="border: 2px solid #d32f2f;">
                        <div class="info-tarjeta">
                            <h2 class="id-pedido">#<?= $row['numero_pedido'] ?></h2>
                            <span class="tipo-pedido"><?= strtoupper($row['tipo']) ?></span>
                        </div>

                        <div class="lista-productos-cocina">
                            <ul>
                                <?php 
                                $idPedido = $row['id'];
                                $queryItems = "SELECT lp.cantidad, pr.nombre, pr.id as id_producto, lp.preparado 
                                               FROM Lineas_Pedido lp 
                                               JOIN Productos pr ON lp.id_producto = pr.id 
                                               WHERE lp.id_pedido = $idPedido";
                                $items = $conn->query($queryItems); 
                                $todos_preparados = true;

                                while ($item = $items->fetch_assoc()): 
                                    if (!$item['preparado']) $todos_preparados = false;
                                    $estilo_texto = $item['preparado'] ? 'text-decoration: line-through; color: #aaa;' : 'font-weight: bold;';
                                ?>
                                    <li style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="<?= $estilo_texto ?>">
                                            <strong><?= $item['cantidad'] ?>x</strong> <?= htmlspecialchars($item['nombre']) ?>
                                        </span>
                                        <form action="includes/procesar_cocina.php" method="POST" style="margin:0;">
                                            <input type="hidden" name="accion" value="alternar_producto">
                                            <input type="hidden" name="id_pedido" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="id_producto" value="<?= $item['id_producto'] ?>">
                                            <button type="submit" class="<?= $item['preparado'] ? 'btn-gris' : 'btn-verde' ?>">
                                                <?= $item['preparado'] ? 'Deshacer' : '✔ Listo' ?>
                                            </button>
                                        </form>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>

                        <form action="includes/procesar_cocina.php" method="POST" class="margen-superior">
                            <input type="hidden" name="accion" value="finalizar_pedido">
                            <input type="hidden" name="id_pedido" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn-listo" <?= !$todos_preparados ? 'style="background: #ccc;" onclick="return confirm(\'Faltan platos. ¿Seguro?\')"' : '' ?>>
                                🛎️ FINALIZAR PEDIDO
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No tienes pedidos activos.</p>
            <?php endif; ?>
        </div>

        <h2 class="margen-superior" style="color: #555;">📥 Pedidos a la espera</h2>
        <div class="cuadricula-pedidos">
            <?php if ($resPendientes && $resPendientes->num_rows > 0): ?>
                <?php while ($row = $resPendientes->fetch_assoc()): ?>
                    <div class="tarjeta" style="background: #f9f9f9;">
                        <div class="info-tarjeta">
                            <h2 class="id-pedido">#<?= $row['numero_pedido'] ?></h2>
                            <span class="tipo-pedido"><?= strtoupper($row['tipo']) ?></span>
                        </div>
                        <form action="includes/procesar_cocina.php" method="POST" class="margen-superior">
                            <input type="hidden" name="accion" value="tomar_pedido">
                            <input type="hidden" name="id_pedido" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn-oscuro" style="width: 100%;">
                                👨‍🍳 TOMAR PEDIDO
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
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
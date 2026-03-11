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

// obtener pedidos que están en preparación
$query = "SELECT * FROM Pedidos WHERE estado = 'En preparación' ORDER BY fecha_hora ASC";
$result = $conn->query($query);
?>

<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <?php if (isset($_SESSION['mensaje_perfil'])): ?>
            <div class="alerta-exito"><?= $_SESSION['mensaje_perfil'] ?></div>
            <?php unset($_SESSION['mensaje_perfil']); ?>
        <?php endif; ?>
        <div class="cabecera-seccion-flexible">
            <h1>👨‍🍳 PANEL DE COCINA</h1>
            <div class="estado-servicio">
                <small class="estado-en-linea">EN SERVICIO</small>
            </div>
        </div>

        <div class="cuadricula-pedidos">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="tarjeta">
                        <div class="info-tarjeta">
                            <h2 class="id-pedido">#<?= $row['id'] ?></h2>
                            <span class="tipo-pedido"><?= strtoupper($row['tipo']) ?></span>
                        </div>

                        <div class="lista-productos-cocina">
                            <ul>
                                <?php 
                                $idPedido = $row['id'];
                                $queryItems = "SELECT lp.cantidad, pr.nombre 
                                               FROM Lineas_Pedido lp 
                                               JOIN Productos pr ON lp.id_producto = pr.id 
                                               WHERE lp.id_pedido = $idPedido";
                                $items = $conn->query($queryItems);
                                
                                while ($item = $items->fetch_assoc()): ?>
                                    <li>
                                        <strong><?= $item['cantidad'] ?>x</strong> 
                                        <?= htmlspecialchars($item['nombre']) ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>

                        <form action="includes/actualizarEstado.php" method="POST">
                            <input type="hidden" name="id_pedido" value="<?= $row['id'] ?>">
                            <input type="hidden" name="nuevo_estado" value="Listo cocina">
                            <button type="submit" class="btn-listo">✅ LISTO COCINA!</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="cocina-vacia">
                    <h3>No hay pedidos pendientes en cocina.</h3>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'includes/vistas/comun/pie.php'; ?>
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

$conn = $app->conexionBd();
$query = "SELECT p.*, u.nombre FROM Pedidos p JOIN Usuarios u ON p.id_cliente = u.id ORDER BY p.fecha_hora DESC";
$result = $conn->query($query);

include __DIR__ . '/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>
    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>Gestión Global de Pedidos</h1>
            <a href="../admin.php" class="btn-gris">Volver al Panel</a>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <table class="tabla-gestion">
                <thead>
                    <tr>
                        <th>Nº Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $result->fetch_assoc()):  ?>
                        <tr>
                            <td>#<?= $pedido['numero_pedido'] ?></td>
                            <td><?= htmlspecialchars($pedido['nombre']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['fecha_hora'])) ?></td>
                            <td><?= $pedido['tipo'] ?></td>
                            <td>
                                <span class="badge-estado">
                                    <?= $pedido['estado'] ?>
                                </span>
                            </td>
                            <td><strong><?= number_format($pedido['total'], 2) ?>€</strong></td>
                            <td>
                                <a href="detallePedido.php?id=<?= $pedido['id'] ?>" class="enlace-editar">Ver detalle</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="margen-superior">No hay pedidos globales.</p>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
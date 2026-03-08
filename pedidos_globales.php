<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

$conn = $app->conexionBd();

// CONSULTA: Todos los pedidos de todos los usuarios
$query = "SELECT p.*, u.nombre FROM Pedidos p JOIN Usuarios u ON p.id_cliente = u.id ORDER BY p.fecha_hora DESC";
$result = $conn->query($query);

include __DIR__ . '/includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include __DIR__ . '/includes/vistas/comun/sideBarIzq.php'; ?>
    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Gestión Global de Pedidos</h1>
            <a href="admin.php" style="background: #666; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Volver al Panel</a>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #333; color: white; text-align: left;">
                        <th style="padding: 12px;">Nº Pedido</th>
                        <th style="padding: 12px;">Cliente</th>
                        <th style="padding: 12px;">Fecha</th>
                        <th style="padding: 12px;">Tipo</th>
                        <th style="padding: 12px;">Estado</th>
                        <th style="padding: 12px;">Total</th>
                        <th style="padding: 12px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $result->fetch_assoc()): 
                        $colorEstado = "#666";
                        switch($pedido['estado']) {
                            case 'Recibido': $colorEstado = "#007bff"; break;
                            case 'En preparación': $colorEstado = "#ffc107"; break;
                            case 'Terminado': case 'Entregado': $colorEstado = "#28a745"; break;
                            case 'Cancelado': $colorEstado = "#dc3545"; break;
                        }
                    ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px;">#<?= $pedido['numero_pedido'] ?></td>
                            <td style="padding: 12px;"><?= htmlspecialchars($pedido['nombre']) ?></td>
                            <td style="padding: 12px;"><?= date('d/m/Y H:i', strtotime($pedido['fecha_hora'])) ?></td>
                            <td style="padding: 12px;"><?= $pedido['tipo'] ?></td>
                            <td style="padding: 12px;">
                                <span style="background: <?= $colorEstado ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                    <?= $pedido['estado'] ?>
                                </span>
                            </td>
                            <td style="padding: 12px;"><strong><?= number_format($pedido['total'], 2) ?>€</strong></td>
                            <td style="padding: 12px;">
                                <a href="detallePedido.php?id=<?= $pedido['id'] ?>" style="color: #333; text-decoration: underline;">Ver detalle</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="margin-top: 20px;">No hay pedidos globales.</p>
        <?php endif; ?>
    </main>
</div>
<?php include __DIR__ . '/includes/vistas/comun/pie.php'; ?>

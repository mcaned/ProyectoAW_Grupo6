<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php'); exit();
}

$conn = $app->conexionBd();
$idUsuario = $_SESSION['idUsuario']; // ID de la persona que está mirando ahora mismo

// CONSULTA: Solo mis pedidos (Filtro por id_cliente)
$query = sprintf("SELECT p.*, u.nombre 
                  FROM Pedidos p 
                  JOIN Usuarios u ON p.id_cliente = u.id 
                  WHERE p.id_cliente = %d 
                  ORDER BY p.fecha_hora DESC", $idUsuario);

$result = $conn->query($query);
include __DIR__ . '/vistas/comun/cabecera.php';
?>
<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>
    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <h1>Mis Pedidos Personales</h1>

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
                                <a href="<?= RUTA_APP ?>/detallePedido.php?id=<?= $pedido['id'] ?>" style="color: #333; text-decoration: underline;">Ver detalle</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="margin-top: 20px;">No has realizado ningún pedido aún.</p>
        <?php endif; ?>
    </main>
</div>
<?php include __DIR__ . '/vistas/comun/pie.php'; ?>

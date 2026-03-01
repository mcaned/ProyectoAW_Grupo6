<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$conn = $app->conexionBd();
$rol = $_SESSION['rol'];
$idUsuario = $_SESSION['idUsuario'];

// 1. Definir la consulta según el rol
if ($rol === 'gerente') {
    // El gerente ve todos los pedidos, ordenados por fecha (más recientes primero)
    $query = "SELECT p.*, u.nombreUsuario 
              FROM Pedidos p 
              JOIN Usuarios u ON p.id_cliente = u.id 
              ORDER BY p.fecha_hora DESC";
} else {
    // El cliente solo ve sus propios pedidos
    $query = sprintf("SELECT * FROM Pedidos WHERE id_cliente = %d ORDER BY fecha_hora DESC", $idUsuario);
}

$result = $conn->query($query);

include 'vistas/comun/cabecera.php';
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <h1><?= $rol === 'gerente' ? "Gestión Global de Pedidos" : "Mis Pedidos" ?></h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #333; color: white; text-align: left;">
                        <th style="padding: 12px;">Nº Pedido (Día)</th>
                        <?php if ($rol === 'gerente'): ?><th style="padding: 12px;">Cliente</th><?php endif; ?>
                        <th style="padding: 12px;">Fecha</th>
                        <th style="padding: 12px;">Tipo</th>
                        <th style="padding: 12px;">Estado</th>
                        <th style="padding: 12px;">Total</th>
                        <th style="padding: 12px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $result->fetch_assoc()): 
                        // Color según el estado para facilitar la vista al gerente
                        $colorEstado = "#666";
                        switch($pedido['estado']) {
                            case 'Recibido': $colorEstado = "#007bff"; break;
                            case 'En preparación': $colorEstado = "#ffc107"; break;
                            case 'Terminado': $colorEstado = "#28a745"; break;
                            case 'Entregado': $colorEstado = "#28a745"; break;
                            case 'Cancelado': $colorEstado = "#dc3545"; break;
                        }
                    ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px;">#<?= $pedido['numero_pedido'] ?></td>
                            <?php if ($rol === 'gerente'): ?>
                                <td style="padding: 12px;"><?= htmlspecialchars($pedido['nombreUsuario']) ?></td>
                            <?php endif; ?>
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
            <p style="margin-top: 20px;">No se han encontrado pedidos.</p>
        <?php endif; ?>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'vistas/comun/pie.php'; ?>
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php'); exit();
}

$conn = $app->conexionBd();
$idUsuario = $_SESSION['idUsuario']; 

$query = sprintf("SELECT p.*, u.nombre 
                  FROM Pedidos p 
                  JOIN Usuarios u ON p.id_cliente = u.id 
                  WHERE p.id_cliente = %d 
                  ORDER BY p.fecha_hora DESC", $idUsuario);

$result = $conn->query($query);
include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>
    
    <main class="contenido-central">
        <h1 class = "texto-centrado">Mis Pedidos Personales</h1>

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
                    <?php while ($pedido = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $pedido['numero_pedido'] ?></td>
                            <td><?= htmlspecialchars($pedido['nombre']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido['fecha_hora'])) ?></td>
                            <td><?= htmlspecialchars($pedido['tipo']) ?></td>
                            <td>
                                <span class="etiqueta-estado">
                                    <?= htmlspecialchars($pedido['estado']) ?>
                                </span>
                            </td>
                            <td><strong><?= number_format($pedido['total'], 2) ?>€</strong></td>
                            <td>
                                <a href="detallePedido.php?id=<?= $pedido['id'] ?>" class="enlace-editar">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php $result->free(); ?>
        <?php else: ?>
            <div class="cocina-vacia">
                <p>No has realizado ningún pedido aún.</p>
            </div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
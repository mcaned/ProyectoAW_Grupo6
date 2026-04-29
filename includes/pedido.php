<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/pedidos.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php'); exit();
}

$conn = $app->conexionBd();
$idUsuario = $_SESSION['idUsuario']; 

$pedidos = Pedido::buscaPorUsuario($idUsuario);

include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>
    
    <main class="contenido-central">
        <h1 class = "texto-centrado">Mis Pedidos Personales</h1>

        <?php if (!empty($pedidos)): ?>
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
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= $pedido->getNumpedido() ?></td>
                            <td><?= htmlspecialchars($pedido->getNombreCliente()) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pedido->getFechahora())) ?></td>
                            <td><?= htmlspecialchars($pedido->getTipo()) ?></td>
                            <td>
                                <span class="etiqueta-estado">
                                    <?= htmlspecialchars($pedido->getEstado()) ?>
                                </span>
                            </td>
                            <td><strong><?= number_format($pedido->getTotal(), 2) ?>€</strong></td>
                            <td>
                                <a href="detallePedido.php?id=<?= $pedido->getId() ?>" class="enlace-editar">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="cocina-vacia">
                <p>No has realizado ningún pedido aún.</p>
            </div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
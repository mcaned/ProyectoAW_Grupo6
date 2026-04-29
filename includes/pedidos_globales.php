<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/pedidos.php';

$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

$pedidos = Pedido::listar();

include __DIR__ . '/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>Gestión Global de Pedidos</h1>
            <a href="../admin.php" class="btn-atras">⬅️ Volver al Panel</a>
        </div>

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
                    <?php foreach ($pedidos as $p):  ?>
                        <tr>
                            <td>#<?= $p->getNumpedido() ?></td>
                            <td><?= htmlspecialchars($p->getNombreCliente()) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p->getfechahora())) ?></td>
                            <td><?= htmlspecialchars($p->getTipo()) ?></td>
                            <td>
                                <span>
                                    <?= htmlspecialchars($p->getEstado()) ?>
                                </span>
                            </td>
                            <td><strong><?= number_format($p->getTotal(), 2) ?>€</strong></td>
                            <td>
                                <a href="detallePedido.php?id=<?= $p->getId() ?>" class="enlace-editar">Ver detalle</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="margen-superior">No hay pedidos globales.</p>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>
<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/producto.php';

$app = Aplicacion::getInstance(); $app->init();
$conn = $app->conexionBd();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

if (isset($_GET['retirar'])) {
    if (Producto::borrar($_GET['retirar'])) {
        header('Location: gestion_productos.php?msg=borrado_ok');
    } else {
        header('Location: gestion_productos.php?error=en_pedido');
    }
    exit();
}

$listaProductos = Producto::listar(false);

include 'vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">

    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>🍔 Gestión de Productos</h1>
            <a href="../admin.php" class="btn-atras">⬅️ Volver al Panel</a>
        </div>

        <div class="contenedor-acciones-superior">
            <a href="producto.php" class="btn">+ NUEVO PRODUCTO</a>
        </div>

        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($listaProductos as $p): 
                ?>
                <tr>
                   <td><?= htmlspecialchars($p->getNombre()) ?></td>
                    <td><?= htmlspecialchars($p->getCatNom()) ?></td>
                   
                    <td><strong><?= number_format($p->getPrecioFinal(), 2) ?>€</strong></td>
                    <td>
                        <a href="producto.php?id=<?= $p->getId() ?>" class="enlace-editar">📝 Editar</a> | 
                        <a href="?retirar=<?= $p->getId() ?>" class="enlace-borrar" onclick="return confirm('¿Retirar de la carta?')">🚫 Retirar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
     <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>
<?php 
include 'vistas/comun/pie.php'; ?>
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();
$conn = $app->conexionBd();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

if (isset($_GET['retirar'])) {
    $id = intval($_GET['retirar']);
    $query = "DELETE FROM Productos WHERE id = $id";
    if ($conn->query($query)) {
        header('Location: gestion_productos.php?msg=borrado_ok');
    } else {
        header('Location: gestion_productos.php?error=en_pedido');
    }
    exit();
}

$prods = $conn->query("SELECT p.*, c.nombre as cat_nom FROM Productos p JOIN Categorias c ON p.id_categoria = c.id WHERE p.ofertado = 1");
include 'vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>
    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>🍔 Gestión de Productos</h1>
            <a href="../admin.php" class="btn-gris">⬅️ Volver al Panel</a>
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
                <?php while($p = $prods->fetch_assoc()): 
                    $final = $p['precio_base'] * (1 + $p['iva']/100);
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['cat_nom']) ?></td>
                    <td><strong><?= number_format($final, 2) ?>€</strong></td>
                    <td>
                        <a href="producto.php?id=<?= $p['id'] ?>" class="enlace-editar">📝 Editar</a> | 
                        <a href="?retirar=<?= $p['id'] ?>" class="enlace-borrar" onclick="return confirm('¿Retirar de la carta?')">🚫 Retirar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
     <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'vistas/comun/pie.php'; ?>
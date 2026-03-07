<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();
$conn = $app->conexionBd();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

// --- LÓGICA DE BORRADO REAL (DELETE) ---
if (isset($_GET['retirar'])) {
    $id = intval($_GET['retirar']);
    
    // Cambiamos el UPDATE por un DELETE
    $query = "DELETE FROM Productos WHERE id = $id";
    
    if ($conn->query($query)) {
        header('Location: gestion_productos.php?msg=borrado_ok');
    } else {
        // Si el producto ya se ha vendido en algún pedido, 
        // MySQL te dará un error de "Foreign Key" otra vez.
        header('Location: gestion_productos.php?error=en_pedido');
    }
    exit();
}

$prods = $conn->query("SELECT p.*, c.nombre as cat_nom FROM Productos p JOIN Categorias c ON p.id_categoria = c.id WHERE p.ofertado = 1");

include 'includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; min-height: 85vh;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main style="flex-grow: 1; padding: 40px; background: white;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>🍔 Gestión de Productos</h1>
            <a href="admin.php" style="background: #666; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">⬅️ Volver al Panel</a>
        </div>

        <div style="margin-top: 20px;">
            <a href="form_producto.php" style="background: #333; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">+ NUEVO PRODUCTO</a>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 30px;">
            <tr style="background: #333; color: white;">
                <th style="padding: 12px;">Nombre</th>
                <th style="padding: 12px;">Categoría</th>
                <th style="padding: 12px;">Precio Final</th>
                <th style="padding: 12px;">Acciones</th>
            </tr>
            <?php while($p = $prods->fetch_assoc()): 
                $final = $p['precio_base'] * (1 + $p['iva']/100);
            ?>
            <tr style="border-bottom: 1px solid #ddd; text-align: center;">
                <td style="padding: 12px;"><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['cat_nom']) ?></td>
                <td><strong><?= number_format($final, 2) ?>€</strong></td>
                <td>
                    <a href="form_producto.php?id=<?= $p['id'] ?>" style="color: blue; text-decoration: none;">📝 Editar</a> | 
                    <a href="?retirar=<?= $p['id'] ?>" style="color: red; text-decoration: none;" onclick="return confirm('¿Retirar de la carta?')">🚫 Retirar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </main>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>

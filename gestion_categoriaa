<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();
$conn = $app->conexionBd();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

// --- LÓGICA DE BORRADO CORREGIDA ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // 1. Comprobar si hay productos en esta categoría
    $check = $conn->query("SELECT COUNT(*) as total FROM Productos WHERE id_categoria = $id");
    $res = $check->fetch_assoc();
    
    if ($res['total'] > 0) {
        // Si hay productos, mandamos un error por la URL
        header('Location: gestion_categorias.php?error=tiene_productos');
    } else {
        // Si está vacía, borramos normalmente
        $conn->query("DELETE FROM Categorias WHERE id = $id");
        header('Location: gestion_categorias.php?deleted=1');
    }
    exit();
}

$categorias = $conn->query("SELECT * FROM Categorias");
include 'includes/vistas/comun/cabecera.php';
?>
<div style="display: flex; min-height: 85vh;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main style="flex-grow: 1; padding: 40px; background: white;">
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>📁 Gestión de Categorías</h1>
            <a href="<?= RUTA_APP ?>/admin.php" style="background: #666; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">⬅️ Volver al Panel</a>
        </div>

        <!-- MENSAJE DE ERROR SI TIENE PRODUCTOS -->
        <?php if (isset($_GET['error']) && $_GET['error'] == 'tiene_productos'): ?>
            <div style="background: #ffcccc; color: #cc0000; padding: 15px; border: 1px solid #cc0000; margin-top: 20px; border-radius: 5px;">
                <strong>⚠️ No se puede borrar:</strong> Esta categoría contiene productos. Debes borrar o cambiar la categoría de esos productos antes de eliminarla.
            </div>
        <?php endif; ?>

        <div style="margin-top: 20px;">
            <a href="<?= RUTA_APP ?>/form_categoria.php" style="background: #333; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">+ NUEVA CATEGORÍA</a>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 30px;">
            <tr style="background: #333; color: white;">
                <th style="padding: 12px;">Nombre</th>
                <th style="padding: 12px;">Descripción</th>
                <th style="padding: 12px;">Acciones</th>
            </tr>
            <?php while($c = $categorias->fetch_assoc()): ?>
            <tr style="border-bottom: 1px solid #ddd; text-align: center;">
                <td style="padding: 12px;"><?= htmlspecialchars($c['nombre']) ?></td>
                <td style="padding: 12px;"><?= htmlspecialchars($c['descripcion']) ?></td>
                <td style="padding: 12px;">
                    <a href="<?= RUTA_APP ?>/form_categoria.php?id=<?= $c['id'] ?>" style="color: blue; text-decoration: none;">📝 Editar</a> | 
                    <a href="?delete=<?= $c['id'] ?>" style="color: red; text-decoration: none;" onclick="return confirm('¿Borrar categoría?')">🗑️ Borrar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </main>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>

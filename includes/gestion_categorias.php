<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();
$conn = $app->conexionBd();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $check = $conn->query("SELECT COUNT(*) as total FROM Productos WHERE id_categoria = $id");
    $res = $check->fetch_assoc();
    
    if ($res['total'] > 0) {
        header('Location: gestion_categorias.php?error=tiene_productos');
    } else {
        $conn->query("DELETE FROM Categorias WHERE id = $id");
        header('Location: gestion_categorias.php?deleted=1');
    }
    exit();
}

$categorias = $conn->query("SELECT * FROM Categorias");
include 'vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>
    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>📁 Gestión de Categorías</h1>
            <a href="<?= RUTA_APP ?>/admin.php" class="btn-gris">⬅️ Volver al Panel</a>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'tiene_productos'): ?>
            <div class="alerta-error-critico">
                <strong>⚠️ No se puede borrar:</strong> Esta categoría contiene productos activos.
            </div>
        <?php endif; ?>

        <div class="contenedor-acciones-superior">
            <a href="<?= RUTA_APP ?>/form_categoria.php" class="btn-oscuro">+ NUEVA CATEGORÍA</a>
        </div>
        
        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = $categorias->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                    <td><?= htmlspecialchars($c['descripcion']) ?></td>
                    <td>
                        <a href="form_categoria.php?id=<?= $c['id'] ?>" class="enlace-editar">📝 Editar</a> | 
                        <a href="?delete=<?= $c['id'] ?>" class="enlace-borrar" onclick="return confirm('¿Borrar categoría?')">🗑️ Borrar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
     <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'vistas/comun/pie.php'; ?>
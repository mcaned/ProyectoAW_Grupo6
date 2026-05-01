<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/categorias.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    if (Categoria::borrar($id)) {
        header('Location: gestion_categorias.php?deleted=1');
    } else {
        header('Location: gestion_categorias.php?error=tiene_productos');
    }
    exit();
}

$listaCategorias = Categoria::listarTodas();

include 'vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">

    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>📁 Gestión de Categorías</h1>
            <a href="<?= RUTA_APP ?>/admin.php" class="btn-atras">⬅️ Volver al Panel</a>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'tiene_productos'): ?>
            <div class="alerta-error-critico">
                <strong>⚠️ No se puede borrar:</strong> Esta categoría contiene productos activos.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alerta-exito">✅ Categoría eliminada correctamente.</div>
        <?php endif; ?>

        <div class="contenedor-acciones-superior">
            <a href="categoria.php" class="btn">+ NUEVA CATEGORÍA</a>
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
                <?php if (!empty($listaCategorias)): ?>
                    <?php foreach($listaCategorias as $cat): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($cat->getNombre()) ?></strong></td>
                        <td><?= htmlspecialchars($cat->getDescripcion()) ?></td>
                        <td>
                            <a href="categoria.php?id=<?= $cat->getId() ?>" class="enlace-editar">📝 Editar</a>
                            
                            <a href="?delete=<?= $cat->getId() ?>" 
                               class="enlace-borrar" 
                               onclick="return confirm('¿Estás seguro de borrar la categoría \'<?= addslashes($cat->getNombre()) ?>\'?')">
                               🗑️ Borrar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No hay categorías registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
     <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>
<?php include 'vistas/comun/pie.php'; ?>
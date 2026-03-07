<?php
// Ya estamos en la carpeta 'includes', así que no hace falta poner '/includes/' en el require
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$conn = $app->conexionBd();

// Lógica de filtrado por categoría
$idCatFiltrada = isset($_GET['cat']) ? intval($_GET['cat']) : null;

// 1. Obtener todas las categorías para el selector superior
$res_cats = $conn->query("SELECT * FROM Categorias");

// 2. Obtener productos (filtrados o todos)
$query = "SELECT p.*, c.nombre AS nombre_cat 
          FROM Productos p 
          JOIN Categorias c ON p.id_categoria = c.id 
          WHERE p.disponible = 1 AND p.ofertado = 1";

if ($idCatFiltrada) {
    $query .= " AND p.id_categoria = $idCatFiltrada";
}

$query .= " ORDER BY c.nombre, p.nombre";
$result = $conn->query($query);

// La carpeta vistas está dentro de includes, así que la ruta es esta:
include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        
        <h1>Nuestra Carta</h1>

        <!-- SELECTOR DE CATEGORÍAS -->
        <div style="display: flex; gap: 20px; overflow-x: auto; padding: 20px 0; border-bottom: 1px solid #eee; margin-bottom: 30px;">
            <a href="carta.php" style="text-align: center; text-decoration: none; color: #333; min-width: 80px;">
                <div style="width: 60px; height: 60px; background: #333; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px;">TODOS</div>
                <small>Todos</small>
            </a>
            <?php while($cat = $res_cats->fetch_assoc()): ?>
                <a href="?cat=<?= $cat['id'] ?>" style="text-align: center; text-decoration: none; color: #333; min-width: 80px;">
                    <img src="<?= RUTA_APP ?>/img/<?= ($cat['imagen_url'] ?? 'categorias/default.png') ?>" 
                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid <?= ($idCatFiltrada == $cat['id']) ? '#d32f2f' : '#ddd' ?>; margin-bottom: 5px;"
                         onerror="this.src='<?= RUTA_APP ?>/img/categorias/default.png'">
                    <br><small><?= htmlspecialchars($cat['nombre']) ?></small>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- LISTADO DE PRODUCTOS -->
        <?php 
        if ($result && $result->num_rows > 0): 
            echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px;">';
            while ($prod = $result->fetch_assoc()): 
        ?>
                <div style="border: 1px solid #eee; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); background: white; display: flex; flex-direction: column;">
                    
                    <!-- Imagen del Producto -->
                    <img src="<?= RUTA_APP ?>/img/<?= ($prod['imagen_url'] ?? 'productos/default.png') ?>" 
                         style="width: 100%; height: 180px; object-fit: cover;"
                         onerror="this.src='<?= RUTA_APP ?>/img/productos/default.png'">

                    <div style="padding: 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <span style="font-size: 0.7rem; color: #d32f2f; font-weight: bold; text-transform: uppercase;"><?= $prod['nombre_cat'] ?></span>
                            <h3 style="margin: 5px 0;"><?= htmlspecialchars($prod['nombre']) ?></h3>
                            <p style="color: #666; font-size: 0.85rem; margin-bottom: 15px;"><?= htmlspecialchars($prod['descripcion']) ?></p>
                        </div>
                        
                        <div>
                            <p style="font-size: 1.2rem; font-weight: bold; margin-bottom: 10px;">
                                <?= number_format($prod['precio_base'] * (1 + $prod['iva']/100), 2) ?>€
                            </p>
                            
                            <form action="procesarCarrito.php" method="POST" style="display: flex; gap: 8px;">
                                <input type="hidden" name="id_producto" value="<?= $prod['id'] ?>">
                                <input type="number" name="cantidad" value="1" min="1" style="width: 40px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                <button type="submit" style="background: #d32f2f; color: white; border: none; padding: 10px; cursor: pointer; flex-grow: 1; border-radius: 4px; font-weight: bold;">
                                    AGREGAR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php 
            endwhile; 
            echo '</div>';
        else: 
        ?>
            <p style="text-align: center; color: #999; margin-top: 50px;">No hay productos en esta categoría.</p>
        <?php endif; ?>

    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>

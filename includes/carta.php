<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$conn = $app->conexionBd();

// Modificamos la consulta para traer el nombre de la categoría y ordenar por ella
$query = "SELECT p.*, c.nombre AS nombre_cat 
          FROM Productos p 
          JOIN Categorias c ON p.id_categoria = c.id 
          WHERE p.disponible = 1 AND p.ofertado = 1 
          ORDER BY c.nombre, p.nombre";
$result = $conn->query($query);

include 'vistas/comun/cabecera.php';
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px; display: flex; flex-direction: column;">
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'added'): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; margin-bottom: 20px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                <span>✓ Producto añadido con éxito.</span>
                <a href="carrito.php" style="color: #155724; font-weight: bold; text-decoration: underline;">Ver cesta</a>
            </div>
        <?php endif; ?>

        <h1>Nuestra Carta</h1>
        
        <?php 
        if ($result && $result->num_rows > 0): 
            $categoriaActual = "";
            while ($prod = $result->fetch_assoc()): 
                // Si la categoría cambia, imprimimos un nuevo encabezado
                if ($prod['nombre_cat'] !== $categoriaActual): 
                    if ($categoriaActual !== "") echo '</div>'; // Cerramos el grid anterior
                    $categoriaActual = $prod['nombre_cat'];
                    echo "<h2 style='border-bottom: 2px solid #333; margin-top: 30px; padding-bottom: 5px;'>{$categoriaActual}</h2>";
                    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 15px;">';
                endif;
        ?>
                <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h3 style="margin: 0 0 10px 0;"><?= htmlspecialchars($prod['nombre']) ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;"><?= htmlspecialchars($prod['descripcion']) ?></p>
                    </div>
                    
                    <div style="border-top: 1px solid #eee; pt: 15px;">
                        <p style="font-size: 1.1rem; color: #d32f2f; font-weight: bold; margin-bottom: 10px;">
                            <?= number_format($prod['precio_base'] * (1 + $prod['iva']/100), 2) ?>€
                        </p>
                        
                        <form action="procesarCarrito.php" method="POST" style="display: flex; gap: 10px; align-items: center;">
                            <input type="hidden" name="id_producto" value="<?= $prod['id'] ?>">
                            <input type="number" name="cantidad" value="1" min="1" style="width: 45px; padding: 5px; border: 1px solid #ccc;">
                            <button type="submit" style="background: #333; color: white; border: none; padding: 8px 12px; cursor: pointer; flex-grow: 1; border-radius: 4px;">
                                Añadir
                            </button>
                        </form>
                    </div>
                </div>
        <?php 
            endwhile; 
            echo '</div>'; // Cerramos el último grid
        else: 
        ?>
            <p>Actualmente no hay productos disponibles.</p>
        <?php endif; ?>

        <div style="margin-top: 50px; text-align: center; border-top: 2px solid #eee; padding-top: 30px;">
            <a href="carrito.php" style="background-color: #28a745; color: white; padding: 15px 40px; text-decoration: none; font-weight: bold; border-radius: 30px; font-size: 1.2rem; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                🛒 Ir a mi cesta
            </a>
        </div>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'vistas/comun/pie.php'; ?>
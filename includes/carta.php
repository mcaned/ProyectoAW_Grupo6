<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$conn = $app->conexionBd();
$idCatFiltrada = isset($_GET['cat']) ? intval($_GET['cat']) : null;

$mensajeExito = isset($_GET['añadido']) ? "Producto añadido al carrito correctamente." : null;

$res_cats = $conn->query("SELECT * FROM Categorias");   

$query = "SELECT p.*, c.nombre AS nombre_cat 
          FROM Productos p 
          JOIN Categorias c ON p.id_categoria = c.id 
          WHERE p.disponible = 1 AND p.ofertado = 1";

if ($idCatFiltrada) {
    $query .= " AND p.id_categoria = $idCatFiltrada";
}

$query .= " ORDER BY c.nombre, p.nombre";
$result = $conn->query($query);

include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        
        <h1>Nuestra Carta</h1>

        <?php if ($mensajeExito): ?>
            <div class="alerta-exito">
                <span class="check-icon">✓</span>
                <div class="contenido-alerta">
                    <?= $mensajeExito ?>
                    <a href="carrito.php" class="link-carrito">Ver mi pedido</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="contenedor-columnas">
            <a href="carta.php" class = "btn">
                TODOS
            </a>
            
            <?php while($cat = $res_cats->fetch_assoc()): ?>
                <?php $claseActiva = ($idCatFiltrada == $cat['id']) ?>
                <a href="?cat=<?= $cat['id'] ?>" class="texto-ops">
                    <img src="<?= RUTA_APP ?>/img/<?= ($cat['imagen_url'] ?? 'defecto.png') ?>" 
                         class="avatar-usuario" 
                         onerror="this.src='<?= RUTA_APP ?>/img/defecto.png'">
                    <br><small><?= htmlspecialchars($cat['nombre']) ?></small>
                </a>
            <?php endwhile; ?>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="cuadricula-pedidos">
                <?php while ($prod = $result->fetch_assoc()): ?>
                    <div class="tarjeta tarjeta-formulario">
                        
                        <img class = "imagen-carta" src="<?= RUTA_APP ?>/img/<?= ($prod['imagen_url'] ?? 'defecto.png') ?>" 
                             onerror="this.src='<?= RUTA_APP ?>/img/defecto.png'">

                        <div>
                            <span class="texto-rojo" >
                                <?= strtoupper($prod['nombre_cat']) ?>
                            </span>
                            <h3 class="titulo-serif">
                                <?= htmlspecialchars($prod['nombre']) ?>
                            </h3>
                            <p class="texto-ayuda">
                                <?= htmlspecialchars($prod['descripcion']) ?>
                            </p>
                            
                            <p>
                                <?= number_format($prod['precio_base'] * (1 + $prod['iva']/100), 2) ?>€
                            </p>
                            
                            <form action="procesarCarrito.php" method="POST">
                                <input type="hidden" name="id_producto" value="<?= $prod['id'] ?>">
                                <div>
                                    <input type="number" name="cantidad" value="1" min="1" class="input-formulario">
                                    <br><br>
                                    <button type="submit" class="btn-listo">
                                        AGREGAR
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="cocina-vacia">
                <p>No hay productos en esta categoría.</p>
            </div>
        <?php endif; ?>

    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
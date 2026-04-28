<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/producto.php';
require_once __DIR__ . '/clases/categorias.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

$idCatFiltrada = isset($_GET['cat']) ? intval($_GET['cat']) : null;

$mensajeExito = isset($_GET['añadido']) ? "Producto añadido al carrito correctamente." : null;

$listaCategorias = Categoria::listarTodas();

$productos = Producto::listar(true, $idCatFiltrada);

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
            
            <?php foreach($listaCategorias as $cat): ?>
                <a href="?cat=<?= $cat->getId() ?>" class="texto-ops <?= ($idCatFiltrada == $cat->getId()) ? 'activa' : '' ?>">
                    <img alt src="<?= RUTA_APP ?>/img/<?= $cat->getImagenUrl() ?? 'defecto.png' ?>" 
                         class="avatar-usuario" 
                         onerror="this.src='<?= RUTA_APP ?>/img/defecto.png'">
                    <br><small><?= htmlspecialchars($cat->getNombre()) ?></small>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($productos)): ?>
            <div class="cuadricula-pedidos">
                <?php foreach ($productos as $prod): ?>
                    <div class="tarjeta tarjeta-formulario">
                        
                        <img alt class = "imagen-carta" src="<?= RUTA_APP ?>/img/<?= ($prod->getImagenUrl() ?? 'defecto.png') ?>" 
                             onerror="this.src='<?= RUTA_APP ?>/img/defecto.png'">

                        <div>
                            <span class="texto-rojo" >
                                <?= strtoupper(htmlspecialchars($prod->getCatNom())) ?>
                            </span>
                            <h2 class="titulo-serif">
                                <?= htmlspecialchars($prod->getNombre()) ?>
                            </h2>
                            <p class="texto-ayuda">
                                <?= htmlspecialchars($prod->getDescripcion()) ?>
                            </p>
                            
                            <p>
                                <?= number_format($prod->getPrecioFinal(),2)?>€
                            </p>
                            
                            <form action="procesarCarrito.php" method="POST">
                                <input type="hidden" name="id_producto" value="<?= $prod->getId() ?>">
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
                <?php endforeach; ?>
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
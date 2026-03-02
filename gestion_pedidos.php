<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

include 'includes/vistas/comun/cabecera.php';
$conn = $app->conexionBd();

$query = "SELECT * FROM pedidos WHERE estado IN ('Recibido', 'En preparación', 'Listo cocina', 'Terminado') ORDER BY fecha_hora ASC";
$result = $conn->query($query);
$pedidos = [];
while ($row = $result->fetch_assoc()) {
    $pedidos[] = $row;
}
?>

<style>
    .contenedor-principal {
        display: flex; 
        background-color: #e0e0e0;
        min-height: 85vh;
    }

    .contenido-central {
        flex-grow: 1; 
        background-color: white;
        display: flex;
        flex-direction: column;
        min-width: 0; 
    }

    .contenedor-columnas {
        display: flex;
        gap: 10px;
        padding: 20px;
        background-color: #ffffff;
        flex-grow: 1;
        overflow-x: auto; 
    }

    .columna {
        flex: 1;
        min-width: 250px; 
        padding: 10px;
    }

    .titulo-columna {
        text-align: center;
        font-size: 15;
        margin-bottom: 15px;
        padding-bottom: 5px;
    }

    .tarjeta-pedido {
        background: white;
        padding: 15px;
        margin-bottom: 10px;
    }

    .boton-estado {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border: none;
        cursor: pointer;
        color: white;
    }
</style>

<div class="contenedor-principal">
    <main class="contenido-central">
        
        <div style="padding: 20px;  display: flex; justify-content: space-between; align-items: center;">
            <h1 style="margin: 0; font-size: 30;">Gestión de Pedidos</h1>
        </div>

        <div class="contenedor-columnas">
  
            <section class="columna" style="background-color: #ffcccc;">
                <h3 class="titulo-columna">🔴 PENDIENTE PAGO</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'Recibido'): ?>
                        <div class="tarjeta-pedido">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            Total: <strong><?= number_format($p['total'], 2) ?>€</strong>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="En preparación">
                                <button type="submit" class="boton-estado" style="background: #cc0000;">COBRAR</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna" style="background-color: #b9e4b5;">
                <h3 class="titulo-columna">⚪ EN COCINA</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'En preparación'): ?>
                        <div class="tarjeta-pedido">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            <p style="color: #666; font-size: 0.9rem; margin-top: 5px;">👨‍🍳 Los cocineros están trabajando en ello...</p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna" style="background-color: #c5a17e;">
                <h3 class="titulo-columna">🟠 REVISAR EXTRAS</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'Listo cocina'): ?>
                        <div class="tarjeta-pedido">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            <div style="margin: 10px 0; background: #fff9c4; padding: 5px; ">
                                <label style="display: block; cursor: pointer;"><input type="checkbox" required> Complementos incluidos</label>
                            </div>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="Terminado">
                                <button type="submit" class="boton-estado" style="background: #ff8000;">LISTO</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="columna" style="background-color: #cce5ff;">
                <h3 class="titulo-columna">🔵 ENTREGAR</h3>
                <?php foreach ($pedidos as $p): ?>
                    <?php if ($p['estado'] === 'Terminado'): ?>
                        <div class="tarjeta-pedido">
                            <strong>Pedido #<?= $p['id'] ?></strong><br>
                            <small>Tipo: <?= $p['tipo'] ?></small>
                            <form action="<?= RUTA_APP ?>/includes/actualizarEstado.php" method="POST">
                                <input type="hidden" name="id_pedido" value="<?= $p['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="Entregado">
                                <button type="submit" class="boton-estado" style="background: #0066cc;">ENTREGADO</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

        </div>
    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>

</div>

<?php include 'includes/vistas/comun/pie.php'; ?>

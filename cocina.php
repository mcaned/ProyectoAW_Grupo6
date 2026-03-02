<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'cocinero') {
    header('Location: index.php');
    exit();
}

include 'includes/vistas/comun/cabecera.php';
$conn = $app->conexionBd();

$query = "SELECT * FROM pedidos WHERE estado = 'En preparación' ORDER BY fecha_hora ASC";
$result = $conn->query($query);
?>

<div style="display: flex; min-height: 85vh;">

    <main style="flex-grow: 1; background-color: #ffffff; padding: 30px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000000; padding-bottom: 10px; margin-bottom: 20px;">
            <h1 style="margin: 0;">👨‍🍳 PANEL DE COCINA</h1>
            <div style="text-align: right;">
                <span style="font-size: 1.2rem;"><?= htmlspecialchars($_SESSION['nombre']) ?></span><br>
                <small style="color: #00e676; font-weight: bold;">EN SERVICIO</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div style="background: grey; padding: 20px; display: flex; flex-direction: column;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <h2 style="margin: 0;  color: #ffeb3b;">#<?= $row['id'] ?></h2>
                                <span style=" color: red; padding: 6px 10px; font-size: 1rem; font-weight: bold;">
                                    <?= strtoupper($row['tipo']) ?>
                                </span>
                            </div>
                        </div>

                        <form action="includes/actualizarEstado.php" method="POST" style="margin-top: 15px;">
                            <input type="hidden" name="id_pedido" value="<?= $row['id'] ?>">
                            <input type="hidden" name="nuevo_estado" value="Listo cocina">
                            <button type="submit" style="width: 100%; background: #00e676; color: #494848; border: none; padding: 15px; font-size: 1.2rem; font-weight: bold; border-radius: 8px; cursor: pointer;">
                                ✅ LISTO COCINA!
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: #706e6e; ">
            <!-- el grid column ayuda a que ocupe toda la pantalla y no solo una columna -->
                    <h3>No hay pedidos pendientes en cocina.</h3>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'includes/vistas/comun/pie.php'; ?>


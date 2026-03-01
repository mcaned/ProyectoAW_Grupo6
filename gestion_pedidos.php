<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

if (!isset($_SESSION['login']) || !in_array($_SESSION['rol'], ['camarero', 'gerente'])) {
    header('Location: index.php');
    exit();
}

include 'includes/vistas/comun/cabecera.php';
$conn = $app->conexionBd();

// Consulta usando 'fecha_hora' de tu base de datos
$query = "SELECT * FROM pedidos WHERE estado IN ('Recibido', 'En preparación', 'Listo cocina', 'Terminado') ORDER BY fecha_hora ASC";
$result = $conn->query($query);

/**
 * Función para renderizar cada tarjeta de pedido adaptada a Tablet
 * Incluye la lógica de revisión de productos para el paso de "Listo cocina" a "Terminado"
 */
function renderTarjetaPedido($row, $nuevoEstado, $textoBoton, $colorBoton) {
    $rutaProcesar = RUTA_APP . '/includes/actualizarEstado.php';
    
    $html = '<div style="background: white; padding: 15px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 6px solid ' . $colorBoton . ';">';
    $html .= '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">';
    $html .= '<span style="font-size:1.2rem; font-weight:bold;">#' . $row['id'] . '</span>';
    $html .= '<span style="background:#eee; padding:3px 8px; border-radius:4px; font-size:0.8rem;">' . strtoupper($row['tipo']) . '</span>';
    $html .= '</div>';
    
    $html .= '<div style="margin-bottom:15px; font-size:1.1rem;">Total: <strong>' . number_format($row['total'], 2) . '€</strong></div>';

    // REQUISITO: Revisar productos que no son de cocina (bebidas)
    if ($row['estado'] === 'Listo cocina') {
        $html .= '<div style="background: #fff9c4; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #fbc02d;">';
        $html .= '<strong style="display:block; margin-bottom:5px; font-size:0.85rem; color:#856404;">📝 REVISIÓN DE SALIDA:</strong>';
        $html .= '<label style="display:block; margin-bottom:5px; cursor:pointer;"><input type="checkbox" required> Bebidas preparadas</label>';
        $html .= '<label style="display:block; cursor:pointer;"><input type="checkbox" required> ¿Es para llevar? (Pack)</label>';
        $html .= '</div>';
    }

    if ($nuevoEstado) {
        $html .= '<form action="' . $rutaProcesar . '" method="POST">';
        $html .= '<input type="hidden" name="id_pedido" value="' . $row['id'] . '">';
        $html .= '<input type="hidden" name="nuevo_estado" value="' . $nuevoEstado . '">';
        $html .= '<button type="submit" style="width: 100%; background: ' . $colorBoton . '; color: white; border: none; padding: 15px; font-weight: bold; border-radius: 8px; cursor: pointer; font-size: 1rem; transition: opacity 0.2s;">';
        $html .= $textoBoton;
        $html .= '</button></form>';
    } else {
        $html .= '<div style="text-align:center; padding:10px; color:#999; font-style:italic; border:1px dashed #ccc;">En proceso...</div>';
    }
    
    $html .= '</div>';
    return $html;
}
?>

<div style="display: flex; flex-direction: column; height: 88vh; background-color: #f5f5f5; font-family: Segoe UI, sans-serif;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 30px; background: white; border-bottom: 3px solid #ddd;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <img src="<?= RUTA_APP ?>/img/avatar_<?= $_SESSION['nombre'] ?>.png" 
                 onerror="this.src='<?= RUTA_APP ?>/img/default-avatar.png'" 
                 style="width: 65px; height: 65px; border-radius: 50%; border: 3px solid #28a745; object-fit: cover;">
            <div>
                <h2 style="margin: 0;"><?= htmlspecialchars($_SESSION['nombre'] ) ?></h2>
                <small style="color: #28a745; font-weight: bold;">CAMARERO EN LÍNEA</small>
            </div>
        </div>
        <div style="text-align: center;">
            <h1 style="margin: 0; font-size: 1.5rem; color: #444;">GESTIÓN DE PEDIDOS</h1>
        </div>
        <div style="text-align: right; color: #666;">
            <strong><?= date('H:i') ?></strong><br>
            <?= date('d/m/Y') ?>
        </div>
    </div>

    <div style="display: flex; flex: 1; gap: 15px; padding: 15px; overflow-x: auto;">
        
        <section style="flex: 1; min-width: 300px; background: #ffebee; border-radius: 12px; padding: 12px; display: flex; flex-direction: column;">
            <h3 style="text-align: center; color: #c62828; margin-top: 0; border-bottom: 2px solid #ffcdd2; padding-bottom: 10px;">1. PENDIENTE PAGO</h3>
            <div style="flex: 1; overflow-y: auto; padding-right: 5px;">
                <?php 
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    if ($row['estado'] === 'Recibido') echo renderTarjetaPedido($row, 'En preparación', '💰 COBRAR', '#c62828');
                }
                ?>
            </div>
        </section>

        <section style="flex: 1; min-width: 300px; background: #f5f5f5; border-radius: 12px; padding: 12px; display: flex; flex-direction: column; border: 1px solid #ddd;">
            <h3 style="text-align: center; color: #616161; margin-top: 0; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px;">2. EN COCINA</h3>
            <div style="flex: 1; overflow-y: auto; padding-right: 5px;">
                <?php 
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    if ($row['estado'] === 'En preparación') echo renderTarjetaPedido($row, null, null, '#757575');
                }
                ?>
            </div>
        </section>

        <section style="flex: 1; min-width: 300px; background: #fff3e0; border-radius: 12px; padding: 12px; display: flex; flex-direction: column;">
            <h3 style="text-align: center; color: #e65100; margin-top: 0; border-bottom: 2px solid #ffe0b2; padding-bottom: 10px;">3. REVISAR EXTRAS</h3>
            <div style="flex: 1; overflow-y: auto; padding-right: 5px;">
                <?php 
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    if ($row['estado'] === 'Listo cocina') echo renderTarjetaPedido($row, 'Terminado', '📦 FINALIZAR', '#ef6c00');
                }
                ?>
            </div>
        </section>

        <section style="flex: 1; min-width: 300px; background: #e3f2fd; border-radius: 12px; padding: 12px; display: flex; flex-direction: column;">
            <h3 style="text-align: center; color: #0d47a1; margin-top: 0; border-bottom: 2px solid #bbdefb; padding-bottom: 10px;">4. ENTREGAR</h3>
            <div style="flex: 1; overflow-y: auto; padding-right: 5px;">
                <?php 
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    if ($row['estado'] === 'Terminado') echo renderTarjetaPedido($row, 'Entregado', '✔️ ENTREGADO', '#1565c0');
                }
                ?>
            </div>
        </section>

    </div>
</div>

<?php include 'includes/vistas/comun/pie.php'; ?>
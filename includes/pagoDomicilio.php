<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();
include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh;">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px;">
        <h1>Pago y Envío a Domicilio</h1>
        <div style="background: #fff3cd; padding: 15px; border: 1px solid #ffeeba; margin-bottom: 20px;">
            Estás finalizando un pedido para <strong>Llevar</strong>.
        </div>

        <form action="finalizarProceso.php" method="POST" style="max-width: 500px;" id="formPago">
            <div style="margin-bottom: 15px;">
                <label><strong>Dirección de entrega:</strong></label><br>
                <input type="text" name="direccion" required style="width: 100%; padding: 8px;" placeholder="Calle, número, piso...">
            </div>

            <div style="margin-bottom: 15px;">
                <label><strong>Método de Pago Online:</strong></label><br>
                <select name="metodo" id="metodoPago" onchange="toggleTarjeta()" style="width: 100%; padding: 8px;">
                    <option value="tarjeta">Tarjeta de Crédito / Débito</option>
                    <option value="paypal">PayPal</option>
                    <option value="paypal">ApplePay</option>
                </select>
            </div>

            <div id="camposTarjeta" style="background: #f8f9fa; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px;">
                <h4 style="margin-top: 0;">Datos de la Tarjeta</h4>
                <div style="margin-bottom: 10px;">
                    <label>Número de tarjeta:</label><br>
                    <input type="text" name="num_tarjeta" placeholder="0000 0000 0000 0000" pattern="\d{16}" title="16 números" style="width: 100%; padding: 8px;">
                </div>
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label>Caducidad:</label><br>
                        <input type="text" name="caducidad" placeholder="MM/AA" pattern="\d{2}/\d{2}" style="width: 100%; padding: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <label>CVV:</label><br>
                        <input type="password" name="cvv" placeholder="123" pattern="\d{3}" style="width: 100%; padding: 8px;">
                    </div>
                </div>
            </div>

            <button type="submit" style="background: #28a745; color: white; padding: 15px 25px; border: none; cursor: pointer; font-weight: bold; width: 100%; border-radius: 5px;">
                Confirmar y Pagar
            </button>
        </form>
    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<script>
function toggleTarjeta() {
    const metodo = document.getElementById('metodoPago').value;
    const campos = document.getElementById('camposTarjeta');
    const inputs = campos.getElementsByTagName('input');

    if (metodo === 'tarjeta') {
        campos.style.display = 'block';
        // Hacer obligatorios si es tarjeta
        for(let i=0; i<inputs.length; i++) inputs[i].required = true;
    } else {
        campos.style.display = 'none';
        // Quitar obligatoriedad si es PayPal
        for(let i=0; i<inputs.length; i++) inputs[i].required = false;
    }
}
// Ejecutar al cargar por si acaso
toggleTarjeta();
</script>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
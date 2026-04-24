<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init();
include __DIR__ . '/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include __DIR__ . '/vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h1>Pago y Envío a Domicilio</h1>
        
        <div class="alerta-error-critico">
            Estás finalizando un pedido para llevar.
        </div>
        <br>
        <form action="finalizarProceso.php" method="POST" class="formulario-estandar" id="formPago">
            <div class="item-barra-izquierda">
                <label><strong>Dirección de entrega:</strong></label>
                <input type="text" name="direccion" required class="input-formulario" placeholder="Calle, número, piso...">
            </div>

            <div class="item-barra-izquierda">
                <label><strong>Método de Pago Online:</strong></label>
                <select name="metodo" id="metodoPago" onchange="Tarjeta()" class="input-formulario">
                    <option value="tarjeta">Tarjeta de Crédito / Débito</option>
                    <option value="paypal">PayPal</option>
                    <option value="applepay">ApplePay</option>
                </select>
            </div>

            <div id="camposTarjeta">
                <h2 class="titulo-serif">Datos de la Tarjeta</h2>
                <div class="item-barra-izquierda">
                    <label>Número de tarjeta:</label>
                    <input type="text" name="num_tarjeta" class="input-formulario" placeholder="0000 0000 0000 0000" pattern="\d{16}" title="16 números">
                </div>
                
                <div class="contenedor-info-pedido">
                    <div class="columna-info">
                        <label>Caducidad:</label>
                        <input type="text" name="caducidad" class="input-formulario" placeholder="MM/AA" pattern="\d{2}/\d{2}">
                    </div>
                    <div class="columna-info">
                        <label>CVV:</label>
                        <input type="password" name="cvv" class="input-formulario" placeholder="123" pattern="\d{3}"    >
                    </div>
                </div>
            </div>
            <br>
            <button type="submit" class="btn-listo margen-superior">
                Confirmar y Pagar
            </button>
        </form>
    </main>

    <?php include __DIR__ . '/vistas/comun/sideBarDer.php'; ?>
</div>

<script>
function Tarjeta() {
    const metodo = document.getElementById('metodoPago').value;
    const campos = document.getElementById('camposTarjeta');
    const inputs = campos.getElementsByTagName('input');

    if (metodo === 'tarjeta') {
        campos.style.display = 'block';
        for(let i=0; i<inputs.length; i++) inputs[i].required = true;
    } else {
        campos.style.display = 'none';
        for(let i=0; i<inputs.length; i++) inputs[i].required = false;
    }
}
Tarjeta();
</script>

<?php include __DIR__ . '/vistas/comun/pie.php'; ?>
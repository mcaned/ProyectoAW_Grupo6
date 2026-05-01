<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/producto.php';
require_once __DIR__ . '/clases/ofertas.php'; 
require_once __DIR__ . '/clases/gestor_ofertas.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['actualizar_ofertas'])) {
        $_SESSION['ofertas_seleccionadas'] = $_POST['id_ofertas'] ?? [];
    } 
    elseif (isset($_POST['action_oferta']) && $_POST['action_oferta'] === 'clear') {
        $_SESSION['ofertas_seleccionadas'] = [];
    }
    header('Location: carrito.php');
    exit();
}

if (isset($_SESSION['ofertas_seleccionadas']) && !empty($_SESSION['ofertas_seleccionadas'])) {
    $ofertasValidas = [];
    $inventarioTemporal = $_SESSION['carrito'] ?? [];

    foreach ($_SESSION['ofertas_seleccionadas'] as $idO) {
        $ahorro = GestorOfertas::calcularAhorro($inventarioTemporal, (int)$idO);

        if ($ahorro > 0) {
            $ofertasValidas[] = $idO;
            
            $ofertaDoc = Oferta::buscaPorId($idO);
            if ($ofertaDoc) {
                foreach ($ofertaDoc->getProductos() as $item) {
                    $idP = $item->getIdProducto();
                    $cantNecesaria = $item->getCantidad();
                    if (isset($inventarioTemporal[$idP])) {
                        $inventarioTemporal[$idP] -= $cantNecesaria;
                    }
                }
            }
        }
    }
    $_SESSION['ofertas_seleccionadas'] = $ofertasValidas;
}

$idOfertasSeleccionadas = $_SESSION['ofertas_seleccionadas'] ?? [];

include 'vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h1>Tu Carrito de Compra</h1>

        <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
            <table class="tabla-gestion">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Base</th>
                        <th>IVA</th>
                        <th>Subtotal</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_iva_incluido = 0;
                    foreach ($_SESSION['carrito'] as $id_prod => $cantidad):
                        $p = Producto::buscaPorId($id_prod);
                        if ($p):
                            $subtotal_con_iva = $p->getPrecioFinal() * $cantidad;
                            $total_iva_incluido += $subtotal_con_iva;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($p->getNombre()) ?></td>
                            <td class="texto-centrado">
                                <form action="procesarCarrito.php" method="POST" class="form-cantidad">
                                    <input type="hidden" name="id_producto" value="<?= $p->getId()?>">
                                    <input type="hidden" name="action" value="update">
                                    
                                    <button type="submit" name="cantidad" value="<?= $cantidad - 1 ?>" <?= ($cantidad <= 1) ? 'disabled' : '' ?>>-</button>
                                    <span class="cantidad-numero"><?= $cantidad ?></span>
                                    <button type="submit" name="cantidad" value="<?= $cantidad + 1 ?>">+</button>
                                </form>
                            </td>
                            <td><?= number_format($p->getPrecioBase(), 2) ?>€</td>
                            <td><?= $p->getIva()?>%</td>
                            <td><strong><?= number_format($subtotal_con_iva, 2) ?>€</strong></td>
                            <td class="texto-centrado">
                                <form action="procesarCarrito.php" method="POST">
                                    <input type="hidden" name="id_producto" value="<?= $p->getId() ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>

            <div class="margen-superior">
                <h2 class="titulo-serif">Ofertas Aplicables</h2>
                
                <form method="POST" action="carrito.php" id="form-ofertas">
                    <div class="flex-col">
                        <?php 
                        $ofertas = Oferta::listarActivas();
                        $inventarioLibre = $_SESSION['carrito'] ?? [];
                        foreach ($idOfertasSeleccionadas as $idSel) {
                            $oSel = Oferta::buscaPorId($idSel);
                            if ($oSel) {
                                foreach ($oSel->getProductos() as $pItem) {
                                    $idP = $pItem->getIdProducto();
                                    if (isset($inventarioLibre[$idP])) {
                                        $inventarioLibre[$idP] -= $pItem->getCantidad();
                                    }
                                }
                            }
                        }

                        foreach ($ofertas as $oferta): 
                            $idO = $oferta->getId();
                            $esSeleccionada = in_array($idO, $idOfertasSeleccionadas);
                        
                            if ($esSeleccionada) {
                                $cumpleRequisitos = true;

                                $ahorroMostrar = GestorOfertas::calcularAhorro($_SESSION['carrito'], $idO);
                            } else {
                                $ahorroMostrar = GestorOfertas::calcularAhorro($inventarioLibre, $idO);
                                $cumpleRequisitos = ($ahorroMostrar > 0);
                            }
                        ?>
                            <div class="tarjeta">
                                
                                    <input type="checkbox" name="id_ofertas[]" value="<?= $idO ?>" 
                                        <?= $esSeleccionada ? 'checked' : '' ?>
                                        <?= (!$cumpleRequisitos && !$esSeleccionada) ? 'disabled' : '' ?>
                                        onchange="document.getElementById('form-ofertas').submit()">

                                    <div >
                                        <div class="info-tarjeta" >
                                            <h3><?= htmlspecialchars($oferta->getNombre()) ?></h3>
                                            <?php if ($cumpleRequisitos): ?>
                                                <span class="tipo-pedido">AHORRO: <?= number_format($ahorroMostrar, 2) ?>€</span>
                                            <?php endif; ?>
                                        </div>
                                        <p><?= htmlspecialchars($oferta->getDescripcion()) ?></p>
                                        <?php if (!$cumpleRequisitos && !$esSeleccionada): ?>
                                            <p class="texto-rojo">
                                                (No quedan productos disponibles en el carrito para esta oferta)
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="actualizar_ofertas" value="1">
            
                </form>

                <?php if (!empty($idOfertasSeleccionadas)): ?>
                    <form method="POST" action="carrito.php">
                        <input type="hidden" name="action_oferta" value="clear">
                        <button type="submit" class="btn-cancelar">✕ Quitar todas las ofertas</button>
                    </form>
                <?php endif; ?>
            </div>

            <?php 
                $ahorroTotalFinal = GestorOfertas::calcularAhorro($_SESSION['carrito'], $idOfertasSeleccionadas);
                $totalBruto = $total_iva_incluido;
                $totalNeto = $totalBruto - $ahorroTotalFinal;
            ?>

            <div class="titulo-serif">
                <p>Total a pagar (IVA incluido): <strong><?= number_format($totalBruto, 2) ?>€</strong></p>
            </div>

            <div class="titulo-serif">
                <?php if ($ahorroTotalFinal > 0): ?>
                    <p>Descuento aplicado: <?= number_format($ahorroTotalFinal, 2) ?>€</p>
                <?php endif; ?>
                <p>Total a pagar: <strong><?= number_format($totalNeto, 2) ?>€</strong></p>
            </div>

            <form action="confirmarPedido.php" method="POST">
                <?php foreach($idOfertasSeleccionadas as $idO): ?>
                    <input type="hidden" name="id_ofertas[]" value="<?= $idO ?>">
                <?php endforeach; ?>
                <input type="hidden" name="ahorro" value="<?= $ahorroTotalFinal ?>">
                
                <div class="titulo-serif">
                    <label><strong>Tipo de pedido:</strong></label><br>
                    <input type="radio" name="tipo" value="Local" checked> Consumir en el local
                    <input type="radio" name="tipo" value="Llevar"> Para llevar
                </div>
                
                <button type="submit" class="btn-listo margen-superior">Confirmar y Pagar</button>
            </form>

        <?php else: ?>
            <div class="cocina-vacia">
                <p>El carrito está vacío.</p>
                <a href="carta.php" class="enlace-editar">Vuelve a la carta para añadir productos</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>

<?php include 'vistas/comun/pie.php'; ?>
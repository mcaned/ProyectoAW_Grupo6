<div class="contenedor-barra-izquierda">
    <ul class="lista-barra-izquierda">
        <?php if (isset($_SESSION['login'])): ?>
            
            <?php if ($_SESSION['rol'] === 'cliente'): ?>
                <li class="item-barra-izquierda">
                    <a href="<?= RUTA_APP ?>/includes/carta.php" class="enlace-barra-izquierda">🍴 Carta</a>
                </li> 
                <li class="item-barra-izquierda">
                    <a href="<?= RUTA_APP ?>/includes/pedido.php" class="enlace-barra-izquierda">📋 Ver mis pedidos</a>
                </li>
                <li class="item-barra-izquierda">
                    <a href="<?= RUTA_APP ?>/includes/carrito.php" class="enlace-barra-izquierda">🛒 Carrito</a>
                </li>
            <?php endif; ?>

            <?php if ($_SESSION['rol'] === 'gerente'): ?>
                <li class="item-barra-izquierda">
                    <a href="<?= RUTA_APP ?>/admin.php" class="enlace-barra-izquierda">⚙️ Panel de Administración</a>
                </li>
            <?php endif; ?>

        <?php else: ?>
            <li class="item-barra-izquierda">
                <p class="texto-sin-sesion">Inicia sesión para ver opciones.</p>
            </li>
        <?php endif; ?>
    </ul>
</div>
<div style="padding: 20px;">
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 15px;">
            <a href="<?= RUTA_APP ?>/includes/carta.php" style="text-decoration: none; color: #333; font-weight: bold;">🍴 Carta</a>
        </li>
        
        <li style="margin-bottom: 15px;">
            <a href="<?= RUTA_APP ?>/includes/pedido.php" style="text-decoration: none; color: #333; font-weight: bold;">
                📋 <?= (isset($_SESSION['rol']) && $_SESSION['rol'] === 'gerente') ? 'Gestionar Pedidos' : 'Ver mis pedidos' ?>
            </a>
        </li>

        <li style="margin-bottom: 15px;">
            <a href="<?= RUTA_APP ?>/includes/carrito.php" style="text-decoration: none; color: #333; font-weight: bold;">🛒 Carrito</a>
        </li>
    </ul>
</div>

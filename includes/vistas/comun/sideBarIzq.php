<div style="padding: 20;">
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 15;">
            <a href="<?= RUTA_APP ?>/includes/carta.php" style=" text-decoration: none; color: dark grey; font-weight: bold;">🍴 Carta</a>
        </li> 
        
        <li style="margin-bottom: 15;">
            <a href="<?= RUTA_APP ?>/includes/pedido.php" style="text-decoration: none; color: dark grey; font-weight: bold;">
                📋 <?= (isset($_SESSION['rol']) && $_SESSION['rol'] === 'gerente') ? 'Gestionar Pedidos' : 'Ver mis pedidos' ?>
            </a>
        </li>

        <li style="margin-bottom: 15;">
            <a href="<?= RUTA_APP ?>/includes/carrito.php" style="text-decoration: none; color: dark grey; font-weight: bold;">🛒 Carrito</a>
        </li>
    </ul>
</div>
<!--text-decoration lo ponemos a none para que no se nos visualice la linea -->

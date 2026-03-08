<div style="padding: 20px; background-color: #cccccc4a; border-radius: 8px; min-height: 80vh;">
    <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="margin-bottom: 15px;">
            <a href="<?= RUTA_APP ?>/includes/carta.php" style="text-decoration: none; color: #555; font-weight: bold;">🍴 Carta</a>
        </li> 
        
        <li style="margin-bottom: 15px;">
            <!-- Ahora siempre apunta a tus propios pedidos -->
            <a href="<?= RUTA_APP ?>/includes/pedido.php" style="text-decoration: none; color: #555; font-weight: bold;">
                📋 Ver mis pedidos
            </a>
        </li>

        <li style="margin-bottom: 15px;">
            <a href="<?= RUTA_APP ?>/includes/carrito.php" style="text-decoration: none; color: #555; font-weight: bold;">🛒 Carrito</a>
        </li>

         <!-- BOTÓN EXTRA SOLO PARA EL GERENTE -->
        <?php if (isset($_SESSION['login']) && $_SESSION['rol'] === 'gerente'): ?>
        <li style="margin-bottom: 15px; border-top: 1px solid #ccc; padding-top: 15px;">
            <a href="<?= RUTA_APP ?>/admin.php" style="text-decoration: none; color: #555; font-weight: bold;">⚙️ Panel de Administración</a>
        </li>
        <?php endif; ?>
    </ul>
</div>

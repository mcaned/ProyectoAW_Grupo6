<aside style="width: 200px; background-color: #cccccc4a; padding: 15px; color: #333; font-size: 14px;">
    <?php if (isset($_SESSION['login'])): ?>
        <div style="text-align: center; border-bottom: 1px solid #999; padding-bottom: 10px; margin-bottom: 10px;">
            
            <img src="<?= RUTA_APP ?>/img/defecto.png" alt="Avatar del usuario" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #666; margin-bottom: 10px;">
            
            <p style="margin: 0; font-weight: bold; font-size: 16px;">
                <?= htmlspecialchars($_SESSION['nombre']) ?>
            </p>
            <p style="margin: 5px 0 0 0; font-style: italic; color: #666;">
                Rol: <?= htmlspecialchars($_SESSION['rol']) ?>
            </p>
        </div>
        <ul style="list-style: none; padding: 0; margin: 0; text-align: center;">
            <li>
                <a href="<?= RUTA_APP ?>/perfil.php" style="text-decoration: none; color: #333; font-weight: bold;">
                    ✏️ Editar Perfil
                </a>
            </li>
        </ul>
    <?php else: ?>
        <p style="text-align: center; color: #666;">No has iniciado sesión.</p>
    <?php endif; ?>
</aside>
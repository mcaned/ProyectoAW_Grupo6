<aside class="barra-derecha">
    <?php if (isset($_SESSION['login'])): ?>
        <?php 
            $avatar = (isset($_SESSION['avatar']) && $_SESSION['avatar'] !== '') ? $_SESSION['avatar'] : 'defecto.png';
            $rutaAvatar = RUTA_APP . '/img/' . $avatar;
        ?>
        <div class="info-perfil-usuario">
            <img src="<?= $rutaAvatar ?>" alt="Avatar del usuario" class="avatar-usuario">
            
            <p>
                <?= htmlspecialchars($_SESSION['nombre']) ?>
            </p>
            <p class="rol-usuario">
                <?= htmlspecialchars($_SESSION['rol']) ?>
            </p>
        </div>
        <ul class="lista-barra-derecha">
            <li>
                <a href="<?= RUTA_APP ?>/includes/perfil.php" class="btn-editar-perfil">
                    ✏️ Editar Perfil
                </a>
            </li>
        </ul>
    <?php else: ?>
        <p>No has iniciado sesión.</p>
    <?php endif; ?>
</aside>
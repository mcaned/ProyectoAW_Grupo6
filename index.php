<?php 

require_once __DIR__ . '/includes/config.php'; 
require_once __DIR__ . '/includes/Aplicacion.php';

include 'includes/vistas/comun/cabecera.php'; 
?>

<div style="display: flex; background-color: #e0e0e0; min-height: 85vh; font-family: sans-serif;">

    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 40px; border-left: 1px solid #ccc; border-right: 1px solid #ccc;">
        
        <?php if (isset($_SESSION['login']) && $_SESSION['login']): ?>
            <h2 style="font-family: serif; font-size: 1.8rem; margin-top: 0;">Panel de Control</h2>
            <p>Hola <strong><?= $_SESSION['nombre'] ?></strong>, bienvenido de nuevo al Bistro FDI.</p>
            <p>Usa el menú de la izquierda para navegar.</p>
        <?php else: ?>
            <h2 style="font-family: serif; font-size: 1.8rem; margin-top: 0;">Acceso al sistema</h2>
            
            <div style="border: 1px solid #999; padding: 25px; margin-top: 30px; position: relative; width: 450px; background-color: #fff;">
                <span style="position: absolute; top: -12px; left: 15px; background: #333; color: white; padding: 2px 10px; font-size: 0.85rem; font-weight: bold;">
                    Usuario y contraseña
                </span>
                
                <form action="login.php" method="POST">
                    <input type="hidden" name="idFormulario" value="formLogin">
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Nombre de usuario:</label>
                        <input type="text" name="nombreUsuario" value="admin" style="width: 180px; padding: 3px; border: 1px solid #777;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Password:</label>
                        <input type="password" name="password" value="adminpass" style="width: 180px; padding: 3px; border: 1px solid #000;">
                    </div>
                    
                    <button type="submit" style="padding: 3px 20px; cursor: pointer; background-color: #f0f0f0; border: 1px solid #777; font-size: 0.9rem;">
                        Entrar
                    </button>
                </form>
            </div>
        <?php endif; ?>

    </main>

    <?php include 'includes/vistas/comun/sideBarDer.php'; ?>

</div>

<?php 
include 'includes/vistas/comun/pie.php'; 
?>
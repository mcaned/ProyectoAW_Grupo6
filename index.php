<?php include 'cabecera.php'; ?>

<div style="display: flex; background-color: #e0e0e0; min-height: 80vh;">
    
    <?php include 'sideBarIzq.php'; ?>

    <main style="flex-grow: 1; background-color: white; padding: 30px;">
        <h2 style="font-family: serif;">Acceso al sistema</h2>
        
        <div style="border: 1px solid #ccc; padding: 20px; margin-top: 20px; position: relative; width: 400px;">
            <span style="position: absolute; top: -12px; left: 10px; background: #333; color: white; padding: 2px 8px; font-size: 0.8rem;">Usuario y contraseña</span>
            
            <form>
                <label style="display: block; margin-bottom: 5px;">Nombre de usuario:</label>
                <input type="text" value="admin" style="width: 150px; margin-bottom: 10px; border: 1px solid #999;">
                
                <label style="display: block; margin-bottom: 5px;">Password:</label>
                <input type="password" value="********" style="width: 150px; margin-bottom: 15px; border: 1px solid #000;">
                
                <br>
                <button type="submit" style="padding: 2px 15px;">Entrar</button>
            </form>
        </div>
    </main>

    <?php include 'sideBarDer.php'; ?>

</div>

</body>
</html>

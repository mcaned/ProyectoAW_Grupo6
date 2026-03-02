<style>
    .header-bistro {
        background-color: grey;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .header-section {
        flex: 1; 
    }

    
    .header-logo img { /*Tenemos a la izq el logo*/
        width: 230px;
        height: auto;
        display: block;
    }

    
    .header-titles { /*Centro texto*/
        text-align: center;
    }
    .header-titles h1 { margin: 0; font-size: 40; }
    .header-titles h2 { margin: 0; font-size: 17; color: white; font-style: italic; }

    
    .header-links { /* Derecha Login */
        text-align: right;
        font-size: 17;
        padding-right: 20; //crea espacio a la derecha
    }
    .header-links a { color: white;  font-weight: bold; }
    .header-links a:hover { text-decoration: underline; } /*subrayamos salir*/
</style>

<header class="header-bistro">
    <div class="header-section header-logo">
        <img src="<?= RUTA_APP ?>/img/logo.png" alt="Logo">
    </div>

    <div class="header-section header-titles">
        <h1>BISTRO FDI</h1>
        <h2>¿Te gusta el pisto?</h2>
    </div>

    <div class="header-section header-links">
        <?php if (isset($_SESSION['login'])): ?> 
            Bienvenido, <?= htmlspecialchars($_SESSION['nombre'])?> | 
            <a href="<?= RUTA_APP ?>/logout.php">Salir</a>
        <?php else: ?>
            <a href="<?= RUTA_APP ?>/login.php">Login</a> | 
            <a href="<?= RUTA_APP ?>/registro.php">Registro</a>
        <?php endif; ?>
    </div>
</header>

<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/usuarios/usuario.php';
require_once __DIR__ . '/clases/pedidos.php';

$app = Aplicacion::getInstance(); 
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

// --- LÓGICA DE BORRADO ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $usuarioBorrar = Usuario::buscaPorId($id);
    
    if ($usuarioBorrar){
        //no borrarse a si mismo
        if ($id == $_SESSION['idUsuario']) {
            header('Location: gestion_usuarios.php?error=autoborrado'); exit();
        }
        
        // No borrar al último gerente
       if ($usuarioBorrar->getRol() === 'gerente') {
            if (Usuario::contarPorRol('gerente') <= 1) {
                header('Location: gestion_usuarios.php?error=ultimo_gerente');
                exit();
            }
        }

        $usuarioBorrar->borrar();
        header('Location: gestion_usuarios.php?msg=borrado_ok'); 
        exit();
    }
   
}

$usuarios = Usuario::listar();
include 'vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    
    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>👥 Gestión de Usuarios</h1>
            <a href="<?= RUTA_APP ?>/admin.php" class="btn-atras">⬅️ Volver al Panel</a>
        </div>

        <!-- ALERTAS DE SEGURIDAD -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alerta-error-critico" style="background:#fee; color:red; padding:15px; border:1px solid red; margin-bottom:20px;">
                <?php 
                    if($_GET['error'] == 'ultimo_gerente') echo "⚠️ Error: No puedes eliminar al último GERENTE. El sistema debe tener al menos uno.";
                    if($_GET['error'] == 'autoborrado') echo "⚠️ Error: No puedes eliminar tu propia cuenta mientras estás conectado.";
                ?>
            </div>
        <?php endif; ?>

        <div class="contenedor-acciones-superior">
            <a href="usuarios.php" class="btn">+ NUEVO USUARIO</a>
        </div>
        
        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Username</th><th>Nombre</th><th>Rol</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <?php 
                    $confirmMsg = ($u->getRol() === 'cliente') ? "¿Borrar usuario? (Se eliminarán sus pedidos)" : "¿Seguro que quieres borrar este usuario?";
                ?>
                <tr>
                    <td><?= htmlspecialchars($u->getNombreUsuario()) ?></td>
                    <td><?= htmlspecialchars($u->getNombre()) ?></td>
                    <td><span class="etiqueta-rol <?= $u->getRol() ?>"><?= strtoupper($u->getRol()) ?></span></td>
                    <td>
                        <a href="usuarios.php?id=<?= $u->getId() ?>" class="enlace-editar">📝 Editar</a> | 
                        <a href="?delete=<?= $u->getId() ?>" class="enlace-borrar" 
                           onclick="return confirm('<?= $confirmMsg ?>')">🗑️ Borrar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?> 
</div>
<?php 
$usuarios->free();
include 'vistas/comun/pie.php'; ?>

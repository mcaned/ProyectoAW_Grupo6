<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();
$conn = $app->conexionBd();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

// --- LÓGICA DE BORRADO ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    //  No borrarse a uno mismo
    if ($id == $_SESSION['idUsuario']) {
        header('Location: gestion_usuarios.php?error=autoborrado'); exit();
    }

    // No borrar al último gerente
    $resU = $conn->query("SELECT rol FROM Usuarios WHERE id = $id");
    $u_a_borrar = $resU->fetch_assoc();
    $resU>free();

    if ($u_a_borrar['rol'] === 'gerente') {
        $resCount = $conn->query("SELECT COUNT(*) as total FROM Usuarios WHERE rol = 'gerente'");
        $totalGerentes = $resCount->fetch_assoc()['total'];
        $resCount->free();
        if ($totalGerentes <= 1) {
            header('Location: gestion_usuarios.php?error=ultimo_gerente'); exit();
        }
    }

    // Borrado en cascada
    $conn->query("DELETE FROM Lineas_Pedido WHERE id_pedido IN (SELECT id FROM Pedidos WHERE id_cliente = $id)");
    $conn->query("DELETE FROM Pedidos WHERE id_cliente = $id");
    $conn->query("DELETE FROM Usuarios WHERE id = $id");
    
    header('Location: gestion_usuarios.php?msg=borrado_ok'); exit();
}

$usuarios = $conn->query("SELECT * FROM Usuarios ORDER BY rol DESC");
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
                <?php while($u = $usuarios->fetch_assoc()): ?>
                <?php 
                    $confirmMsg = ($u['rol'] === 'cliente') ? "¿Borrar usuario? (Se eliminarán sus pedidos)" : "¿Seguro que quieres borrar este usuario?";
                ?>
                <tr>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><span class="etiqueta-rol <?= $u['rol'] ?>"><?= strtoupper($u['rol']) ?></span></td>
                    <td>
                        <a href="usuarios.php?id=<?= $u['id'] ?>" class="enlace-editar">📝 Editar</a> | 
                        <a href="?delete=<?= $u['id'] ?>" class="enlace-borrar" 
                           onclick="return confirm('<?= $confirmMsg ?>')">🗑️ Borrar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?> 
</div>
<?php 
$usuarios->free();
include 'vistas/comun/pie.php'; ?>

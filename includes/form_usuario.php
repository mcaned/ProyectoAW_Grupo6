<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();
$conn = $app->conexionBd();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

$id = $_GET['id'] ?? null;
$u = ['username' => '', 'email' => '', 'nombre' => '', 'apellidos' => '', 'rol' => 'cliente'];

if ($id) {
    $res = $conn->query("SELECT * FROM Usuarios WHERE id = ".intval($id));
    if ($res && $res->num_rows > 0) { $u = $res->fetch_assoc(); }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $nom = $conn->real_escape_string($_POST['nombre']);
    $ape = $conn->real_escape_string($_POST['apellidos']);
    $nuevo_rol = $conn->real_escape_string($_POST['rol']);
    $pass_plana = $_POST['password'];
    
    $permitir = true;

    // No dejar al sistema sin gerentes al cambiar el rol
    if ($id && $u['rol'] === 'gerente' && $nuevo_rol !== 'gerente') {
        $resCount = $conn->query("SELECT COUNT(*) as total FROM Usuarios WHERE rol = 'gerente'");
        if ($resCount->fetch_assoc()['total'] <= 1) {
            $error = "⚠️ No puedes cambiar el rol al último GERENTE.";
            $permitir = false;
        }
    }

    if ($permitir) {
        // Borrar pedidos si deja de ser cliente
        if ($id && $u['rol'] === 'cliente' && $nuevo_rol !== 'cliente') {
            $conn->query("DELETE FROM Lineas_Pedido WHERE id_pedido IN (SELECT id FROM Pedidos WHERE id_cliente = $id)");
            $conn->query("DELETE FROM Pedidos WHERE id_cliente = $id");
        }

        if ($id) {
            $sql = "UPDATE Usuarios SET username='$user', email='$email', nombre='$nom', apellidos='$ape', rol='$nuevo_rol'";
            if (!empty($pass_plana)) {
                $hash = password_hash($pass_plana, PASSWORD_DEFAULT);
                $sql .= ", password_hash='$hash'";
            }
            $sql .= " WHERE id=$id";
        } else {
            $hash = password_hash($pass_plana, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Usuarios (username, email, nombre, apellidos, password_hash, rol, avatar_url) VALUES ('$user', '$email', '$nom', '$ape', '$hash', '$nuevo_rol', 'defecto.png')";
        }

          if ($conn->query($sql)) {
            
            // Si el ID del usuario que acabo de editar soy YO mismo
            if ($id == $_SESSION['idUsuario']) {
                
                // Borramos la sesión para obligar a re-identificarse
                session_destroy(); 

                // Te mandamos al login con un mensaje explicativo
                header('Location: ' . RUTA_APP . '/login.php?msg=cambio_rol');
                exit();
            }

            // Si he editado a OTRO usuario, vuelvo al listado normal de gestión
            header('Location: gestion_usuarios.php?msg=ok'); 
            exit();

        } else { 
            $error = "Error al guardar: " . $conn->error; 
        }
    }
}

include 'vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>
    
    <main class="contenido-central bloque-formulario">
        <h1><?= $id ? '📝 Editar Usuario' : '➕ Crear Usuario' ?></h1>
        <?php if(isset($error)) echo "<div class='alerta-error-critico' style='color:red; border:1px solid red; padding:10px; margin-bottom:10px;'>$error</div>"; ?>

        <form method="POST" class="formulario-estandar" onsubmit="return confirmarCambio();">
            <p><label><strong>Username:</strong></label><br><input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" required class="input-formulario"></p>
            <p><label><strong>Email:</strong></label><br><input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required class="input-formulario"></p>
            <p><label><strong>Nombre:</strong></label><br><input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>" required class="input-formulario"></p>
            <p><label><strong>Apellidos:</strong></label><br><input type="text" name="apellidos" value="<?= htmlspecialchars($u['apellidos']) ?>" required class="input-formulario"></p>
            
            <p>
                <label><strong>Rol:</strong></label><br>
                <select name="rol" id="selector_rol" class="input-formulario">
                    <option value="cliente" <?= $u['rol']=='cliente'?'selected':'' ?>>Cliente</option>
                    <option value="camarero" <?= $u['rol']=='camarero'?'selected':'' ?>>Camarero</option>
                    <option value="cocinero" <?= $u['rol']=='cocinero'?'selected':'' ?>>Cocinero</option>
                    <option value="gerente" <?= $u['rol']=='gerente'?'selected':'' ?>>Gerente</option>
                </select>
            </p>

            <p><label><strong>Contraseña <?= $id ? '(vacío para no cambiar)' : '' ?>:</strong></label><br>
            <input type="password" name="password" <?= $id ? '' : 'required' ?> class="input-formulario"></p>

            <div class="acciones-formulario">
                <button type="submit" class="btn-verde">💾 GUARDAR</button>
                <a href="gestion_usuarios.php" class="enlace-cancelar">Cancelar</a>
            </div>
        </form>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>

<script>
function confirmarCambio() {
    var rolOriginal = "<?= $u['rol'] ?>";
    var nuevoRol = document.getElementById('selector_rol').value;
    if (rolOriginal === 'cliente' && nuevoRol !== 'cliente') {
        return confirm("¡ADVERTENCIA! Se borrarán los pedidos del cliente al cambiarle el rol. ¿Continuar?");
    }
    return true;
}
</script>
<?php include 'vistas/comun/pie.php'; ?>

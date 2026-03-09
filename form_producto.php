<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

// Seguridad: Solo el gerente puede entrar aquí
if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

$conn = $app->conexionBd();
$id = $_GET['id'] ?? null;

// Valores por defecto para un producto nuevo (Punto 7 y 8)
$p = [
    'nombre' => '', 
    'descripcion' => '', 
    'id_categoria' => '', 
    'precio_base' => 0, 
    'iva' => '10', 
    'disponible' => 1,
    'imagen_url' => 'productos/default.jpg' // Actualizado a .jpg
];

// Si venimos a EDITAR, cargamos los datos de la base de datos
if ($id) {
    $res = $conn->query("SELECT * FROM Productos WHERE id = " . intval($id));
    if ($res && $res->num_rows > 0) {
        $p = $res->fetch_assoc();
    }
}

// PROCESAR EL FORMULARIO AL DARLE A GUARDAR
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat  = intval($_POST['id_categoria']);
    $nom  = $conn->real_escape_string($_POST['nombre']);
    $des  = $conn->real_escape_string($_POST['descripcion']);
    $pre  = floatval($_POST['precio_base']);
    $iva  = $_POST['iva'];
    $dis  = isset($_POST['disponible']) ? 1 : 0;

    // --- LÓGICA DE IMAGEN POR DEFECTO PARA PRODUCTOS ---
    $img  = $conn->real_escape_string($_POST['imagen_url']);
    if (empty($img)) {
        $img = 'productos/default.jpg'; // Si está vacío, ponemos la de por defecto
    }

    if ($id) {
        // ACCIÓN: ACTUALIZAR PRODUCTO EXISTENTE (Punto 7 y 8)
        $query = "UPDATE Productos SET 
                    id_categoria=$cat, 
                    nombre='$nom', 
                    descripcion='$des', 
                    precio_base=$pre, 
                    iva='$iva', 
                    disponible=$dis, 
                    imagen_url='$img' 
                  WHERE id=$id";
    } else {
        // ACCIÓN: CREAR NUEVO PRODUCTO (Se crea con ofertado=1 por defecto)
        $query = "INSERT INTO Productos (id_categoria, nombre, descripcion, precio_base, iva, disponible, imagen_url, ofertado) 
                  VALUES ($cat, '$nom', '$des', $pre, '$iva', $dis, '$img', 1)";
    }
    
    if ($conn->query($query)) {
        header('Location: gestion_productos.php'); 
        exit();
    } else {
        $error = "Error en la base de datos: " . $conn->error;
    }
}

// Obtener categorías para el selector (Punto 5.1)
$categorias = $conn->query("SELECT * FROM Categorias");

include 'includes/vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main class="contenido-central bloque-formulario">
        <h1><?= $id ? '📝 Editar Producto' : '➕ Crear Nuevo Producto' ?></h1>
        <hr><br>

        <?php if(isset($error)) echo "<p class='texto-error'>$error</p>"; ?>

        <form method="POST" class="formulario-estandar">
            <p>
                <label><strong>Categoría:</strong></label><br>
                <select name="id_categoria" required class="input-formulario">
                    <option value="">-- Selecciona una categoría --</option>
                    <?php while($c = $categorias->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= ($p['id_categoria'] == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </p>

            <p>
                <label><strong>Nombre del Producto:</strong></label><br>
                <input type="text" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>" required class="input-formulario">
            </p>

            <p>
                <label><strong>Descripción:</strong></label><br>
                <textarea name="descripcion" rows="4" class="input-formulario"><?= htmlspecialchars($p['descripcion']) ?></textarea>
            </p>

            <p>
                <label><strong>Ruta de la Imagen:</strong></label><br>
                <input type="text" name="imagen_url" value="<?= htmlspecialchars($p['imagen_url']) ?>" placeholder="productos/ejemplo.jpg" class="input-formulario">
                <small class="texto-ayuda">Si se deja vacío, se usará: <em>productos/default.jpg</em></small>
            </p>

            <div class="grupo-formulario-flex">
                <p class="flex-1">
                    <label><strong>Precio Base (€):</strong></label><br>
                    <input type="number" step="0.01" name="precio_base" id="base" value="<?= $p['precio_base'] ?>" required class="input-formulario" oninput="recalc()">
                </p>

                <p class="flex-1">
                    <label><strong>IVA (%):</strong></label><br>
                    <select name="iva" id="iva" class="input-formulario" onchange="recalc()">
                        <option value="4" <?= $p['iva']=='4'?'selected':'' ?>>4%</option>
                        <option value="10" <?= $p['iva']=='10'?'selected':'' ?>>10%</option>
                        <option value="21" <?= $p['iva']=='21'?'selected':'' ?>>21%</option>
                    </select>
                </p>
            </div>

            <div class="caja-precio-final">
                <span class="etiqueta-precio">Precio Final (Base + IVA): </span>
                <span id="total" class="valor-precio">0.00</span> 
                <span class="valor-precio">€</span>
            </div>

            <p>
                <label class="label-checkbox">
                    <input type="checkbox" name="disponible" <?= $p['disponible'] ? 'checked' : '' ?>> 
                    <strong>¿Producto disponible para la venta? (Stock)</strong>
                </label>
            </p>

            <div class="acciones-formulario">
                <button type="submit" class="btn-verde">💾 GUARDAR PRODUCTO</button>
                <a href="gestion_productos.php" class="enlace-cancelar">Cancelar y volver</a>
            </div>
        </form>
    </main>
</div>
<script>
function recalc() {
    let base = parseFloat(document.getElementById('base').value) || 0;
    let iva = parseInt(document.getElementById('iva').value);
    let total = base * (1 + iva/100);
    document.getElementById('total').innerText = total.toFixed(2);
}
window.onload = recalc;
</script>
<?php include 'includes/vistas/comun/pie.php'; ?>
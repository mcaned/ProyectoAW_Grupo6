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

// Valores por defecto para un producto nuevo
$p = [
    'nombre' => '', 
    'descripcion' => '', 
    'id_categoria' => '', 
    'precio_base' => 0, 
    'iva' => '10', 
    'disponible' => 1,
    'imagen_url' => 'productos/default.png' // Valor por defecto
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
    $img  = $conn->real_escape_string($_POST['imagen_url']);
    $dis  = isset($_POST['disponible']) ? 1 : 0;

    if ($id) {
        // ACCIÓN: ACTUALIZAR PRODUCTO EXISTENTE
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
        // ACCIÓN: CREAR NUEVO PRODUCTO
        $query = "INSERT INTO Productos (id_categoria, nombre, descripcion, precio_base, iva, disponible, imagen_url, ofertado) 
                  VALUES ($cat, '$nom', '$des', $pre, '$iva', $dis, '$img', 1)";
    }

    if ($conn->query($query)) {
        header('Location: gestion_productos.php'); 
        exit();
    } else {
        $error = "Error al guardar en la base de datos: " . $conn->error;
    }
}

// Obtener categorías para el selector
$categorias = $conn->query("SELECT * FROM Categorias");

include 'includes/vistas/comun/cabecera.php';
?>

<div style="display: flex; min-height: 85vh; background-color: #f0f0f0;">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>

    <main style="flex-grow: 1; padding: 40px; background: white; margin: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h1><?= $id ? '📝 Editar Producto' : '➕ Crear Nuevo Producto' ?></h1>
        <hr><br>

        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST" style="max-width: 600px;">
            <p>
                <label>Categoría:</label><br>
                <select name="id_categoria" required style="width:100%; padding: 8px;">
                    <option value="">-- Selecciona una categoría --</option>
                    <?php while($c = $categorias->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>" <?= ($p['id_categoria'] == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </p>

            <p>
                <label>Nombre del Producto:</label><br>
                <input type="text" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>" required style="width:100%; padding: 8px;">
            </p>

            <p>
                <label>Descripción:</label><br>
                <textarea name="descripcion" rows="4" style="width:100%; padding: 8px;"><?= htmlspecialchars($p['descripcion']) ?></textarea>
            </p>

            <p>
                <label>Ruta de la Imagen (ej: productos/hamburguesa.jpg):</label><br>
                <input type="text" name="imagen_url" value="<?= htmlspecialchars($p['imagen_url']) ?>" style="width:100%; padding: 8px;">
                <small style="color: #666;">Asegúrate de que el archivo esté en la carpeta <strong>img/</strong></small>
            </p>

            <div style="display: flex; gap: 20px;">
                <p style="flex: 1;">
                    <label>Precio Base (€):</label><br>
                    <input type="number" step="0.01" name="precio_base" id="base" value="<?= $p['precio_base'] ?>" required style="width:100%; padding: 8px;" oninput="recalc()">
                </p>

                <p style="flex: 1;">
                    <label>IVA (%):</label><br>
                    <select name="iva" id="iva" style="width:100%; padding: 8px;" onchange="recalc()">
                        <option value="4" <?= $p['iva']=='4'?'selected':'' ?>>4%</option>
                        <option value="10" <?= $p['iva']=='10'?'selected':'' ?>>10%</option>
                        <option value="21" <?= $p['iva']=='21'?'selected':'' ?>>21%</option>
                    </select>
                </p>
            </div>

            <!-- USABILIDAD: Cálculo automático del precio final -->
            <div style="background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <span style="font-size: 1.1rem; font-weight: bold;">Precio Final (con IVA): </span>
                <span id="total" style="font-size: 1.3rem; color: #d32f2f; font-weight: bold;">0.00</span> 
                <span style="font-size: 1.3rem; color: #d32f2f; font-weight: bold;">€</span>
            </div>

            <p>
                <label>
                    <input type="checkbox" name="disponible" <?= $p['disponible'] ? 'checked' : '' ?>> 
                    ¿El producto está disponible para la venta ahora?
                </label>
            </p>

            <div style="margin-top: 30px;">
                <button type="submit" style="padding: 12px 25px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    💾 GUARDAR PRODUCTO
                </button>
                <a href="gestion_productos.php" style="margin-left: 15px; text-decoration: none; color: #666;">Cancelar y volver</a>
            </div>
        </form>
    </main>
</div>

<script>
// Función para calcular el precio final automáticamente
function recalc() {
    let base = parseFloat(document.getElementById('base').value) || 0;
    let iva = parseInt(document.getElementById('iva').value);
    let total = base * (1 + iva/100);
    document.getElementById('total').innerText = total.toFixed(2);
}
// Ejecutar al cargar por si estamos editando
window.onload = recalc;
</script>

<?php include 'includes/vistas/comun/pie.php'; ?>

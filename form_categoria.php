<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

// --- 1. SEGURIDAD: Solo el gerente puede entrar ---
if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); exit();
}

$conn = $app->conexionBd();
$id = $_GET['id'] ?? null;

// Valores por defecto para una categoría nueva
$cat = [
    'nombre' => '', 
    'descripcion' => '', 
    'imagen_url' => 'categorias/default.jpg' // Ponemos el defecto aquí también
];

if ($id) {
    $res = $conn->query("SELECT * FROM Categorias WHERE id = ".intval($id));
    if ($res && $res->num_rows > 0) {
        $cat = $res->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $conn->real_escape_string($_POST['nombre']);
    $des = $conn->real_escape_string($_POST['descripcion']);
    
    // --- LÓGICA DE IMAGEN POR DEFECTO ---
    $img = $conn->real_escape_string($_POST['imagen_url']);
    if (empty($img)) {
        $img = 'categorias/default.jpg'; 
    }

    if ($id) {
        $query = "UPDATE Categorias SET nombre='$nom', descripcion='$des', imagen_url='$img' WHERE id=$id";
    } else {
        $query = "INSERT INTO Categorias (nombre, descripcion, imagen_url) VALUES ('$nom', '$des', '$img')";
    }
    $conn->query($query);
    header('Location: gestion_categorias.php'); exit();
}

include 'includes/vistas/comun/cabecera.php';
?>
<div class="contenedor-principal bg-gris-claro">
    <?php include 'includes/vistas/comun/sideBarIzq.php'; ?>
    <main class="contenido-central tarjeta-formulario">
        <h1><?= $id ? '📝 Editar Categoría' : '➕ Crear Nueva Categoría' ?></h1>
        <hr><br>

        <form method="POST" class="formulario-estandar">
            <p>
                <label><strong>Nombre de la Categoría:</strong></label><br>
                <input type="text" name="nombre" value="<?= htmlspecialchars($cat['nombre']) ?>" required class="input-formulario">
            </p>

            <p>
                <label><strong>Descripción:</strong></label><br>
                <textarea name="descripcion" rows="4" class="input-formulario"><?= htmlspecialchars($cat['descripcion']) ?></textarea>
            </p>

            <p>
                <label><strong>Ruta de la Imagen:</strong></label><br>
                <input type="text" name="imagen_url" value="<?= htmlspecialchars($cat['imagen_url']) ?>" placeholder="categorias/ejemplo.jpg" class="input-formulario">
                <small class="texto-ayuda">Si se deja vacío, se usará: <em>categorias/default.jpg</em></small>
            </p>

            <div class="acciones-formulario">
                <button type="submit" class="btn-verde">💾 GUARDAR CATEGORÍA</button>
                <a href="gestion_categorias.php" class="enlace-cancelar">Cancelar</a>
            </div>
        </form>
    </main>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>

<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/aplicacion.php';
$app = Aplicacion::getInstance(); $app->init();

$conn = $app->conexionBd();
$id = $_GET['id'] ?? null;
$cat = ['nombre' => '', 'descripcion' => '', 'imagen_url' => ''];

if ($id) {
    $res = $conn->query("SELECT * FROM Categorias WHERE id = ".intval($id));
    $cat = $res->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $conn->real_escape_string($_POST['nombre']);
    $des = $conn->real_escape_string($_POST['descripcion']);
    
    // --- LÓGICA DE IMAGEN POR DEFECTO ---
    $img = $conn->real_escape_string($_POST['imagen_url']);
    if (empty($img)) {
        $img = 'categorias/default.jpg'; // Si no escribe nada, ponemos esta ruta
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
<div style="display: flex; min-height: 85vh;">
    <main style="flex-grow: 1; padding: 40px; background: white;">
        <h1><?= $id ? 'Editar' : 'Crear' ?> Categoría</h1>
        <form method="POST" style="max-width: 500px;">
            Nombre: <input type="text" name="nombre" value="<?= $cat['nombre'] ?>" required style="width:100%"><br><br>
            Descripción: <textarea name="descripcion" style="width:100%"><?= $cat['descripcion'] ?></textarea><br><br>
            URL Imagen: <input type="text" name="imagen_url" value="<?= $cat['imagen_url'] ?>" style="width:100%"><br><br>
            <button type="submit" style="padding: 10px 20px; background: green; color: white; border: none;">Guardar</button>
            <a href="gestion_categorias.php">Cancelar</a>
        </form>
    </main>
</div>
<?php include 'includes/vistas/comun/pie.php'; ?>

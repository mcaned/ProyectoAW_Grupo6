<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/usuarios/formularioProducto.php';

$app = Aplicacion::getInstance();
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); 
    exit();
}

$id = $_GET['id'] ?? null;

include 'vistas/comun/cabecera.php';
?>

<div class="contenedor-principal">
    <?php include 'vistas/comun/sideBarIzq.php'; ?>

    <main class="contenido-central">
        <h1><?= $id ? '📝 Editar Producto' : '➕ Crear Nuevo Producto' ?></h1>
        
        <?php
            $form = new FormularioProducto($id);
            echo $form->gestiona();
        ?>
    </main>

    <?php include 'vistas/comun/sideBarDer.php'; ?>
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
<?php include 'vistas/comun/pie.php'; ?>
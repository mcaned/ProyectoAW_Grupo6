<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/clases/aplicacion.php';
require_once __DIR__ . '/clases/ofertas.php';

$app = Aplicacion::getInstance(); 
$app->init();

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header('Location: index.php'); 
    exit();
}

if (isset($_GET['retirar'])) {
    $idOferta = intval($_GET['retirar']);
    if (Oferta::borrar($idOferta)) {
        header('Location: gestion_ofertas.php?msg=borrado_ok');
    } else {
        header('Location: gestion_ofertas.php?error=fallo_borrado');
    }
    exit();
}

$listaOfertas = Oferta::listarTodas();

include 'vistas/comun/cabecera.php';
?>
<div class="contenedor-principal">

    <main class="contenido-central">
        <div class="cabecera-seccion-flexible">
            <h1>🍔 Gestión de Ofertas</h1>
            <a href="../admin.php" class="btn-atras">⬅️ Volver al Panel</a>
        </div>

        <div class="contenedor-acciones-superior">
            <a href="oferta.php" class="btn">+ NUEVA OFERTA</a>
        </div>

        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Descuento</th>
                    <th>Fecha comienzo</th>
                    <th>Fecha fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($listaOfertas as $o): 
                ?>
                <tr>
                   <td><?= htmlspecialchars($o->getNombre()) ?></td>
                    <td><?= htmlspecialchars($o->getDescripcion()) ?></td>
                   
                    <td><?= number_format($o->getDescuento(), 0) ?>%</td>
                    <td><?= date('d/m/Y', strtotime($o->getComienzo())) ?></td>
                    <td><?= date('d/m/Y', strtotime($o->getFin())) ?></td>
                    <td>
                        <a href="oferta.php?id=<?= $o->getId() ?>" class="enlace-editar">📝 Editar</a> | 
                        <a href="?retirar=<?= $o->getId() ?>" class="enlace-borrar" onclick="return confirm('¿Eliminar esta oferta permanentemente?')">🚫 Retirar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($listaOfertas)): ?>
                    <tr><td>No hay ofertas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
     <?php include 'vistas/comun/sideBarDer.php'; ?>
</div>
<?php 
include 'vistas/comun/pie.php'; ?>
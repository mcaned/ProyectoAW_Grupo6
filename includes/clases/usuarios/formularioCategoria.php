<?php

require_once __DIR__ . '/../formulario.php';
require_once __DIR__ . '/../aplicacion.php'; 
require_once __DIR__ . '/../categorias.php';

class formularioCategoria extends formulario {
    private $idCategoria;
    private $datos;
    private $categoria;

    public function __construct($idCategoria = null) {
        $this->idCategoria = $idCategoria;
        $this->categoria = null;
        $action = $_SERVER['PHP_SELF'] . ($idCategoria ? "?id=$idCategoria" : "");
        parent::__construct('formCategoria', ['action' => $action]);
        
        if ($idCategoria) {
            $this->categoria = Categoria::buscaPorId($idCategoria);
        }
    }

    protected function generaCamposFormulario($datosIniciales) {
        $nombre = $this->categoria ? $this->categoria->getNombre() : '';
        $descripcion = $this->categoria ? $this->categoria->getDescripcion() : '';
        $img = $this->categoria ? $this->categoria->getImagenUrl() : 'categorias/default.jpg';

        return <<<EOF
        <div class="formulario-estandar">
            <p>
                <label><strong>Nombre de la Categoría:</strong></label><br>
                <input type="text" name="nombre" value="$nombre" required class="input-formulario">
            </p>
            <p>
                <label><strong>Descripción:</strong></label><br>
                <textarea name="descripcion" rows="4" class="input-formulario">$descripcion</textarea>
            </p>
            <p>
                <label><strong>Ruta de la Imagen:</strong></label><br>
                <input type="text" name="imagen_url" value="$img" class="input-formulario">
            </p>
            <div class="acciones-formulario">
                <button type="submit" class="btn-guardar">💾 GUARDAR CATEGORÍA</button>
                <a href="gestion_categorias.php" class="btn-cancelar">❌ CANCELAR Y VOLVER</a>
            </div>
        </div>
EOF;
    }

    protected function procesaFormulario($datos) {
        $datos['id'] = $this->idCategoria;
        if (Categoria::guardaOActualiza($datos)) {
            header('Location: gestion_categorias.php');
            exit();
        }
        return ["Error al procesar la categoría."];
    }
}
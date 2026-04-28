<?php
require_once __DIR__ . '/../formulario.php';
require_once __DIR__ . '/../aplicacion.php';    
require_once __DIR__ . '/../producto.php';    
require_once __DIR__ . '/../categorias.php';    

class formularioProducto extends formulario {
    private $idProducto;
    private $datos;

    public function __construct($idProducto = null) {
        $this->idProducto = $idProducto;
        $action = $_SERVER['PHP_SELF'] . ($idProducto ? "?id=$idProducto" : "");
        parent::__construct('formProducto', ['action' => $action]);
        
        $this->cargarDatos();
    }

    private function cargarDatos() {
        if ($this->idProducto) {
            $this->datosProducto = Producto::buscaPorId($this->idProducto);
        } else {
            $this->datosProducto = null;
        }
    }

    protected function generaCamposFormulario($datosIniciales) {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();

        $nombre = $datosIniciales['nombre'] ?? ($this->datosProducto ? $this->datosProducto->getNombre() : '');
        $descripcion = $datosIniciales['descripcion'] ?? ($this->datosProducto ? $this->datosProducto->getDescripcion() : '');
        $id_cat = $datosIniciales['id_categoria'] ?? ($this->datosProducto ? $this->datosProducto->getIdCategoria() : '');
        $precio = $datosIniciales['precio_base'] ?? ($this->datosProducto ? $this->datosProducto->getPrecioBase() : 0);
        $iva = $datosIniciales['iva'] ?? ($this->datosProducto ? $this->datosProducto->getIva() : '10');
        $img = $datosIniciales['imagen_url'] ?? ($this->datosProducto ? $this->datosProducto->getImagenUrl() : 'productos/default.jpg');
        $esDisponible = isset($datosIniciales['disponible']) || ($this->datosProducto && $this->datosProducto->getDisponible());
        $checkDisponible = $esDisponible ? 'checked' : '';
        
        $categorias = Categoria::listarTodas();
        $selectCategorias = '<option value="">-- Selecciona una categoría --</option>';
        foreach($categorias as $c) {
            $sel = ($id_cat == $c->getId()) ? 'selected' : '';
            $selectCategorias .= "<option value='{$c->getId()}' $sel>" . htmlspecialchars($c->getNombre()) . "</option>";
        }



        $iva4 = $iva == '4' ? 'selected' : '';
        $iva10 = $iva == '10' ? 'selected' : '';
        $iva21 = $iva == '21' ? 'selected' : '';

        return <<<EOF
        <div class="formulario-estandar">
            <p>
                <label><strong>Categoría:</strong></label><br>
                <select name="id_categoria" required class="input-formulario">
                    $selectCategorias
                </select>
            </p>

            <p>
                <label><strong>Nombre del Producto:</strong></label><br>
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

            <div>
                <p class="flex-1">
                    <label><strong>Precio Base (€):</strong></label><br>
                    <input type="number" step="0.01" name="precio_base" id="base" value="$precio" required class="input-formulario" oninput="recalc()">
                </p>
                <p class="flex-1">
                    <label><strong>IVA (%):</strong></label><br>
                    <select name="iva" id="iva" class="input-formulario" onchange="recalc()">
                        <option value="4" $iva4>4%</option>
                        <option value="10" $iva10>10%</option>
                        <option value="21" $iva21>21%</option>
                    </select>
                </p>
            </div>

            <div>
                <span>Precio Final (Base + IVA): </span>
                <span id="total">0.00</span>€
            </div>

            <p>
                <label>
                    <input type="checkbox" name="disponible" $checkDisponible> 
                    <strong>¿Producto disponible?</strong>
                </label>
            </p>

            <div class="acciones-formulario">
                <button type="submit" class="btn-guardar">💾 GUARDAR PRODUCTO</button>
                <a href="gestion_productos.php" class="btn-cancelar">❌ CANCELAR Y VOLVER</a>
            </div>
        </div>
        <script>
        function recalc() {
            const base = parseFloat(document.getElementById('base').value) || 0;
            const iva = parseFloat(document.getElementById('iva').value) || 0;
            const total = base + (base * (iva / 100));
            document.getElementById('total').innerText = total.toFixed(2);
        }
        recalc(); // Ejecutar al cargar
        </script>
EOF;
    }

   protected function procesaFormulario($datos) {
        $datos['id'] = $this->idProducto;

        if (Producto::guardaOActualiza($datos)) {
            header('Location: gestion_productos.php');
            exit();
        }
        
        return ["Error al guardar el producto."];
    }
}
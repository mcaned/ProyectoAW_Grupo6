<?php
require_once __DIR__ . '/../formulario.php';
require_once __DIR__ . '/../aplicacion.php';    

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
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();
        
        $this->datos = [
            'nombre' => '', 'descripcion' => '', 'id_categoria' => '', 
            'precio_base' => 0, 'iva' => '10', 'disponible' => 1,
            'imagen_url' => 'productos/default.jpg'
        ];

        if ($this->idProducto) {
            $res = $conn->query("SELECT * FROM Productos WHERE id = " . intval($this->idProducto));
            if ($res && $res->num_rows > 0) {
                $this->datos = $res->fetch_assoc();
            }
        }
    }

    protected function generaCamposFormulario($datosIniciales) {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();
        
        $p = array_merge($this->datos, $datosIniciales);
        
        $categorias = $conn->query("SELECT * FROM Categorias");
        $selectCategorias = '<option value="">-- Selecciona una categoría --</option>';
        while($c = $categorias->fetch_assoc()) {
            $sel = ($p['id_categoria'] == $c['id']) ? 'selected' : '';
            $selectCategorias .= "<option value='{$c['id']}' $sel>" . htmlspecialchars($c['nombre']) . "</option>";
        }

        $checkDisponible = $p['disponible'] ? 'checked' : '';
        $iva4 = $p['iva'] == '4' ? 'selected' : '';
        $iva10 = $p['iva'] == '10' ? 'selected' : '';
        $iva21 = $p['iva'] == '21' ? 'selected' : '';

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
                <input type="text" name="nombre" value="{$p['nombre']}" required class="input-formulario">
            </p>

            <p>
                <label><strong>Descripción:</strong></label><br>
                <textarea name="descripcion" rows="4" class="input-formulario">{$p['descripcion']}</textarea>
            </p>

            <p>
                <label><strong>Ruta de la Imagen:</strong></label><br>
                <input type="text" name="imagen_url" value="{$p['imagen_url']}" class="input-formulario">
            </p>

            <div>
                <p class="flex-1">
                    <label><strong>Precio Base (€):</strong></label><br>
                    <input type="number" step="0.01" name="precio_base" id="base" value="{$p['precio_base']}" required class="input-formulario" oninput="recalc()">
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
EOF;
    }

    protected function procesaFormulario($datos) {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();

        $id_cat = intval($datos['id_categoria']);
        $nombre = $conn->real_escape_string($datos['nombre']);
        $desc = $conn->real_escape_string($datos['descripcion']);
        $precio = floatval($datos['precio_base']);
        $iva = $datos['iva'];
        $disp = isset($datos['disponible']) ? 1 : 0;
        $img = $conn->real_escape_string($datos['imagen_url'] ?: 'productos/default.jpg');

        if ($this->idProducto) {
            $query = "UPDATE Productos SET id_categoria=$id_cat, nombre='$nombre', descripcion='$desc', precio_base=$precio, iva='$iva', disponible=$disp, imagen_url='$img' WHERE id=" . intval($this->idProducto);
        } else {
            $query = "INSERT INTO Productos (id_categoria, nombre, descripcion, precio_base, iva, disponible, imagen_url, ofertado) VALUES ($id_cat, '$nombre', '$desc', $precio, '$iva', $disp, '$img', 1)";
        }

        if ($conn->query($query)) {
            header('Location: gestion_productos.php');
            exit();
        }
        return ["Error al guardar en la base de datos."];
    }
}
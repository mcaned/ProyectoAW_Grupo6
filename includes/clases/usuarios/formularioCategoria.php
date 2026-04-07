<?php
// includes/clases/usuarios/formularioCategoria.php

require_once __DIR__ . '/../formulario.php';
require_once __DIR__ . '/../aplicacion.php'; 

class formularioCategoria extends formulario {
    private $idCategoria;
    private $datos;

    public function __construct($idCategoria = null) {
        $this->idCategoria = $idCategoria;
        $action = $_SERVER['PHP_SELF'] . ($idCategoria ? "?id=$idCategoria" : "");
        parent::__construct('formCategoria', ['action' => $action]);
        
        $this->cargarDatos();
    }

    private function cargarDatos() {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();
        
        $this->datos = [
            'nombre' => '',
            'descripcion' => ''
        ];

        if ($this->idCategoria) {
            $id = intval($this->idCategoria);
            $res = $conn->query("SELECT * FROM Categorias WHERE id = $id");
            if ($res && $res->num_rows > 0) {
                $this->datos = $res->fetch_assoc();
            }
        }
    }

    protected function generaCamposFormulario($datosIniciales) {
        $nombre = htmlspecialchars($datosIniciales['nombre'] ?? $this->datos['nombre']);
        $descripcion = htmlspecialchars($datosIniciales['descripcion'] ?? $this->datos['descripcion']);

        return <<<EOF
        <div class="formulario-estandar">
            <p>
                <label><strong>Nombre de la Categoría:</strong></label><br>
                <input type="text" name="nombre" value="$nombre" required class="input-formulario" placeholder="Ej: Hamburguesas">
            </p>

            <p>
                <label><strong>Descripción:</strong></label><br>
                <textarea name="descripcion" rows="4" class="input-formulario" placeholder="Breve descripción de la categoría">$descripcion</textarea>
            </p>

            <div class="acciones-formulario">
                <button type="submit" class="btn-verde">💾 GUARDAR CATEGORÍA</button>
                <a href="gestion_categorias.php" class="enlace-cancelar">Cancelar y volver</a>
            </div>
        </div>
EOF;
    }

    protected function procesaFormulario($datos) {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();

        $nombre = $conn->real_escape_string($datos['nombre'] ?? '');
        $descripcion = $conn->real_escape_string($datos['descripcion'] ?? '');

        if (empty($nombre)) {
            return ["El nombre de la categoría es obligatorio."];
        }

        if ($this->idCategoria) {
            // EDITAR
            $id = intval($this->idCategoria);
            $query = "UPDATE Categorias SET nombre='$nombre', descripcion='$descripcion' WHERE id=$id";
        } else {
            // CREAR NUEVA
            $query = "INSERT INTO Categorias (nombre, descripcion) VALUES ('$nombre', '$descripcion')";
        }

        if ($conn->query($query)) {
            header('Location: gestion_categorias.php');
            exit();
        } else {
            return ["Error en la base de datos: " . $conn->error];
        }
    }
}
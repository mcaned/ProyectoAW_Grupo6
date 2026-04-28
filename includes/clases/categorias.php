<?php
require_once __DIR__ . '/aplicacion.php';

class Categoria {
    private $id;
    private $nombre;
    private $descripcion;
    private $imagen_url;

    public function __construct($id, $nombre, $descripcion, $imagen_url) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->imagen_url = $imagen_url;
    }

    public static function buscaPorId($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT * FROM categorias WHERE id=%d", intval($id));
        $rs = $conn->query($query);
        if ($rs && $f = $rs->fetch_assoc()) {
            $result = new Categoria($f['id'], $f['nombre'], $f['descripcion'], $f['imagen_url']);
            $rs->free();
            return $result;
        }
        return null;
    }

    public static function listarTodas() {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = "SELECT * FROM categorias ORDER BY nombre ASC";
        $rs = $conn->query($query);
        $categorias = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) {
                $categorias[] = new Categoria($f['id'], $f['nombre'], $f['descripcion'], $f['imagen_url']);
            }
            $rs->free();
        }
        return $categorias;
    }

    public static function guardaOActualiza($datos) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $nombre = $conn->real_escape_string($datos['nombre']);
        $desc = $conn->real_escape_string($datos['descripcion']);
        $img = $conn->real_escape_string($datos['imagen_url'] ?: 'categorias/default.jpg');

        if (isset($datos['id']) && !empty($datos['id'])) {
            $id = intval($datos['id']);
            $query = "UPDATE categorias SET nombre='$nombre', descripcion='$desc', imagen_url='$img' WHERE id=$id";
        } else {
            $query = "INSERT INTO categorias (nombre, descripcion, imagen_url) VALUES ('$nombre', '$desc', '$img')";
        }
        return $conn->query($query);
    }

    public static function borrar($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $idLimpiado = intval($id);

        if (self::tieneProductos($idLimpiado)) {
            return false;
        }

        $query = "DELETE FROM categorias WHERE id = $idLimpiado";
        return $conn->query($query);
    }

    public static function tieneProductos($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT COUNT(*) as total FROM productos WHERE id_categoria = %d", intval($id));
        $rs = $conn->query($query);
        if ($rs) {
            $f = $rs->fetch_assoc();
            $rs->free(); 
            return ($f['total'] > 0);
        }
    
        return false;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getImagenUrl() { return $this->imagen_url; }
}
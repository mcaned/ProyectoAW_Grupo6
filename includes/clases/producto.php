<?php
require_once __DIR__ . '/aplicacion.php';

class Producto {
    
    private $id;
    private $id_categoria;
    private $nombre;
    private $descripcion;
    private $precio_base;
    private $iva;
    private $disponible;
    private $imagen_url;
    private $categoria; 

    public function __construct($id, $id_cat, $nombre, $desc, $precio, $iva, $disp, $img, $categoria = null) {
        $this->id = $id;
        $this->id_categoria = $id_cat;
        $this->nombre = $nombre;
        $this->descripcion = $desc;
        $this->precio_base = $precio;
        $this->iva = $iva;
        $this->disponible = $disp;
        $this->imagen_url = $img;
        $this->categoria = $categoria;
    }


    public static function buscaPorId($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT * FROM Productos WHERE id=%d", intval($id));
        $rs = $conn->query($query);
        $result = null;
        if ($rs && $f = $rs->fetch_assoc()) {
            $result = self::creaDesdeFila($f);
            $rs->free();
        }
        return $result;
    }

    public static function listar($soloOfertados = false, $idCategoria = null) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = "SELECT p.*, c.nombre as cat_nom 
                  FROM Productos p 
                  JOIN Categorias c ON p.id_categoria = c.id 
                  WHERE 1=1";

        if ($soloOfertados) {
            $query .= " AND p.disponible = 1 AND p.ofertado = 1";
        }

        if ($idCategoria) {
            $query .= " AND p.id_categoria = " . intval($idCategoria);
        }
        $query .= " ORDER BY c.nombre, p.nombre";

        $rs = $conn->query($query);
        $productos = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) {
                $productos[] = self::creaDesdeFila($f);
            }
            $rs->free();
        }
        return $productos;
    }

    private static function creaDesdeFila($f) {
        return new Producto(
            $f['id'],
            $f['id_categoria'],
            $f['nombre'],
            $f['descripcion'],
            $f['precio_base'],
            $f['iva'],
            $f['disponible'],
            $f['imagen_url'],
            $f['cat_nom'] ?? null
        );
    }

    public static function borrar($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $idLimpiado = intval($id);
        $query = "DELETE FROM Productos WHERE id = $idLimpiado";
        
        return $conn->query($query);
    }

    public static function guardaOActualiza($datos) {
        $conn = Aplicacion::getInstance()->conexionBd();
        
        $id_cat = intval($datos['id_categoria']);
        $nombre = $conn->real_escape_string($datos['nombre']);
        $desc = $conn->real_escape_string($datos['descripcion']);
        $precio = floatval($datos['precio_base']);
        $iva = $conn->real_escape_string($datos['iva']);
        $disp = isset($datos['disponible']) ? 1 : 0;
        $img = $conn->real_escape_string($datos['imagen_url'] ?: 'productos/default.jpg');

        if (isset($datos['id']) && $datos['id'] != null) {
            $query = "UPDATE Productos SET id_categoria=$id_cat, nombre='$nombre', descripcion='$desc', precio_base=$precio, iva='$iva', disponible=$disp, imagen_url='$img' WHERE id=" . intval($datos['id']);
        } else {
            $query = "INSERT INTO Productos (id_categoria, nombre, descripcion, precio_base, iva, disponible, imagen_url, ofertado) VALUES ($id_cat, '$nombre', '$desc', $precio, '$iva', $disp, '$img', 1)";
        }

        return $conn->query($query);
    }


    public function getId() { return $this->id; }
    public function getIdCategoria() { return $this->id_categoria; }
    public function getNombre() { return $this->nombre; }
    public function getPrecioBase() { return $this->precio_base; }
    public function getIva() { return $this->iva; }
    public function getCatNom() { return $this->categoria; }
    public function getImagenUrl() { return $this->imagen_url; }
    public function getDescripcion() { return $this->descripcion; }
    public function getDisponible() { return $this->disponible; }
    
    public function getPrecioFinal() {
        return $this->precio_base * (1 + ($this->iva / 100));
    }
}

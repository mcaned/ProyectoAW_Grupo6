<?php 
require_once __DIR__ . '/aplicacion.php';

class Oferta{
    private $id;
    private $nombre;
    private $descripcion;
    private $productos;
    private $comienzo;
    private $fin;
    private $descuento;

    public function __construct($id, $nombre, $descripcion, $productos , $comienzo, $fin, $descuento){
        $this->id = $id; 
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->productos = $productos;
        $this->comienzo = $comienzo;
        $this->fin = $fin;
        $this->descuento = $descuento;
    }

    public static function listarActivas() {
        $conn = Aplicacion::getInstance()->conexionBd();
        $ahora = date('Y-m-d H:i:s');
        $query = "SELECT * FROM Ofertas WHERE comienzo <= '$ahora' AND fin >= '$ahora'";
        $rs = $conn->query($query);
        $ofertas = [];
        if ($rs){
            while ($rs && $f = $rs->fetch_assoc()) {
                $ofertas[] = self::buscaPorId($f['id']);
            }
            $rs->free();
        }
        return $ofertas; 
    }

    public static function buscaPorId($id) {
        $conn = Aplicacion::getInstance()->conexionBd();

        $query = sprintf("SELECT * FROM Ofertas WHERE id=%d", intval($id));
        $rs = $conn->query($query);

        if ($f = $rs->fetch_assoc()) {
            $productos = self::getRelacionProductos($f['id']);
            $rs->free();
            return new Oferta($f['id'], $f['nombre'], $f['descripcion'], $productos, $f['comienzo'], $f['fin'], $f['descuento']);   
        }
        return null;
    }

    private static function getRelacionProductos($idOferta) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT id_producto, cantidad FROM Ofertas_Productos WHERE id_oferta=%d", $idOferta);
        $rs = $conn->query($query);
        $productos = [];
        while ($f = $rs->fetch_assoc()) {
            $productos[] = new OfertaProducto($f['id_producto'], $f['cantidad']);
        }
        $rs->free();
        return $productos;
    }

    public static function listarTodas() {
        $conn = Aplicacion::getInstance()->conexionBd();
        
        $query = "SELECT id FROM Ofertas ORDER BY comienzo DESC";
        $rs = $conn->query($query);
        
        $ofertas = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) {

                $ofertas[] = self::buscaPorId($f['id']);
            }
            $rs->free();
        }
        
        return $ofertas;
    }

    public static function borrar($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $id = intval($id);

        $conn->begin_transaction();

       
        $queryProductos = sprintf("DELETE FROM Ofertas_Productos WHERE id_oferta = %d", $id);
        $rsProductos = $conn->query($queryProductos);

        if ($rsProductos) {
            $queryOferta = sprintf("DELETE FROM Ofertas WHERE id = %d", $id);
            $rsOferta = $conn->query($queryOferta);

            if ($rsOferta) {
                $conn->commit();
                return true;
            }
        }

        $conn->rollback();
        return false;
    }


    public static function guardaOActualiza($datos) {
        $conn = Aplicacion::getInstance()->conexionBd();
        
        $id = isset($datos['id']) ? intval($datos['id']) : null;
        $nombre = $conn->real_escape_string($datos['nombre']);
        $descripcion = $conn->real_escape_string($datos['descripcion']);
        $comienzo = $conn->real_escape_string($datos['comienzo']);
        $fin = $conn->real_escape_string($datos['fin']);
        $descuento = floatval($datos['descuento']);
        $productosIds = $datos['productos'] ?? [];

        $conn->begin_transaction();
        $error = false;

        if ($id) {
            $query = sprintf("UPDATE Ofertas SET nombre='%s', descripcion='%s', comienzo='%s', fin='%s', descuento=%f WHERE id=%d",
                $nombre, $descripcion, $comienzo, $fin, $descuento, $id);
            
            if (!$conn->query($query)) { $error = true; }
            
            if (!$error && !$conn->query("DELETE FROM Ofertas_Productos WHERE id_oferta = $id")) {
                $error = true;
            }
            $idOferta = $id;
        } else {
            $query = sprintf("INSERT INTO Ofertas (nombre, descripcion, comienzo, fin, descuento) VALUES ('%s', '%s', '%s', '%s', %f)",
                $nombre, $descripcion, $comienzo, $fin, $descuento);
            
            if ($conn->query($query)) {
                $idOferta = $conn->insert_id;
            } else {
                $error = true;
            }
        }

        if (!$error) {
            foreach ($productosIds as $pId) {
                $cantidad = intval($datos["cantidad_$pId"] ?? 1);
                $queryProd = sprintf("INSERT INTO Ofertas_Productos (id_oferta, id_producto, cantidad) VALUES (%d, %d, %d)",
                    $idOferta, intval($pId), $cantidad);
                
                if (!$conn->query($queryProd)) {
                    $error = true;
                    break;
                }
            }
        }
        if ($error) {
            $conn->rollback();
            return false;
        } else {
            $conn->commit();
            return true;
        }
    }


    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescuento() { return $this->descuento; }
    public function getProductos() { return $this->productos; }
    public function getDescripcion() { return $this->descripcion; }
    public function getComienzo() { return $this->comienzo; }
    public function getFin() { return $this->fin; }

}

class OfertaProducto {
    private $id_producto;
    private $cantidad;

    public function __construct($id_producto, $cantidad) {
        $this->id_producto = $id_producto;
        $this->cantidad = $cantidad;
    }

    public function getIdProducto() {
        return $this->id_producto;
    }

    public function getCantidad() {
        return $this->cantidad;
    }
}

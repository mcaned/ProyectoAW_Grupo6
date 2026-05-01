<?php
require_once __DIR__ . '/aplicacion.php';

class LineaPedido {
    private $id_pedido;
    private $id_producto;
    private $cantidad;
    private $preparado; 

    public function __construct($id_pedido, $id_producto, $cantidad, $preparado = 0) {
        $this->id_pedido = $id_pedido;
        $this->id_producto = $id_producto;
        $this->cantidad = $cantidad;
        $this->preparado = $preparado;
    }

    public static function alternarPreparado($id_pedido, $id_producto) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("UPDATE Lineas_Pedido SET preparado = NOT preparado WHERE id_pedido = %d AND id_producto = %d", 
            $id_pedido, 
            $id_producto
        );
        return $conn->query($query);
    }

    public static function buscaPorPedido($id_pedido) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT * FROM lineas_pedido WHERE id_pedido = %d", intval($id_pedido));
        $rs = $conn->query($query);
        $lineas = [];
        while ($rs && $f = $rs->fetch_assoc()) {
            $lineas[] = new LineaPedido($f['id_pedido'], $f['id_producto'], $f['cantidad'], $f['preparado']);
        }
        $rs->free():
        return $lineas;
    }

    // Getters
    public function getIdPedido() { return $this->id_pedido; }
    public function getIdProducto() { return $this->id_producto; }
    public function getCantidad() { return $this->cantidad; }
    public function estaPreparado() { return $this->preparado == 1; }
    public function getProducto() {
        return Producto::buscaPorId($this->id_producto);
    }
}
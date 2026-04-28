<?php
require_once __DIR__ . '/aplicacion.php';

class Pedido {
    
    private $id;
    private $num_pedido;
    private $id_cliente;
    private $nombreCliente;//extra
    private $id_cocinero;
    private $fechahora;
    private $tipo;
    private $estado;
    private $total; 

    public function __construct($id, $num_pedido, $id_cliente, $id_cocinero, $fechahora, $tipo, $estado, $total, $nombreCliente = null) {
        $this->id = $id;
        $this->num_pedido = $num_pedido;
        $this->id_cliente = $id_cliente;
        $this->id_cocinero = $id_cocinero;
        $this->fechahora = $fechahora;
        $this->tipo = $tipo;
        $this->disponible = $disp;
        $this->estado = $estado;
        $this->total = $total;
        $this->nombreCliente = $nombreCliente;
    }


    public static function buscaPorId($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT * FROM pedidos WHERE id=%d", intval($id));
        $rs = $conn->query($query);
        $result = null;
        if ($rs && $f = $rs->fetch_assoc()) {
            $result = self::creaDesdeFila($f);
            $rs->free();
        }
        return $result;
    }

    public static function listar() {
       $conn = Aplicacion::getInstance()->conexionBd();
        $query = "SELECT p.*, u.nombre as nombre_cliente 
                  FROM Pedidos p 
                  JOIN Usuarios u ON p.id_cliente = u.id 
                  ORDER BY p.fecha_hora DESC";
        
        $rs = $conn->query($query);
        $pedidos = [];
        if ($rs) {
            while ($f = $rs->fetch_assoc()) {
                $pedidos[] = self::creaDesdeFila($f);
            }
            $rs->free();
        }
        return $pedidos;
    }

    public static function crear($id_cliente, $tipo, $carrito) {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();

        $total = 0;
        foreach ($carrito as $id_prod => $cantidad) {
            $p = Producto::buscaPorId($id_prod);
            if ($p) $total += $p->getPrecioFinal() * $cantidad;
        }

        $resNum = $conn->query("SELECT MAX(numero_pedido) as ultimo FROM pedidos WHERE DATE(fecha_hora) = CURDATE()");
        $filaNum = $resNum->fetch_assoc();
        $nuevo_num = ($filaNum['ultimo'] ?? 0) + 1;

        $queryPedido = sprintf(
            "INSERT INTO pedidos (numero_pedido, id_cliente, tipo, estado, total) VALUES (%d, %d, '%s', 'Recibido', %F)",
            $nuevo_num, $id_cliente, $conn->real_escape_string($tipo), $total
        );

        if ($conn->query($queryPedido)) {
            $id_pedido = $conn->insert_id;
            foreach ($carrito as $id_prod => $cantidad) {
                $queryLinea = sprintf("INSERT INTO lineas_pedido (id_pedido, id_producto, cantidad) VALUES (%d, %d, %d)",
                    $id_pedido, $id_prod, $cantidad);
                $conn->query($queryLinea);
            }
            return $id_pedido;
        }
        return false;
    }

    private static function creaDesdeFila($f) {
        return new Pedido(
            $f['id'],
            $f['num_pedido'],
            $f['id_cliente'],
            $f['id_cocinero'] ?? null,
            $f['fechahora'],
            $f['tipo'],
            $f['estado'],
            $f['total'],
            $f['nombre_cliente'] ?? null
        );
    }

    public static function borrar($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = "DELETE FROM pedidos WHERE id = $intval($id)";
        
        return $conn->query($query);
    }



    public function getId() { return $this->id; }
    public function getNumpedido() { return $this->num_pedido; }
    public function getIdCliente() { return $this->id_cliente; }
    public function getIdCocinero() { return $this->id_cocinero; }
    public function getfechahora() { return $this->fechahora; }
    public function getTipo() { return $this->tipo; }
    public function getEstado() { return $this->estado; }
    public function getTotal() { return $this->total; }
    public function getNombreCliente() { return $this->nombreCliente; }
    
}
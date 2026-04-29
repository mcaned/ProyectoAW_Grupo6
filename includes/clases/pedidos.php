<?php
require_once __DIR__ . '/aplicacion.php';

class Pedido {
    
    private $id;
    private $numero_pedido;
    private $id_cliente;
    private $nombreCliente;//extra
    private $id_cocinero;
    private $fecha_hora;
    private $tipo;
    private $estado;
    private $total; 

    public function __construct($id, $numero_pedido, $id_cliente, $id_cocinero, $fecha_hora, $tipo, $estado, $total, $nombreCliente = null) {
        $this->id = $id;
        $this->numero_pedido = $numero_pedido;
        $this->id_cliente = $id_cliente;
        $this->id_cocinero = $id_cocinero;
        $this->fecha_hora = $fecha_hora;
        $this->tipo = $tipo;
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
            $f['numero_pedido'],
            $f['id_cliente'],
            $f['id_cocinero'] ?? null,
            $f['fecha_hora'],
            $f['tipo'],
            $f['estado'],
            $f['total'],
            $f['nombre_cliente'] ?? null
        );
    }

    public static function borrar($id) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = "DELETE FROM pedidos WHERE id = intval($id)";
        
        return $conn->query($query);
    }

    public static function actualizarEstado($id_pedido, $nuevo_estado) {
        $conn = Aplicacion::getInstance()->conexionBd();
        
        $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
        $id_pedido = intval($id_pedido);
        $stmt->bind_param("si", $nuevo_estado, $id_pedido);
        
        $ok = $stmt->execute();
        $stmt->close();
    
        return $ok;
    }

    public static function buscaPorUsuario($idUsuario) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT p.*, u.nombre as nombre_cliente 
                          FROM pedidos p 
                          JOIN usuarios u ON p.id_cliente = u.id 
                          WHERE p.id_cliente = %d 
                          ORDER BY p.fecha_hora DESC", intval($idUsuario));
        
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

    public static function buscarPedidosEnGestion() {
        $conn = Aplicacion::getInstance()->conexionBd();
       
        $estados = "('Recibido', 'En preparación', 'Listo cocina', 'Terminado')";
        
        $query = "SELECT * FROM pedidos WHERE estado IN $estados ORDER BY fecha_hora ASC";
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


    public static function tomarPedido($id_pedido, $id_cocinero) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $stmt = $conn->prepare("UPDATE pedidos SET estado = 'Cocinando', id_cocinero = ? WHERE id = ? AND estado = 'En preparación'");
        $stmt->bind_param("ii", $id_cocinero, $id_pedido);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    
    public static function finalizarPedido($id_pedido, $id_cocinero) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $stmt = $conn->prepare("UPDATE pedidos SET estado = 'Listo cocina' WHERE id = ? AND id_cocinero = ?");
        $stmt->bind_param("ii", $id_pedido, $id_cocinero);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public static function borrarPorCliente($idCliente) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $idCliente = intval($idCliente);
        
        $queryLineas = "DELETE FROM lineas_pedido WHERE id_pedido IN (SELECT id FROM Pedidos WHERE id_cliente = $idCliente)";
        $conn->query($queryLineas);
        
        $queryPedidos = "DELETE FROM pedidos WHERE id_cliente = $idCliente";
        return $conn->query($queryPedidos);
    }

    // Método extendido para el detalle con información del cliente y cocinero
    public static function buscaDetallePorId($id) { //detallePedido
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT p.*, u.nombre as nombre_cliente, u.apellidos as apellidos_cliente, u.email as email_cliente,
                                c.nombre as nombre_cocinero, c.avatar_url as avatar_cocinero 
                        FROM pedidos p 
                        JOIN usuarios u ON p.id_cliente = u.id 
                        LEFT JOIN usuarios c ON p.id_cocinero = c.id
                        WHERE p.id = %d", intval($id));
        $rs = $conn->query($query);
        if ($rs && $f = $rs->fetch_assoc()) {
            $p = self::creaDesdeFila($f);
            // Guardamos datos extra dinámicamente o mediante propiedades nuevas
            $p->nombreCliente = $f['nombre_cliente'] . ' ' . $f['apellidos_cliente'];
            $p->emailCliente = $f['email_cliente'];
            $p->nombreCocinero = $f['nombre_cocinero'];
            $p->avatarCocinero = $f['avatar_cocinero'];
            return $p;
        }
        return null;
    }


    public static function buscarPedidosPorEstado($estado, $idCocinero = null) {    
        $conn = Aplicacion::getInstance()->conexionBd(); // Base de la consulta 
        if ($idCocinero !== null) { 
            $query = sprintf( "SELECT * FROM pedidos WHERE estado = '%s' AND id_cocinero = %d ORDER BY fecha_hora ASC", 
            $conn->real_escape_string($estado), intval($idCocinero) ); 
        } 
        else { 
            $query = sprintf( "SELECT * FROM pedidos WHERE estado = '%s' ORDER BY fecha_hora ASC", 
            $conn->real_escape_string($estado)); 
        } 
        $rs = $conn->query($query); 
        $pedidos = []; 
        if ($rs) { 
            while ($f = $rs->fetch_assoc()) { // Usamos el método creaDesdeFila para convertir cada fila en un objeto Pedido
                $pedidos[] = self::creaDesdeFila($f); 
            } 
            $rs->free(); 
        } 
        return $pedidos; 
    }  

    public function getLineas() { //detallePedido
        return LineaPedido::buscaPorPedido($this->id);
    }
    public function getCliente() {
        return Usuario::buscaPorId($this->id_cliente);
    }
    public function getCocinero() {
        if (!$this->id_cocinero) return null;
        return Usuario::buscaPorId($this->id_cocinero);
    }
    public function getId() { return $this->id; }
    public function getNumpedido() { return $this->numero_pedido; }
    public function getIdCliente() { return $this->id_cliente; }
    public function getIdCocinero() { return $this->id_cocinero; }
    public function getfechahora() { return $this->fecha_hora; }
    public function getTipo() { return $this->tipo; }
    public function getEstado() { return $this->estado; }
    public function getTotal() { return $this->total; }
    public function getNombreCliente() { return $this->nombreCliente; }
    
}
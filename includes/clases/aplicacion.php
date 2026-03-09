<?php
class Aplicacion {
    private static $instancia = null;
    private $conn;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instancia === null) self::$instancia = new self();
        return self::$instancia;
    }

    public function conexionBd() {
        if (!$this->conn) {
            $this->conn = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);
            if ($this->conn->connect_error) die("Error BD: " . $this->conn->connect_error);
            $this->conn->set_charset("utf8mb4");
        }
        return $this->conn;
    }

    public function init() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }
}
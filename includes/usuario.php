<?php
class Usuario {
    private $id;
    private $nombreUsuario;
    private $password;
    private $rol;

    private function __construct($nombreUsuario, $password, $rol, $id = null) {
        $this->id = $id; $this->nombreUsuario = $nombreUsuario;
        $this->password = $password; $this->rol = $rol;
    }

    public static function login($nombreUsuario, $password) {
        $user = self::buscaUsuario($nombreUsuario);
        if ($user && password_verify($password, $user->password)) return $user;
        return false;
    }

    public static function buscaUsuario($nombreUsuario) {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("SELECT * FROM usuarios WHERE nombreUsuario='%s'", $conn->real_escape_string($nombreUsuario));
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows == 1) {
            $f = $rs->fetch_assoc();
            return new Usuario($f['nombreUsuario'], $f['password'], $f['rol'] ?? 'user', $f['id']);
        }
        return false;
    }

    public static function crea($nombreUsuario, $password, $rol = 'user') {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("INSERT INTO usuarios(nombreUsuario, password, rol) VALUES ('%s','%s','%s')",
            $conn->real_escape_string($nombreUsuario), password_hash($password, PASSWORD_DEFAULT), $rol);
        return $conn->query($query) ? new Usuario($nombreUsuario, $password, $rol, $conn->insert_id) : false;
    }

    public function getNombreUsuario() { return $this->nombreUsuario; }
    public function getRol() { return $this->rol; }
}
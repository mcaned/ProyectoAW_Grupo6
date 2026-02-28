<?php
require_once __DIR__ . '/Aplicacion.php';

class Usuario {
    private $id;
    private $username;
    private $password;
    private $rol;

    private function __construct($username, $password, $rol, $id = null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->rol = $rol;
    }

    public static function login($username, $password) {
        $user = self::buscaUsuario($username);
        // Usamos la columna password_hash de tu tabla
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public static function buscaUsuario($username) {
        $conn = Aplicacion::getInstance()->conexionBd();
        // Usamos tu columna 'username' y tu tabla 'Usuarios' (en mayúscula si así está en el SQL)
        $query = sprintf("SELECT * FROM Usuarios WHERE username='%s'", $conn->real_escape_string($username));
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows == 1) {
            $f = $rs->fetch_assoc();
            return new Usuario($f['username'], $f['password_hash'], $f['rol'], $f['id']);
        }
        return false;
    }

    // Adaptado a todos los campos de tu nueva tabla
    public static function crea($username, $password, $email, $nombre, $apellidos, $rol = 'cliente') {
    $conn = Aplicacion::getInstance()->conexionBd();
    $query = sprintf("INSERT INTO Usuarios(username, email, nombre, apellidos, password_hash, rol) VALUES ('%s','%s','%s','%s','%s','%s')",
        $conn->real_escape_string($username),
        $conn->real_escape_string($email),
        $conn->real_escape_string($nombre),
        $conn->real_escape_string($apellidos),
        password_hash($password, PASSWORD_DEFAULT),
        $rol
    );
    
    if ($conn->query($query)) {
        return new Usuario($username, $password, $rol, $conn->insert_id);
    }
    return false;
}

    public function getNombreUsuario() { return $this->username; }
    public function getRol() { return $this->rol; }
}
<?php
class Usuario {
    // Definición de la jerarquía requerida por el proyecto
    private static $JERARQUIA_ROLES = [
        'cliente'  => 0,
        'camarero' => 1,
        'cocinero' => 2,
        'gerente'  => 3
    ];

    private $id;
    private $nombreUsuario;
    private $password;
    private $nombre;
    private $apellidos;
    private $email;
    private $rol;
    private $avatar;

    private function __construct($nombreUsuario, $password, $nombre, $apellidos, $email, $rol, $avatar, $id = null) {
        $this->id = $id; 
        $this->nombreUsuario = $nombreUsuario;
        $this->password = $password; 
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->rol = $rol;
        $this->avatar = $avatar;
    }

    public static function login($nombreUsuario, $password) {
        $user = self::buscaUsuario($nombreUsuario);
        if ($user && password_verify($password, $user->password)) return $user;
        return false;
    }

    public static function buscaUsuario($nombreUsuario) {
        $conn = Aplicacion::getInstance()->conexionBd();
        // Ajustado a tus columnas SQL: 'username'
        $query = sprintf("SELECT * FROM Usuarios WHERE username='%s'", $conn->real_escape_string($nombreUsuario));
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows == 1) {
            $f = $rs->fetch_assoc();
            return new Usuario(
                $f['username'], 
                $f['password_hash'], // Ajustado a tu columna 'password_hash'
                $f['nombre'], 
                $f['apellidos'], 
                $f['email'], 
                $f['rol'], 
                $f['avatar_url'], // Ajustado a tu columna 'avatar_url'
                $f['id']
            );
        }
        return false;
    }

    public static function crea($nombreUsuario, $password, $nombre, $apellidos, $email, $rol = 'cliente', $avatar = 'defecto.png') {
        $user = self::buscaUsuario($nombreUsuario);
        if ($user) return false; 

        $conn = Aplicacion::getInstance()->conexionBd();
        // Insertamos usando los nombres exactos de tu tabla 'Usuarios'
        $query = sprintf("INSERT INTO Usuarios(username, password_hash, nombre, apellidos, email, rol, avatar_url) VALUES ('%s','%s','%s','%s','%s','%s','%s')",
            $conn->real_escape_string($nombreUsuario), 
            password_hash($password, PASSWORD_DEFAULT), 
            $conn->real_escape_string($nombre),
            $conn->real_escape_string($apellidos),
            $conn->real_escape_string($email),
            $conn->real_escape_string($rol),
            $conn->real_escape_string($avatar)
        );
        
        if ($conn->query($query)) {
            return new Usuario($nombreUsuario, $password, $nombre, $apellidos, $email, $rol, $avatar, $conn->insert_id);
        }
        return false;
    }

    public function tieneRol($rolRequerido) {
        // Comprueba si el nivel del rol actual es igual o superior al requerido
        return self::$JERARQUIA_ROLES[$this->rol] >= self::$JERARQUIA_ROLES[$rolRequerido];
    }

    public static function buscaPorEmail($email) {
    $conn = Aplicacion::getInstance()->conexionBd();
    $query = sprintf("SELECT * FROM Usuarios WHERE email='%s'", $conn->real_escape_string($email));
    $rs = $conn->query($query);
    if ($rs && $rs->num_rows == 1) {
        return true;
    }
    return false;
    }

    public function actualiza() {
        $conn = Aplicacion::getInstance()->conexionBd();
        // Actualizamos usando tus nombres de columna SQL
        $query = sprintf("UPDATE Usuarios SET nombre='%s', apellidos='%s', email='%s', rol='%s', avatar_url='%s' WHERE id=%d",
            $conn->real_escape_string($this->nombre),
            $conn->real_escape_string($this->apellidos),
            $conn->real_escape_string($this->email),
            $conn->real_escape_string($this->rol),
            $conn->real_escape_string($this->avatar),
            $this->id
        );
        return $conn->query($query);
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombreUsuario() { return $this->nombreUsuario; }
    public function getRol() { return $this->rol; }
    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getEmail() { return $this->email; }
    public function getAvatar() { return $this->avatar; }

    // Setters
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    public function setEmail($email) { $this->email = $email; }
    public function setRol($rol) { $this->rol = $rol; }
    public function setAvatar($avatar) { $this->avatar = $avatar; }
}
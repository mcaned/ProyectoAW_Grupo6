<?php
class Usuario {
    private $id;
    private $nombreUsuario;
    private $password;
    private $nombre;
    private $apellidos;
    private $email;
    private $rol;
    private $avatar;

    private function __construct($nombreUsuario, $password, $nombre, $apellidos, $email, $rol, $avatar, $id = null) {
        $this->id = $id; $this->nombreUsuario = $nombreUsuario;
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
        $query = sprintf("SELECT * FROM usuarios WHERE nombreUsuario='%s'", $conn->real_escape_string($nombreUsuario));
        $rs = $conn->query($query);
        if ($rs && $rs->num_rows == 1) {
            $f = $rs->fetch_assoc();
            return new Usuario(
                $f['nombreUsuario'], 
                $f['password'], 
                $f['nombre'], 
                $f['apellidos'], 
                $f['email'], 
                $f['rol'], 
                $f['avatar'], 
                $f['id']
            );
        }
        return false;
    }

    public static function crea($nombreUsuario, $password, $nombre, $apellidos, $email, $rol = 'user', $avatar = 'defecto.png') {
        $user = self::buscaUsuario($nombreUsuario);
        if ($user) return false; //si el usu ya existe
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("INSERT INTO usuarios(nombreUsuario, password, nombre, apellidos, email, rol, avatar) VALUES ('%s','%s','%s','%s','%s','%s','%s')",
            $conn->real_escape_string($nombreUsuario), password_hash($password, PASSWORD_DEFAULT), $conn->real_escape_string($nombre),
            $conn->real_escape_string($apellidos),
            $conn->real_escape_string($email),
            $conn->real_escape_string($rol),
            $conn->real_escape_string($avatar)
            );
        return $conn->query($query) ? new Usuario($nombreUsuario, $password, $nombre, $apellidos, $email, $rol, $avatar, $conn->insert_id) : false;
    }

    public function getNombreUsuario() { return $this->nombreUsuario; }
    public function getRol() { return $this->rol; }
    public function tieneRol($rolRequerido) {
        return self::$JERARQUIA_ROLES[$this->rol] >= self::$JERARQUIA_ROLES[$rolRequerido];
    }

    public function actualiza() {
        $conn = Aplicacion::getInstance()->conexionBd();
        $query = sprintf("UPDATE usuarios SET nombre='%s', apellidos='%s', email='%s', rol='%s', avatar='%s' WHERE id=%d",
            $conn->real_escape_string($this->nombre),
            $conn->real_escape_string($this->apellidos),
            $conn->real_escape_string($this->email),
            $conn->real_escape_string($this->rol),
            $conn->real_escape_string($this->avatar),
            $this->id
        );
        return $conn->query($query);
    }

    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getApellidos() { return $this->apellidos; }
    public function getEmail() { return $this->email; }
    public function getAvatar() { return $this->avatar; }


    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellidos($apellidos) { $this->apellidos = $apellidos; }
    public function setEmail($email) { $this->email = $email; }
    public function setRol($rol) { $this->rol = $rol; }
    public function setAvatar($avatar) { $this->avatar = $avatar; }
}

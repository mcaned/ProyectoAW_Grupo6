<?php
require_once __DIR__ . '/Formulario.php';
require_once __DIR__ . '/Usuario.php';

class FormularioRegistro extends Formulario {
    public function __construct() {
        parent::__construct('formRegistro', ['action' => 'registro.php']);
    }

    protected function generaCamposFormulario($datosIniciales) {
        return <<<EOF
        <div style="border: 1px solid #999; padding: 25px; width: 450px; background-color: #fff;">
            <legend style="background: #333; color: white; padding: 2px 10px;">Registro de Usuario</legend>
            <br>
            <label>Username:</label><br><input type="text" name="username" required><br>
            <label>Email:</label><br><input type="email" name="email" required><br>
            <label>Nombre:</label><br><input type="text" name="nombre" required><br>
            <label>Apellidos:</label><br><input type="text" name="apellidos" required><br>
            <label>Password:</label><br><input type="password" name="password" required><br><br>
            <button type="submit">Registrarme</button>
        </div>
EOF;
    }

    protected function procesaFormulario($datos) {
        $username = $datos['username'] ?? null;
        $password = $datos['password'] ?? null;
        $email = $datos['email'] ?? null;
        $nombre = $datos['nombre'] ?? null;
        $apellidos = $datos['apellidos'] ?? null;

        if (Usuario::buscaUsuario($username)) {
            return ["El nombre de usuario ya existe."];
        }

        // Llamamos a la función con TODOS los parámetros que pide tu nueva tabla
        $nuevoUsuario = Usuario::crea($username, $password, $email, $nombre, $apellidos);
        
        if ($nuevoUsuario) {
            header('Location: login.php?registro=exito');
            exit();
        }
        return ["Error al crear el usuario. Revisa que el email no esté repetido."];
    }
}
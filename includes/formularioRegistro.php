<?php

require_once 'Formulario.php';
require_once 'Aplicacion.php';

class FormularioRegistro extends Formulario {
    public function __construct() {

        parent::__construct('formRegistro', ['action' => 'registro.php']);
    }

    protected function generaCamposFormulario($datosIniciales) {
        return <<<EOF
        <div style="border: 1px solid #999; padding: 25px; margin-top: 10px; position: relative; width: 450px; background-color: #fff;">
            <span style="position: absolute; top: -12px; left: 15px; background: #333; color: white; padding: 2px 10px; font-size: 0.85rem; font-weight: bold;">
                Datos de registro
            </span>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Nombre de usuario:</label>
                <input type="text" name="nombreUsuario" required style="width: 250px; padding: 3px; border: 1px solid #777;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Password:</label>
                <input type="password" name="password" required style="width: 250px; padding: 3px; border: 1px solid #000;">
            </div>
            
            <button type="submit" style="padding: 3px 20px; cursor: pointer; background-color: #f0f0f0; border: 1px solid #777; font-size: 0.9rem;">
                Registrarme
            </button>
        </div>
EOF;
    }

    protected function procesaFormulario($datos) {
        $errores = [];
        $user = $datos['nombreUsuario'] ?? null;
        $pass = $datos['password'] ?? null;

        if (empty($user) || empty($pass)) {
            $errores[] = "El usuario y la contraseña son obligatorios.";
        }

        if (count($errores) === 0) {
            $app = Aplicacion::getInstance();
            $conn = $app->conexionBd();

            $user = $conn->real_escape_string($user);
            $passwordHash = password_hash($pass, PASSWORD_DEFAULT);

            $query = "INSERT INTO usuarios (nombreUsuario, password) VALUES ('$user', '$passwordHash')";
            
            if ($conn->query($query)) {
                header('Location: login.php?registro=exito');
                exit();
            } else {
                $errores[] = "Error al registrar el usuario: El nombre ya podría estar en uso.";
            }
        }
        return $errores;
    }
}
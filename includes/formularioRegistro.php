<?php
require_once 'Formulario.php';
require_once 'Aplicacion.php';

class FormularioRegistro extends Formulario {
    public function __construct() {
        parent::__construct('formRegistro');
    }

    protected function generaCamposFormulario($datosIniciales) {
        return <<<EOF
        <fieldset style="border: 1px solid #999; padding: 25px; width: 450px;">
            <legend style="background: #333; color: white; padding: 2px 10px;">Crea tu cuenta</legend>
            <div><label>Usuario:</label><br><input type="text" name="nombreUsuario" required></div>
            <div><label>Password:</label><br><input type="password" name="password" required></div>
            <br><button type="submit">Registrarme</button>
        </fieldset>
EOF;
    }

    protected function procesaFormulario($datos) {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();
        
        $user = $conn->real_escape_string($datos['nombreUsuario']);
        $pass = password_hash($datos['password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO usuarios (nombreUsuario, password) VALUES ('$user', '$pass')";
        if ($conn->query($query)) {
            return "Registro completado. <a href='login.php'>Haz login aquí</a>";
        }
        return ["Error al registrar el usuario"];
    }
}
<?php

require_once 'Formulario.php';
require_once 'Aplicacion.php';

class FormularioLogin extends Formulario {
    public function __construct() {
        parent::__construct('formLogin');
    }

    protected function generaCamposFormulario($datosIniciales) {
        return <<<EOF
        <fieldset style="border: 1px solid #999; padding: 25px; width: 450px; background-color: #fff; position: relative;">
            <legend style="background: #333; color: white; padding: 2px 10px; font-weight: bold;">Usuario y contraseña</legend>
            <div style="margin-bottom: 10px;">
                <label>Nombre de usuario:</label><br>
                <input type="text" name="nombreUsuario" required style="width: 180px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Password:</label><br>
                <input type="password" name="password" required style="width: 180px;">
            </div>
            <button type="submit">Entrar</button>
        </fieldset>
EOF;
    }

    protected function procesaFormulario($datos) {
        $errores = [];
        $user = $datos['nombreUsuario'] ?? '';
        $pass = $datos['password'] ?? '';

        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();

        $query = "SELECT * FROM usuarios WHERE nombreUsuario = '$user'";
        $rs = $conn->query($query);

        if ($rs && $rs->num_rows == 1) {
            $fila = $rs->fetch_assoc();
            if (password_verify($pass, $fila['password'])) {
                $_SESSION['login'] = true;
                $_SESSION['nombre'] = $fila['nombreUsuario'];
                header('Location: index.php');
                exit();
            }
        }
        $errores[] = "Usuario o contraseña incorrectos.";
        return $errores;
    }
}
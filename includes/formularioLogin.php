<?php
require_once __DIR__ . '/Formulario.php';
require_once __DIR__ . '/Usuario.php';

class FormularioLogin extends Formulario {
    public function __construct() { 
        parent::__construct('formLogin'); 
    }

    protected function generaCamposFormulario($datosIniciales) {
        return <<<EOF
        <fieldset style="border: 1px solid #999; padding: 25px; width: 400px; background: #fff;">
            <legend style="background: #333; color: white; padding: 2px 10px;">Acceso</legend>
            <label>Usuario:</label><br><input type="text" name="nombreUsuario" required><br>
            <label>Password:</label><br><input type="password" name="password" required><br><br>
            <button type="submit">Entrar</button>
        </fieldset>
EOF;
    }

 protected function procesaFormulario($datos) {
    // 'nombreUsuario' es el name del input en tu HTML
    $user = Usuario::login($datos['nombreUsuario'], $datos['password']);
    
    if ($user) {
        $_SESSION['login'] = true;
        $_SESSION['nombre'] = $user->getNombreUsuario();
        $_SESSION['rol'] = $user->getRol();
        $_SESSION['idUsuario'] = $user->getId();
        header('Location: index.php');
        exit();
    }
    return ["Usuario o contraseña incorrectos"];
}
}
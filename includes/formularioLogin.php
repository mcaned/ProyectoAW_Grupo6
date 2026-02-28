<?php
require_once __DIR__ . '/formulario.php';
require_once __DIR__ . '/usuario.php';

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
    $user = Usuario::login($datos['nombreUsuario'], $datos['password']);
    
    if ($user) {
        $_SESSION['login'] = true;
        $_SESSION['nombre'] = $user->getNombreUsuario();
        $_SESSION['idUsuario'] = $user->getId();
        
        // Limpiamos el rol: quitamos espacios y pasamos a minúsculas
        $rol = strtolower(trim($user->getRol())); 
        $_SESSION['rol'] = $rol;

        // Redirección usando la ruta completa del proyecto
            
        switch ($rol) {
            case 'gerente':
                header('Location: ' . RUTA_APP . '/admin.php');
                break;
            case 'camarero':
                header('Location: ' . RUTA_APP . '/gestion_pedidos.php');
                break;
            case 'cocinero':
                header('Location: ' . RUTA_APP . '/cocina.php');
                break;
            default:
                header('Location: ' . RUTA_APP . '/index.php');
                break;
        }
        exit();
    }
    return ["Usuario o contraseña incorrectos"];
}
}
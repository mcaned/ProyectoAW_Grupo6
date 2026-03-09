<?php
require_once __DIR__ . '/formulario.php';
require_once __DIR__ . '/usuario.php';

class FormularioLogin extends Formulario {
    public function __construct() { 
        parent::__construct('formLogin'); 
    }

   protected function generaCamposFormulario($datosIniciales) {
    return <<<EOF
    <div class="tarjeta-formulario formulario-estandar">
        <fieldset>           
            <div class="item-barra-izquierda">
                <label><strong>Usuario:</strong></label>
                <input type="text" name="nombreUsuario" class="input-formulario" required placeholder="Tu usuario">
            </div>

            <div class="item-barra-izquierda">
                <label><strong>Password:</strong></label>
                <input type="password" name="password" class="input-formulario" required placeholder="********">
            </div>

            <div class="acciones-formulario">
                <button type="submit" class="btn-listo">
                    ENTRAR
                </button>
            </div>
        </fieldset>
    </div>
    EOF;
   
}
   protected function procesaFormulario($datos) {
    $user = Usuario::login($datos['nombreUsuario'], $datos['password']);
    
    if ($user) {
        $_SESSION['login'] = true;
        $_SESSION['nombre'] = $user->getNombreUsuario();
        $_SESSION['idUsuario'] = $user->getId();
        $_SESSION['avatar'] = $user->getAvatar() ? $user->getAvatar() : 'defecto.png';



        $rol = strtolower(trim($user->getRol())); 
        $_SESSION['rol'] = $rol;
            
        switch ($rol) { //segun el rol redirigimos
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

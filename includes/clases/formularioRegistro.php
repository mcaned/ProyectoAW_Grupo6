<?php
require_once __DIR__ . '/formulario.php';
require_once __DIR__ . '/usuario.php'; 

class FormularioRegistro extends Formulario {
    public function __construct() {
        parent::__construct('formRegistro', ['action' => 'registro.php']);
    }
protected function generaCamposFormulario($datosIniciales) {
    return <<<EOF
    <div class="tarjeta-formulario formulario-estandar">
        <fieldset>

            <div class="item-barra-izquierda">
                <label><strong>Nombre:</strong></label>
                <input type="text" name="nombre" class="input-formulario" required placeholder="Tu nombre">
            </div>

            <div class="item-barra-izquierda">
                <label><strong>Apellidos:</strong></label>
                <input type="text" name="apellidos" class="input-formulario" required placeholder="Tus apellidos">
            </div>

            <div class="item-barra-izquierda">
                <label><strong>Email:</strong></label>
                <input type="email" name="email" class="input-formulario" required placeholder="ejemplo@correo.com">
            </div>

            <div class="item-barra-izquierda">
                <label><strong>Nombre de usuario:</strong></label>
                <input type="text" name="nombreUsuario" class="input-formulario" required placeholder="Usuario único">
            </div>

            <div class="item-barra-izquierda">
                <label><strong>Password:</strong></label>
                <input type="password" name="password" class="input-formulario" required placeholder="Mínimo 4 caracteres">
            </div>

            <div class="item-barra-izquierda">
                <label><strong>Rol:</strong></label>
                <select name="rol" class="input-formulario">
                    <option value="cliente">Cliente</option>
                    <option value="camarero">Camarero</option>
                    <option value="cocinero">Cocinero</option>
                    <option value="gerente">Gerente</option>
                </select>
            </div>

            <div class="acciones-formulario">
                <button type="submit" class="btn-listo">
                    Registrarme ahora
                </button>
            </div>
        </fieldset>
    </div>
EOF;
}

   protected function procesaFormulario($datos) {
    $user = $datos['nombreUsuario'] ?? null;
    $pass = $datos['password'] ?? null;
    $nombre = $datos['nombre'] ?? null;
    $apellidos = $datos['apellidos'] ?? null;
    $email = $datos['email'] ?? null;
    $rol = $datos['rol'] ?? 'cliente';

    if (empty($user) || empty($pass) || empty($nombre) || empty($apellidos) || empty($email)) { 
        return ["Todos los campos son obligatorios"];
    }

    if (Usuario::buscaUsuario($user)) { 
        return ["El nombre de usuario ya está en uso"];
    }
    
    if (Usuario::buscaPorEmail($email)) {
        return ["Ese correo electrónico ya está registrado por otro usuario"];
    }
    $nuevoUsuario = Usuario::crea($user, $pass, $nombre, $apellidos, $email, $rol); 
    
    if ($nuevoUsuario) {
        header('Location: login.php?registro=exito');
        exit();
    }
    
    return ["Error al crear el usuario. Inténtalo de nuevo."];
}
}


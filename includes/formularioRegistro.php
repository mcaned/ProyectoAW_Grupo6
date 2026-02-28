<?php
require_once __DIR__ . '/formulario.php';
require_once __DIR__ . '/usuario.php'; 

class FormularioRegistro extends Formulario {
    public function __construct() {
        parent::__construct('formRegistro', ['action' => 'registro.php']);
    }

    protected function generaCamposFormulario($datosIniciales) {
        // Mantenemos el estilo visual que ya tenías
        return <<<EOF
        <div style="border: 1px solid #999; padding: 25px; margin-top: 10px; position: relative; width: 450px; background-color: #fff; font-family: sans-serif;">
            <span style="position: absolute; top: -12px; left: 15px; background: #333; color: white; padding: 2px 10px; font-size: 0.85rem; font-weight: bold;">
                Datos de registro
            </span>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Nombre:</label>
                <input type="text" name="nombre" required style="width: 350px; border: 1px solid #777;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Apellidos:</label>
                <input type="text" name="apellidos" required style="width: 350px; border: 1px solid #777;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Email:</label>
                <input type="email" name="email" required style="width: 350px; border: 1px solid #777;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Nombre de usuario:</label>
                <input type="text" name="nombreUsuario" required style="width: 250px; border: 1px solid #777;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Password:</label>
                <input type="password" name="password" required style="width: 250px; border: 1px solid #000;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Rol:</label>
                <select name="rol" style="width: 250px; border: 1px solid #777;">
                    <option value="cliente">Cliente</option>
                    <option value="camarero">Camarero</option>
                    <option value="cocinero">Cocinero</option>
                    <option value="gerente">Gerente</option>
                </select>
            </div>

            <button type="submit" style="cursor: pointer; padding: 5px 15px;">Registrarme</button>
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

    // Validaciones básicas de los campos que SÍ están en el HTML
    if (empty($user) || empty($pass) || empty($nombre) || empty($apellidos) || empty($email)) {
        return ["Todos los campos son obligatorios"];
    }

    if (Usuario::buscaUsuario($user)) {
        return ["El nombre de usuario ya está en uso"];
    }
    
    if (Usuario::buscaPorEmail($email)) {
        return ["Ese correo electrónico ya está registrado por otro usuario"];
    }

    // Enviamos los 5 datos + el rol por defecto
    $nuevoUsuario = Usuario::crea($user, $pass, $nombre, $apellidos, $email, $rol);
    
    if ($nuevoUsuario) {
        header('Location: login.php?registro=exito');
        exit();
    }
    
    return ["Error al crear el usuario. Inténtalo de nuevo."];
}
}


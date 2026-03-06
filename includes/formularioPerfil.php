<?php
require_once __DIR__ . '/formulario.php';
require_once __DIR__ . '/usuario.php';

class FormularioPerfil extends Formulario {
    public function __construct() {
        parent::__construct('formPerfil', ['action' => 'perfil.php']);
    }

    protected function generaCamposFormulario($datosIniciales) {
        // Obtenemos el usuario actual usando su nombre de usuario de la sesión
        $user = Usuario::buscaUsuario($_SESSION['nombre']);
        
        if (!$user) {
            return "<p>Error: Usuario no encontrado.</p>";
        }

        $nombre = htmlspecialchars($user->getNombre());
        $apellidos = htmlspecialchars($user->getApellidos());
        $email = htmlspecialchars($user->getEmail());
        $avatar = htmlspecialchars($user->getAvatar());

        return <<<EOF
        <fieldset style="border: 1px solid #999; padding: 25px; width: 450px; background: #fff; margin: auto;">
            <legend style="background: #333; color: white; padding: 2px 10px;">Editar mis datos</legend>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold;">Nombre:</label>
                <input type="text" name="nombre" value="{$nombre}" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold;">Apellidos:</label>
                <input type="text" name="apellidos" value="{$apellidos}" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold;">Email:</label>
                <input type="email" name="email" value="{$email}" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold;">Nombre del archivo del Avatar (ej: foto1.jpg):</label>
                <input type="text" name="avatar" value="{$avatar}" style="width: 100%; padding: 8px;">
                <small style="color: #666;">Asegúrate de que la imagen exista en la carpeta /img</small>
            </div>

            <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; font-size: 16px;">Guardar Cambios</button>
        </fieldset>
EOF;
    }

    protected function procesaFormulario($datos) {
        $user = Usuario::buscaUsuario($_SESSION['nombre']);
        if (!$user) {
            return ["Usuario no encontrado."];
        }

        $nombre = $datos['nombre'] ?? '';
        $apellidos = $datos['apellidos'] ?? '';
        $email = $datos['email'] ?? '';
        $avatar = $datos['avatar'] ?? '';

        if (empty($nombre) || empty($apellidos) || empty($email)) {
            return ["Los campos Nombre, Apellidos y Email son obligatorios."];
        }

        // Si el email ha cambiado, validamos que no esté en uso (Opcional pero recomendable)
        if ($email !== $user->getEmail() && Usuario::buscaPorEmail($email)) {
            return ["Ese correo electrónico ya está registrado por otro usuario."];
        }

        // Actualizamos las propiedades del objeto usuario
        $user->setNombre($nombre);
        $user->setApellidos($apellidos);
        $user->setEmail($email);
        $user->setAvatar($avatar);

        // Guardamos en la base de datos
        if ($user->actualiza()) {
            // Actualizamos el avatar en la sesión por si cambió
            $_SESSION['avatar'] = $avatar ? $avatar : 'defecto.png';
            
            // Retornamos un mensaje de éxito para que se imprima
            return "<div style='background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align:center;'>Perfil actualizado correctamente.</div>";
        } else {
            return ["Ha ocurrido un error al intentar actualizar el perfil."];
        }
    }
}
?>
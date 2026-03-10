<?php
require_once __DIR__ . '/formulario.php';
require_once __DIR__ . '/usuario.php';

class FormularioPerfil extends Formulario {
    public function __construct() {
        parent::__construct('formPerfil', ['action' => 'perfil.php']);
    }

    protected function generaCamposFormulario($datosIniciales) {
        $user = Usuario::buscaUsuario($_SESSION['nombre']);
        
        if (!$user) {
            return "<p class='alerta-error-critico'>Error: Usuario no encontrado.</p>";
        }

        $nombre = htmlspecialchars($user->getNombre());
        $apellidos = htmlspecialchars($user->getApellidos());
        $email = htmlspecialchars($user->getEmail());
        $avatar = htmlspecialchars($user->getAvatar());

        return <<<EOF
        <div class="tarjeta-formulario formulario-estandar">
            <fieldset>
                <div class="item-barra-izquierda">
                    <label><strong>Nombre:</strong></label>
                    <input type="text" name="nombre" value="{$nombre}" required class="input-formulario">
                </div>

                <div class="item-barra-izquierda">
                    <label><strong>Apellidos:</strong></label>
                    <input type="text" name="apellidos" value="{$apellidos}" required class="input-formulario">
                </div>

                <div class="item-barra-izquierda">
                    <label><strong>Email:</strong></label>
                    <input type="email" name="email" value="{$email}" required class="input-formulario">
                </div>

                <div class="item-barra-izquierda">
                    <label><strong>Nombre del archivo del Avatar:</strong></label>
                    <input type="text" name="avatar" value="{$avatar}" class="input-formulario">
                    <p class="texto-ayuda">Asegúrate de que la imagen exista en la carpeta /img (ej: foto1.jpg)</p>
                </div>

                <div class="acciones-formulario">
                    <button type="submit" class="btn-listo">
                        Guardar Cambios
                    </button>
                </div>
            </fieldset>
        </div>
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

        if ($email !== $user->getEmail() && Usuario::buscaPorEmail($email)) {
            return ["Ese correo electrónico ya está registrado por otro usuario."];
        }

        $user->setNombre($nombre);
        $user->setApellidos($apellidos);
        $user->setEmail($email);
        $user->setAvatar($avatar);

        if ($user->actualiza()) {
        $_SESSION['avatar'] = $avatar ? $avatar : 'defecto.png';
        $_SESSION['mensaje_perfil'] = "¡Perfil actualizado con éxito!";

        // Determinamos el destino según el rol
        $rol = $_SESSION['rol'];
        $destino = 'index.php'; 

        switch ($rol) {
            case 'gerente':
                $destino = 'admin.php';
                break;
            case 'cocinero':
                $destino = 'cocina.php';
                break;
            case 'camarero':
                $destino = 'gestion_pedidos.php';
                break;
        }

        header("Location: " . RUTA_APP . "/" . $destino);
        exit();
    } else {
        return ["Error al actualizar los datos."];
    }
    }
}
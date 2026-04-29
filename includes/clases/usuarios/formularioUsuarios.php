<?php
require_once __DIR__ . '/../formulario.php';
require_once __DIR__ . '/../aplicacion.php';
require_once __DIR__ . '/usuario.php';
require_once __DIR__ . '/../pedidos.php';

class formularioUsuario extends Formulario {

    private $idUsuario;
    private $objetoUsuario;

    public function __construct($idUsuario = null) {
        $this->idUsuario = $idUsuario;
        $urlAction = $_SERVER['PHP_SELF'] . ($idUsuario ? "?id=$idUsuario" : "");
        
        parent::__construct('formEdicionUsuario', ['action' => $urlAction]);
        $this->cargarDatos();
    }

    private function cargarDatos() {
        if ($this->idUsuario){
            $this->objetoUsuario = Usuario::buscaPorId($this->idUsuario);
        }
    }

    protected function generaCamposFormulario($datosIniciales) {
        
        $user = htmlspecialchars($datosIniciales['username'] ?? ($this->objetoUsuario ? $this->objetoUsuario->getNombreUsuario() : ''));
        $email = htmlspecialchars($datosIniciales['email'] ?? ($this->objetoUsuario ? $this->objetoUsuario->getEmail() : ''));
        $nom = htmlspecialchars($datosIniciales['nombre'] ?? ($this->objetoUsuario ? $this->objetoUsuario->getNombre() : ''));
        $ape = htmlspecialchars($datosIniciales['apellidos'] ?? ($this->objetoUsuario ? $this->objetoUsuario->getApellidos() : ''));
        $rolActual = $datosIniciales['rol'] ?? ($this->objetoUsuario ? $this->objetoUsuario->getRol() : 'cliente');
        
        $id = $this->idUsuario;
        $labelPass = $id ? '(vacío para no cambiar)' : '';
        $requiredPass = $id ? '' : 'required';

        // Helper para marcar el select
        $selRol = function($rol) use ($rolActual) { 
            return $rolActual == $rol ? 'selected' : ''; 
        };
        // El onsubmit se mantiene en la etiqueta <form> (inyectada por la clase base)
        // pero aquí definimos el contenido interno
        return <<<EOF
        <p><label><strong>Username:</strong></label><br><input type="text" name="username" value="$user" required class="input-formulario"></p>
        <p><label><strong>Email:</strong></label><br><input type="email" name="email" value="$email" required class="input-formulario"></p>
        <p><label><strong>Nombre:</strong></label><br><input type="text" name="nombre" value="$nom" required class="input-formulario"></p>
        <p><label><strong>Apellidos:</strong></label><br><input type="text" name="apellidos" value="$ape" required class="input-formulario"></p>
        
        <p>
            <label><strong>Rol:</strong></label><br>
            <select name="rol" id="selector_rol" class="input-formulario">
                <option value="cliente" {$selRol('cliente')}>Cliente</option>
                <option value="camarero" {$selRol('camarero')}>Camarero</option>
                <option value="cocinero" {$selRol('cocinero')}>Cocinero</option>
                <option value="gerente" {$selRol('gerente')}>Gerente</option>
            </select>
        </p>

        <p><label><strong>Contraseña $labelPass:</strong></label><br>
        <input type="password" name="password" $requiredPass class="input-formulario"></p>

        <div class="acciones-formulario">
            <button type="submit" class="btn-guardar">💾 GUARDAR</button>
            <a href="gestion_usuarios.php" class="btn-cancelar">❌ CANCELAR Y VOLVER</a>
        </div>
        
        <script>
        document.getElementById('formEdicionUsuario').onsubmit = function() {
            var rolOriginal = "$rolActual";
            var nuevoRol = document.getElementById('selector_rol').value;
            if (rolOriginal === 'cliente' && nuevoRol !== 'cliente') {
                return confirm("¡ADVERTENCIA! Se borrarán los pedidos del cliente al cambiarle el rol. ¿Continuar?");
            }
            return true;
        };
        </script>
EOF;
    }

    protected function procesaFormulario($datos) {

        $id = $this->idUsuario;
        $nuevo_rol = $datos['rol'] ?? 'cliente';

        // 1. Lógica de seguridad: No dejar al sistema sin gerentes
        if ($id && $this->objetoUsuario->getRol() === 'gerente' && $nuevo_rol !== 'gerente') {
            if (Usuario::contarPorRol('gerente') <= 1) {
                return ["⚠️ No puedes cambiar el rol al último GERENTE."];
            }
        }

        // 2. Lógica de integridad: Borrar pedidos si deja de ser cliente
        if ($id && $this->objetoUsuario->getRol() === 'cliente' && $nuevo_rol !== 'cliente') {
           Pedido::borrarPorCliente($id);
        }

        // 3. Preparar SQL
        if ($id) {
            $u = $this->objetoUsuario;
            $u->setUser($datos['username']);
            $u->setNombre($datos['nombre']);
            $u->setApellidos($datos['apellidos']);
            $u->setEmail($datos['email']);
            $u->setRol($nuevo_rol);
            $resultado = $u->actualiza();
        } else {
           $resultado = Usuario::crea(
                $datos['username'], 
                $datos['password'], 
                $datos['nombre'], 
                $datos['apellidos'], 
                $datos['email'], 
                $nuevo_rol
            );
        }

        if ($resultado) {
            // 4. Lógica de Sesión: Si el editado soy YO mismo
            if ($id == $_SESSION['idUsuario']) {
                session_destroy(); 
                header('Location: ' . RUTA_APP . '/login.php?msg=cambio_rol');
                exit();
            }
            header('Location: gestion_usuarios.php?msg=ok'); 
            exit();
        } else { 
            return ["Error al guardar: " ];
        }
    }
}
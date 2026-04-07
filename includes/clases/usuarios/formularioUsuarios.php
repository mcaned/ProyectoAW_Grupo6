<?php
require_once __DIR__ . '/../formulario.php';
require_once __DIR__ . '/../aplicacion.php';

class formularioUsuario extends Formulario {

    private $idUsuario;
    private $datosUsuario;

    public function __construct($idUsuario = null) {
        $this->idUsuario = $idUsuario;
        $urlAction = $_SERVER['PHP_SELF'] . ($idUsuario ? "?id=$idUsuario" : "");
        
        parent::__construct('formEdicionUsuario', ['action' => $urlAction]);
        $this->cargarDatos();
    }

    private function cargarDatos() {
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();

        // Valores por defecto para creación
        $this->datosUsuario = [
            'username' => '', 
            'email' => '', 
            'nombre' => '', 
            'apellidos' => '', 
            'rol' => 'cliente'
        ];

        if ($this->idUsuario) {
            $id = intval($this->idUsuario);
            $res = $conn->query("SELECT * FROM Usuarios WHERE id = $id");
            if ($res && $res->num_rows > 0) {
                $this->datosUsuario = $res->fetch_assoc();
            }
        }
    }

    protected function generaCamposFormulario($datosIniciales) {
        // Combinamos datos de la BD con lo que el usuario haya escrito si hubo error
        $u = array_merge($this->datosUsuario, $datosIniciales);
        
        $user = htmlspecialchars($u['username']);
        $email = htmlspecialchars($u['email']);
        $nom = htmlspecialchars($u['nombre']);
        $ape = htmlspecialchars($u['apellidos']);
        
        $id = $this->idUsuario;
        $labelPass = $id ? '(vacío para no cambiar)' : '';
        $requiredPass = $id ? '' : 'required';

        // Helper para marcar el select
        $selRol = function($rol) use ($u) { return $u['rol'] == $rol ? 'selected' : ''; };

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
            <button type="submit" class="btn-verde">💾 GUARDAR</button>
            <a href="gestion_usuarios.php" class="enlace-cancelar">Cancelar</a>
        </div>
        
        <script>
        document.getElementById('formEdicionUsuario').onsubmit = function() {
            var rolOriginal = "{$this->datosUsuario['rol']}";
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
        $app = Aplicacion::getInstance();
        $conn = $app->conexionBd();

        $id = $this->idUsuario;
        $user = $conn->real_escape_string($datos['username'] ?? '');
        $email = $conn->real_escape_string($datos['email'] ?? '');
        $nom = $conn->real_escape_string($datos['nombre'] ?? '');
        $ape = $conn->real_escape_string($datos['apellidos'] ?? '');
        $nuevo_rol = $conn->real_escape_string($datos['rol'] ?? 'cliente');
        $pass_plana = $datos['password'] ?? '';

        // 1. Lógica de seguridad: No dejar al sistema sin gerentes
        if ($id && $this->datosUsuario['rol'] === 'gerente' && $nuevo_rol !== 'gerente') {
            $resCount = $conn->query("SELECT COUNT(*) as total FROM Usuarios WHERE rol = 'gerente'");
            if ($resCount->fetch_assoc()['total'] <= 1) {
                return ["⚠️ No puedes cambiar el rol al último GERENTE."];
            }
        }

        // 2. Lógica de integridad: Borrar pedidos si deja de ser cliente
        if ($id && $this->datosUsuario['rol'] === 'cliente' && $nuevo_rol !== 'cliente') {
            $conn->query("DELETE FROM Lineas_Pedido WHERE id_pedido IN (SELECT id FROM Pedidos WHERE id_cliente = $id)");
            $conn->query("DELETE FROM Pedidos WHERE id_cliente = $id");
        }

        // 3. Preparar SQL
        if ($id) {
            $sql = "UPDATE Usuarios SET username='$user', email='$email', nombre='$nom', apellidos='$ape', rol='$nuevo_rol'";
            if (!empty($pass_plana)) {
                $hash = password_hash($pass_plana, PASSWORD_DEFAULT);
                $sql .= ", password_hash='$hash'";
            }
            $sql .= " WHERE id=$id";
        } else {
            $hash = password_hash($pass_plana, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Usuarios (username, email, nombre, apellidos, password_hash, rol, avatar_url) 
                    VALUES ('$user', '$email', '$nom', '$ape', '$hash', '$nuevo_rol', 'defecto.png')";
        }

        if ($conn->query($sql)) {
            // 4. Lógica de Sesión: Si el editado soy YO mismo
            if ($id == $_SESSION['idUsuario']) {
                session_destroy(); 
                header('Location: ' . RUTA_APP . '/login.php?msg=cambio_rol');
                exit();
            }
            header('Location: gestion_usuarios.php?msg=ok'); 
            exit();
        } else { 
            return ["Error al guardar: " . $conn->error];
        }
    }
}
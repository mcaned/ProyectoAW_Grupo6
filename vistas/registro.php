<?php
require '../includes/db.php';
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    // Encriptamos la contraseña obligatoriamente por seguridad
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Usuarios (username, email, nombre, apellidos, password_hash, rol) 
            VALUES (?, ?, ?, ?, ?, 'cliente')";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $email, $nombre, $apellidos, $password]);
        $mensaje = "Registro completado. Ya puedes hacer login.";
    } catch (PDOException $e) {
        $mensaje = "Error: El usuario o email ya existen.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><title>Registro - Bistro FDI</title></head>
<body>
    <h1>Crear Cuenta</h1>
    <p><?php echo $mensaje; ?></p>
    <form method="POST">
        <input type="text" name="username" placeholder="Nombre de usuario" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <input type="text" name="apellidos" placeholder="Apellidos" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>
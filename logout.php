<?php

require_once __DIR__ . '/includes/config.php'; 
require_once __DIR__ . '/includes/aplicacion.php';

$app = Aplicacion::getInstance();
$app->init(); 

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: " . RUTA_APP . "/index.php");
exit();

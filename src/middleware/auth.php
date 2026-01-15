<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Postgres.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 1. Cargar variables de entorno si no están disponibles
if (!getenv('JWT_SECRET')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

// 2. Comprobar si existe la cookie
if (!isset($_COOKIE['auth_token'])) {
    header("Location: /views/login.php");
    exit();
}

try {
    // 3. Decodificar y validar el token
    $secret = getenv('JWT_SECRET'); //
    $jwt = $_COOKIE['auth_token'];
    $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));

    // 4. (Recomendado) Verificar que el usuario sigue existiendo en la DB
    $postgres->connect();
    $userCheck = $postgres->query("SELECT uuid FROM users WHERE uuid = '{$decoded->uuid}' LIMIT 1");
    $postgres->disconnect();

    if (!$userCheck) {
        throw new Exception("Usuario no encontrado en la base de datos");
    }

    // Guardamos los datos del usuario para usarlos en la vista si es necesario
    $currentUser = $decoded;
} catch (Exception $e) {
    // Si el token es inválido, expiró o el usuario no existe
    setcookie('auth_token', '', time() - 3600, '/'); // Borramos la cookie inválida
    header("Location: /views/login.php");
    exit();
}

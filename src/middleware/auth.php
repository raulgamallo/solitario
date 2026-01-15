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

    // REFRESH COOKIE (Sliding Expiration)
    // Extend session for another 15 minutes from NOW
    $newExpiresAt = time() + (15 * 60);
    
    // We can also issue a new JWT with new exp if we want strict statelessness, 
    // but updating the cookie expiration is sufficient if the JWT definition of 'exp' is checked loosely or if we issue new JWT.
    // The previous code checked JWT 'exp'. If we don't update the JWT payload, it will expire based on original issue time.
    // So we MUST generate a new JWT with new 'exp'.

    if (isset($decoded->uuid) && isset($decoded->username) && isset($decoded->email)) {
        $payload = [
            'uuid' => $decoded->uuid,
            'username' => $decoded->username,
            'email' => $decoded->email,
            'iat' => time(),
            'exp' => $newExpiresAt,
        ];
        $newToken = JWT::encode($payload, $secret, 'HS256');
        
        setcookie('auth_token', $newToken, [
            'expires' => $newExpiresAt,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

} catch (Exception $e) {
    // Si el token es inválido, expiró o el usuario no existe
    setcookie('auth_token', '', time() - 3600, '/'); // Borramos la cookie inválida
    header("Location: /views/login.php");
    exit();
}

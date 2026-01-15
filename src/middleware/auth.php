<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../classes/Postgres.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!getenv('JWT_SECRET')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

if (!isset($_COOKIE['auth_token'])) {
    header("Location: /views/login.php");
    exit();
}

try {
    $secret = getenv('JWT_SECRET'); 
    $jwt = $_COOKIE['auth_token'];
    $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));

    $postgres->connect();
    $userCheck = $postgres->query("SELECT uuid, email, username, pfp FROM users WHERE uuid = '{$decoded->uuid}' LIMIT 1");
    $postgres->disconnect();

    if (!$userCheck) {
        throw new Exception("Usuario no encontrado en la base de datos");
    }

    $currentUser = (object) [
        'uuid' => $userCheck[0]['uuid'],
        'email' => $userCheck[0]['email'],
        'username' => $userCheck[0]['username'],
        'pfp' => $userCheck[0]['pfp'] ?? null
    ];

    $newExpiresAt = time() + (15 * 60);

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
    setcookie('auth_token', '', time() - 3600, '/'); 
    header("Location: /views/login.php");
    exit();
}

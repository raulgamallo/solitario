<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../classes/User.php';

use Firebase\JWT\JWT;
use Dotenv\Dotenv;

if (!getenv('JWT_SECRET')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $errors = [];
    try {
        $loginDTO = UserLoginDTO::fromRequest($_POST);
        $_SESSION['last_email_attempt'] = $loginDTO->identifier;
        
        if (empty($loginDTO->identifier) || empty($loginDTO->password)) {
             throw new Exception("Email/Usuario y contraseña son obligatorios.");
        }
        
        global $postgres;
        $postgres->connect();
        
        $identifierEscaped = $postgres->escapeLiteral($loginDTO->identifier);

        $query = "SELECT * FROM users WHERE email = '$identifierEscaped' OR username = '$identifierEscaped'";
        $result = $postgres->query($query);
        $postgres->disconnect();
        
        if ($result && count($result) > 0) {
            $userRecord = $result[0];
            
            if (password_verify($loginDTO->password, $userRecord['password_hash'])) {
                $key = getenv('JWT_SECRET');
                
                $issuedAt = time();
                $expirationTime = $issuedAt + (15 * 60);
                $payload = [
                    'uuid' => $userRecord['uuid'],
                    'email' => $userRecord['email'],
                    'username' => $userRecord['username'],
                    'iat' => $issuedAt,
                    'exp' => $expirationTime
                ];

                $jwt = JWT::encode($payload, $key, 'HS256');

                setcookie("auth_token", $jwt, [
                    'expires' => $expirationTime,
                    'path' => '/',
                    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
                
                header("Location: /views/menu.php");
                exit;
            } else {
                $_SESSION['login_error'] = "Credenciales incorrectas";
            }
        } else {
             $_SESSION['login_error'] = "Usuario no encontrado";
        }
        
        header("Location: /views/login.php");
        exit;

    } catch (\Throwable $th) {
        $_SESSION['login_error'] = "Error: " . $th->getMessage();
        header("Location: /views/login.php");
        exit;
    }
}

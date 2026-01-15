<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../classes/User.php';

use Firebase\JWT\JWT;
use Dotenv\Dotenv;

// Initialize Dotenv if needed
if (!getenv('JWT_SECRET')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $errors = [];
    try {
        $loginDTO = UserLoginDTO::fromRequest($_POST);
        // Save email attempt for pre-filling
        $_SESSION['last_email_attempt'] = $loginDTO->identifier;
        
        // Basic validation
        if (empty($loginDTO->identifier) || empty($loginDTO->password)) {
             throw new Exception("Email/Usuario y contraseña son obligatorios.");
        }
        
        // Connect to DB
        global $postgres;
        $postgres->connect();
        
        $identifierEscaped = $postgres->escapeLiteral($loginDTO->identifier);
        
        // Search by email OR username
        $query = "SELECT * FROM users WHERE email = '$identifierEscaped' OR username = '$identifierEscaped'";
        $result = $postgres->query($query);
        $postgres->disconnect(); // Disconnect early if done
        
        if ($result && count($result) > 0) {
            $userRecord = $result[0];
            
            // Check password
            if (password_verify($loginDTO->password, $userRecord['password_hash'])) {
                // Generate JWT
                $key = getenv('JWT_SECRET');
                
                $issuedAt = time();
                $expirationTime = $issuedAt + (15 * 60); // 15 minutes
                $payload = [
                    'uuid' => $userRecord['uuid'],
                    'email' => $userRecord['email'],
                    'username' => $userRecord['username'],
                    'iat' => $issuedAt,
                    'exp' => $expirationTime
                ];

                $jwt = JWT::encode($payload, $key, 'HS256');

                // Set cookie
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

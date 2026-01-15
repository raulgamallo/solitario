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
        $userRegister = UserRegisterDTO::fromRequest($_POST);
        
        if (empty($userRegister->email) || empty($userRegister->username) || empty($userRegister->password)) {
             $errors[] = "Todos los campos son obligatorios.";
        }

        $uploadDir = __DIR__ . "/../assets/pfp/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $pfp = $_FILES["pfp"] ?? null;
        $destination = null;
        if (isset($pfp) && $pfp['error'] === UPLOAD_ERR_OK) {
             $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
             if (!in_array($pfp['type'], $allowedTypes)) {
                 $errors[] = "Formato de imagen no válido.";
             }
        }

        if (empty($errors)) {
            global $postgres;
            $postgres->connect();
            
            $emailEscaped = $postgres->escapeLiteral($userRegister->email);
            $check = $postgres->query("SELECT uuid FROM users WHERE email = '$emailEscaped'");
            if ($check && count($check) > 0) {
                 throw new Exception("El email ya está registrado.");
            }
            
            $usernameEscaped = $postgres->escapeLiteral($userRegister->username);
            $passwordHash = $userRegister->password;

            $query = "INSERT INTO users (email, username, password_hash) VALUES ('{$emailEscaped}', '{$usernameEscaped}', '{$passwordHash}') RETURNING uuid";
            $result = $postgres->query($query);
            $uuid = $result[0]['uuid'];

            if (isset($pfp) && $pfp['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($pfp['name'], PATHINFO_EXTENSION);
                $filename = $uuid . '.' . $ext;
                $destinationPath = $uploadDir . $filename;
                if (move_uploaded_file($pfp['tmp_name'], $destinationPath)) {
                    $pfpWebPath = "/assets/pfp/" . $filename;
                    $postgres->query("UPDATE users SET pfp = '$pfpWebPath' WHERE uuid = '$uuid'");
                    $destination = $pfpWebPath;
                }
            }
            
            $postgres->disconnect();

            $jwtSecret = getenv('JWT_SECRET');
            if (!$jwtSecret) {
                throw new RuntimeException('JWT secret not configured.');
            }

            $issuedAt = time();
            $expiresAt = $issuedAt + (15 * 60);

            $payload = [
                'uuid' => $uuid,
                'username' => $userRegister->username,
                'email' => $userRegister->email,
                'iat' => $issuedAt,
                'exp' => $expiresAt,
            ];
            $token = JWT::encode($payload, $jwtSecret, 'HS256');

            setcookie('auth_token', $token, [
                'expires' => $expiresAt,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            header("Location: /views/menu.php");
            exit;
        } else {
             $_SESSION['register_errors'] = $errors;
             header("Location: /views/register.php");
             exit;
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        $_SESSION['register_errors'] = $errors;
        header("Location: /views/register.php");
        exit;
    }
}

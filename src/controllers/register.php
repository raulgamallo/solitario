<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../classes/User.php';

use Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $errors = [];
    try {
        $userRegister = UserRegisterDTO::fromRequest($_POST);
        // TODO: Validate values

        // TODO: Check if image folder exists
        $uploadDir = __DIR__ . "/../public/pfp/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // TODO: Validate pfp
        $pfp = $_FILES["pfp"] ?? null;
        if (isset($pfp) && $pfp['error'] === UPLOAD_ERR_OK) {
        }

        if (empty($errors)) {
            // TODO: Save user to DB
            $postgres->connect();
            $query = "INSERT INTO users (email, username, password_hash) VALUES ('{$userRegister->email}', '{$userRegister->username}', '{$userRegister->password}') RETURNING uuid";
            $result = $postgres->query($query);
            $postgres->disconnect();
            $uuid = $query[0]['uuid'];

            // TODO: Save pfp with uuid as filename
            if (isset($pfp) && $pfp['error'] === UPLOAD_ERR_OK) {
                $destination = $uploadDir . $uuid . '.' . $fileExtension;
                move_uploaded_file($fileTmpPath, $destination);
            }

            // TODO: Create User instance
            $user = new User($uuid, $userRegister->username, $userRegister->email, $destination ?? null);

            // TODO: Create JWT and set it as cookie
            $jwtSecret = getenv('JWT_SECRET');
            if (!$jwtSecret) {
                throw new RuntimeException('JWT secret not configured.');
            }

            $issuedAt = time();
            $expiresAt = $issuedAt + 3600;

            $payload = [
                'uuid' => $user->uuid,
                'username' => $user->username,
                'email' => $user->email,
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
        } else {
            header("Location: /views/register.php");
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Postgres.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

if (!getenv('JWT_SECRET')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
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
    $uuid = $decoded->uuid;
} catch (Exception $e) {
    header("Location: /views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $errors = [];
    $success = false;

    try {
        $uploadDir = __DIR__ . "/../assets/pfp/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $pfp = $_FILES["pfp"] ?? null;
        
        if (isset($pfp) && $pfp['error'] === UPLOAD_ERR_OK) {
             $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
             if (!in_array($pfp['type'], $allowedTypes)) {
                 throw new Exception("Formato de imagen no válido. Usa JPG, PNG o GIF.");
             }
             
             if ($pfp['size'] > 2 * 1024 * 1024) {
                 throw new Exception("La imagen es demasiado grande. Máximo 2MB.");
             }

             $ext = pathinfo($pfp['name'], PATHINFO_EXTENSION);
             $filename = $uuid . '_' . time() . '.' . $ext;
             $destinationPath = $uploadDir . $filename;
             
             if (move_uploaded_file($pfp['tmp_name'], $destinationPath)) {
                $pfpWebPath = "/assets/pfp/" . $filename;
                
                global $postgres;
                $postgres->connect();
                $postgres->query("UPDATE users SET pfp = '$pfpWebPath' WHERE uuid = '$uuid'");
                $postgres->disconnect();
                
                $success = true;
                $_SESSION['profile_message'] = "Foto de perfil actualizada correctamente.";
                $_SESSION['profile_success'] = true;
             } else {
                 throw new Exception("Error al mover el archivo subido.");
             }
        } else {
             if (isset($pfp) && $pfp['error'] !== UPLOAD_ERR_NO_FILE) {
                 throw new Exception("Error en la subida: " . $pfp['error']);
             }
        }
    } catch (Exception $e) {
        $_SESSION['profile_message'] = $e->getMessage();
        $_SESSION['profile_success'] = false;
    }
    
    header("Location: /views/profile.php");
    exit;
}

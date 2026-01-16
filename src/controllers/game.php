<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../classes/Postgres.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

if (!isset($_COOKIE['auth_token'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'No autenticado']);
    exit;
}

try {
    $secret = getenv('JWT_SECRET');
    if (!$secret) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        $secret = getenv('JWT_SECRET');
    }
    
    $jwt = $_COOKIE['auth_token'];
    $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
    $userUuid = $decoded->uuid;
} catch (Exception $e) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Token inválido']);
    exit;
}

$timeMs = isset($_POST['timeMs']) ? (int) $_POST['timeMs'] : null; 
$moves = isset($_POST['moves']) ? (int) $_POST['moves'] : null;
$result = isset($_POST['result']) ? trim($_POST['result']) : null;
$validResults = ['victoria', 'derrota'];

if ($moves === null || !in_array($result, $validResults, true)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Parámetros inválidos']);
    exit;
}

if ($result === 'victoria') {
    try {
        global $postgres;
        $postgres->connect();

        $userUuidSafe = $postgres->escapeLiteral($userUuid);
        
        $durationSeconds = $timeMs / 1000;
        
        if (!is_numeric($durationSeconds) || !is_numeric($moves)) {
             throw new Exception("Invalid numeric data");
        }
        
        $query = "
            INSERT INTO games (user_uuid, movements, started, finished)
            VALUES (
                '$userUuidSafe', 
                $moves, 
                NOW() - INTERVAL '$durationSeconds seconds', 
                NOW()
            )
            RETURNING uuid
        ";
        
        $postgres->query($query);
        $postgres->disconnect();

    } catch (Exception $e) {
        try { $postgres->disconnect(); } catch (Exception $e2) {}
        
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Error al guardar la partida: ' . $e->getMessage()]);
        exit;
    }
}

header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'result' => $result,
    'moves' => $moves,
    'timeMs' => $timeMs,
]);

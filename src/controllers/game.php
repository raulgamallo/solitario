<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../classes/Postgres.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Simple endpoint to receive game results. Extend to persist in DB if desired.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

// Authenticate user
if (!isset($_COOKIE['auth_token'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'No autenticado']);
    exit;
}

try {
    $secret = getenv('JWT_SECRET');
    if (!$secret) {
        // Fallback for dev env issues, or try loading .env if needed
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

$timeMs = isset($_POST['timeMs']) ? (int) $_POST['timeMs'] : null; // optional
$moves = isset($_POST['moves']) ? (int) $_POST['moves'] : null;
$result = isset($_POST['result']) ? trim($_POST['result']) : null;
$validResults = ['victoria', 'derrota'];

if ($moves === null || !in_array($result, $validResults, true)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Parámetros inválidos']);
    exit;
}

// Only save victories for the ranking
if ($result === 'victoria') {
    try {
        global $postgres;
        $postgres->connect();

        // Calculate timestamps
        // Postgres expects TIMESTAMPTZ, we can pass ISO strings or let it handle it.
        // We want accurate duration, so we should calculate 'started' based on 'finished' (NOW) - duration.
        // Or we pass interval.
        
        $durationSeconds = $timeMs / 1000;
        
        // Use SQL to handle time arithmetic
        $query = "
            INSERT INTO games (user_uuid, movements, started, finished)
            VALUES (
                '$userUuid', 
                $moves, 
                NOW() - INTERVAL '$durationSeconds seconds', 
                NOW()
            )
            RETURNING uuid
        ";
        
        $postgres->query($query);
        $postgres->disconnect();

    } catch (Exception $e) {
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

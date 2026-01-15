<?php
// Simple endpoint to receive game results. Extend to persist in DB if desired.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
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

// Placeholder: hook here to persist to storage or analytics.
header('Content-Type: application/json');
echo json_encode([
    'ok' => true,
    'result' => $result,
    'moves' => $moves,
    'timeMs' => $timeMs,
]);

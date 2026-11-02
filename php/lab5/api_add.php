<?php
$pdo = require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Дозволено лише POST'], JSON_UNESCAPED_UNICODE);
    exit;
}

// JSON-тіло або звичайна форма
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$name   = trim($input['name'] ?? '');
$city   = trim($input['city'] ?? '');
$points = filter_var($input['points'] ?? 0, FILTER_VALIDATE_INT);

if ($name === '' || $city === '' || $points === false || $points < 0) {
    http_response_code(422);
    echo json_encode(
        ['error' => 'Заповніть назву, місто та невід\'ємні очки'],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO teams (name, city, points) VALUES (:name, :city, :points)');
    $stmt->execute(['name' => $name, 'city' => $city, 'points' => $points]);

    http_response_code(201);
    echo json_encode([
        'id'     => (int)$pdo->lastInsertId(),
        'name'   => $name,
        'city'   => $city,
        'points' => $points,
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Помилка бази даних'], JSON_UNESCAPED_UNICODE);
}

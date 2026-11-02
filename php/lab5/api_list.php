<?php
$pdo = require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');

try {
    if ($q !== '') {
        $stmt = $pdo->prepare(
            'SELECT id, name, city, points FROM teams
             WHERE city LIKE :q
             ORDER BY points DESC, name ASC'
        );
        $stmt->execute(['q' => '%' . $q . '%']);
    } else {
        $stmt = $pdo->query(
            'SELECT id, name, city, points FROM teams
             ORDER BY points DESC, name ASC'
        );
    }
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Помилка бази даних'], JSON_UNESCAPED_UNICODE);
}

<?php
/*
 * API «Спортивна ліга» — ресурс teams (id, name, city, points)
 * Використовує db.php і TeamRepository.php з практикуму №4 (лежать в тій самій папці).
 * Усі відповіді: {"success": true, "data": ...} або {"success": false, "error": "..."}
 *
 *  GET  /api.php?resource=teams                    -> 200 список команд
 *  GET  /api.php?resource=teams&id=N               -> 200 одна команда | 400 | 404
 *  POST /api.php?resource=teams                    -> 201 створена команда | 400
 *       body: name*, city*, points (необов'язково, >= 0)
 *  POST /api.php?resource=teams&action=addPoints   -> 200 оновлена команда | 400 | 404
 *       body: id*, result* (win=3 | draw=1 | loss=0)
 *  інший метод для teams -> 405 | невідомий ресурс -> 404
 */

header('Content-Type: application/json; charset=utf-8');

// Очки за результат матчу
const POINTS_BY_RESULT = ['win' => 3, 'draw' => 1, 'loss' => 0];

function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function ok(int $status, $data): void
{
    respond($status, ['success' => true, 'data' => $data]);
}

function fail(int $status, string $message): void
{
    respond($status, ['success' => false, 'error' => $message]);
}

// Тіло запиту: JSON (php://input) або звичайна форма ($_POST)
function getInput(): array
{
    $raw = file_get_contents('php://input');
    if ($raw !== false && trim($raw) !== '') {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
        if (!$_POST) {
            fail(400, 'Некоректний JSON у тілі запиту');
        }
    }
    return $_POST;
}

// Ціле невід'ємне число? (приймає int або рядок з цифр)
function isNonNegativeInt($value): bool
{
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== false;
}

function isPositiveInt($value): bool
{
    return $value !== null
        && filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
}

// ---------- Обробники ----------

function listTeams(TeamRepository $repo): void
{
    ok(200, $repo->standings());
}

function getTeam(TeamRepository $repo, $id): void
{
    if (!isPositiveInt($id)) {
        fail(400, 'Параметр id має бути додатним цілим числом');
    }
    $team = $repo->find((int) $id);
    if (!$team) {
        fail(404, "Команду з id=$id не знайдено");
    }
    ok(200, $team);
}

function createTeam(TeamRepository $repo): void
{
    $in = getInput();

    $name   = isset($in['name']) ? trim((string) $in['name']) : '';
    $city   = isset($in['city']) ? trim((string) $in['city']) : '';
    $points = $in['points'] ?? 0;

    if ($name === '') {
        fail(400, 'Поле name обов\'язкове');
    }
    if (mb_strlen($name) > 100) {
        fail(400, 'Поле name не може бути довшим за 100 символів');
    }
    if ($city === '') {
        fail(400, 'Поле city обов\'язкове');
    }
    if (mb_strlen($city) > 100) {
        fail(400, 'Поле city не може бути довшим за 100 символів');
    }
    if (!isNonNegativeInt($points)) {
        fail(400, 'Поле points має бути цілим невід\'ємним числом');
    }

    $id = $repo->addTeam($name, $city, (int) $points);
    ok(201, $repo->find($id));
}

// Доменна дія: нарахувати очки за результат матчу
function addPoints(TeamRepository $repo): void
{
    $in = getInput();

    $id     = $in['id'] ?? null;
    $result = isset($in['result']) ? strtolower(trim((string) $in['result'])) : '';

    if (!isPositiveInt($id)) {
        fail(400, 'Поле id обов\'язкове і має бути додатним цілим числом');
    }
    if (!array_key_exists($result, POINTS_BY_RESULT)) {
        fail(400, 'Поле result має бути одним зі значень: win, draw, loss');
    }

    $team = $repo->find((int) $id);
    if (!$team) {
        fail(404, "Команду з id=$id не знайдено");
    }

    $added = POINTS_BY_RESULT[$result];
    $repo->updatePoints((int) $id, (int) $team['points'] + $added);

    $team = $repo->find((int) $id);
    $team['added'] = $added;
    ok(200, $team);
}

// ---------- Маршрутизація ----------

$method   = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? null;
$id       = $_GET['id'] ?? null;
$action   = $_GET['action'] ?? null;

try {
    $pdo = require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/TeamRepository.php';
    $repo = new TeamRepository($pdo);

    if ($resource !== 'teams') {
        fail(404, 'Невідомий ресурс');
    }

    if ($method === 'GET') {
        if ($id === null) {
            listTeams($repo);
        }
        getTeam($repo, $id);
    } elseif ($method === 'POST') {
        if ($action === null) {
            createTeam($repo);
        } elseif ($action === 'addPoints') {
            addPoints($repo);
        } else {
            fail(400, 'Невідома дія action. Підтримується: addPoints');
        }
    } else {
        header('Allow: GET, POST');
        fail(405, "Метод $method не підтримується для ресурсу teams");
    }
} catch (PDOException $e) {
    fail(500, 'Помилка бази даних. Перевірте db.php і чи імпортовано schema.sql');
}

<?php
$pdo = require_once 'db.php';
require_once 'TeamRepository.php';

$repo = new TeamRepository($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);
    $name   = trim($_POST['name'] ?? '');
    $city   = trim($_POST['city'] ?? '');
    $points = (int) ($_POST['points'] ?? 0);

    if ($id > 0 && $name !== '' && $city !== '') {
        $repo->updateTeam($id, $name, $city, $points);
    }
    header('Location: index.php');
    exit;
}

$team = $repo->find((int) ($_GET['id'] ?? 0));
if (!$team) {
    http_response_code(404);
    exit('Команду не знайдено');
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагування команди</title>
</head>
<body style="font-family: sans-serif; margin: 2rem;">
<h1>Редагувати команду</h1>
<form method="post">
    <input type="hidden" name="id" value="<?= (int) $team['id'] ?>">
    <label>Назва: <input type="text" name="name" value="<?= htmlspecialchars($team['name']) ?>" required></label><br>
    <label>Місто: <input type="text" name="city" value="<?= htmlspecialchars($team['city']) ?>" required></label><br>
    <label>Очки: <input type="number" name="points" value="<?= (int) $team['points'] ?>" min="0" required></label><br>
    <button type="submit">Зберегти</button>
    <a href="index.php">Скасувати</a>
</form>
</body>
</html>

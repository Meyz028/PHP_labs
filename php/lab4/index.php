<?php
$pdo = require_once 'db.php';
require_once 'TeamRepository.php';

$repo = new TeamRepository($pdo);

$city  = trim($_GET['city'] ?? '');
$sort  = ($_GET['sort'] ?? '') === 'standings';

if ($city !== '') {
    $teams = $repo->findByCity($city);   // findByCity() - підготовлений запит
} elseif ($sort) {
    $teams = $repo->standings();         // standings() - турнірна таблиця
} else {
    $teams = $repo->all();               // SELECT * FROM teams
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Спортивна ліга</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        table { border-collapse: collapse; margin: 1rem 0; }
        th, td { border: 1px solid #999; padding: 6px 12px; }
        th { background: #eee; }
        form.inline { display: inline; }
        label { display: inline-block; margin: 4px 0; }
    </style>
</head>
<body>
<h1>Спортивна ліга</h1>

<form method="get">
    <input type="text" name="city" placeholder="Місто" value="<?= htmlspecialchars($city) ?>">
    <button type="submit">Знайти за містом</button>
    <a href="index.php">Усі команди</a> |
    <a href="index.php?sort=standings">Турнірна таблиця</a>
</form>

<table>
    <tr><th>ID</th><th>Назва</th><th>Місто</th><th>Очки</th><th>Дії</th></tr>
    <?php foreach ($teams as $t): ?>
        <tr>
            <td><?= (int) $t['id'] ?></td>
            <td><?= htmlspecialchars($t['name']) ?></td>
            <td><?= htmlspecialchars($t['city']) ?></td>
            <td><?= (int) $t['points'] ?></td>
            <td>
                <a href="edit.php?id=<?= (int) $t['id'] ?>">Редагувати</a>
                <form class="inline" method="post" action="delete.php"
                      onsubmit="return confirm('Видалити команду?');">
                    <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
                    <button type="submit">Видалити</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$teams): ?>
        <tr><td colspan="5">Нічого не знайдено</td></tr>
    <?php endif; ?>
</table>

<h2>Додати команду</h2>
<form method="post" action="add.php">
    <label>Назва: <input type="text" name="name" required></label><br>
    <label>Місто: <input type="text" name="city" required></label><br>
    <label>Очки: <input type="number" name="points" value="0" min="0" required></label><br>
    <button type="submit">Додати</button>
</form>
</body>
</html>

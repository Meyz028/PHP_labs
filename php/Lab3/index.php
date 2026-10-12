<?php
declare(strict_types=1);

// Підключення бібліотеки функцій і класів (Team потрібен перед PromotedTeam/League)
require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/classes/Team.php';
require_once __DIR__ . '/classes/PromotedTeam.php';
require_once __DIR__ . '/classes/League.php';

// Створення об'єктів: базовий і похідний клас
$league = new League('Українська ліга');

$league->addTeam(new Team('Динамо', 'Київ', 52));
$league->addTeam(new Team('Шахтар', 'Донецьк', 55));
$league->addTeam(new Team('Карпати', 'Львів', 41));
$league->addTeam(new Team('Дніпро-1', 'Дніпро', 38));
$league->addTeam(new PromotedTeam('Верес', 'Рівне', 33, 'Перша ліга'));
$league->addTeam(new PromotedTeam('Волинь', 'Луцьк', 21, 'Перша ліга'));

$standings = $league->standings();
$kyivTeams = $league->findByCity('Київ');

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Практична робота №3 — Спортивна ліга</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        table { border-collapse: collapse; min-width: 600px; }
        th, td { border: 1px solid #999; padding: 6px 12px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1><?= e($league->getName()) ?> — турнірна таблиця</h1>

    <table>
        <tr>
            <th>Місце</th>
            <th>Команда</th>
            <th>Місто</th>
            <th>Очки</th>
            <th>Повний опис (getInfo)</th>
        </tr>
        <?php foreach ($standings as $i => $team): ?>
            <tr>
                <td><?= e(rankLabel($i + 1)) ?></td>
                <td><?= e($team->getName()) ?></td>
                <td><?= e($team->getCity()) ?></td>
                <td><?= e(formatPoints($team->getPoints())) ?></td>
                <td><?= e($team->getInfo()) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Пошук за містом: Київ</h2>
    <ul>
        <?php foreach ($kyivTeams as $team): ?>
            <li><?= e($team->getInfo()) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>

<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Практична робота №1
 * Варіант 20. Спортивна ліга (турнірна таблиця, матчі, команди)
 */

// Крок 2. Масив даних домену: команди
$teams = [
    ['name' => 'Динамо',   'city' => 'Київ',      'points' => 24, 'played' => 10],
    ['name' => 'Шахтар',   'city' => 'Донецьк',   'points' => 21, 'played' => 10],
    ['name' => 'Полісся',  'city' => 'Житомир',   'points' => 15, 'played' => 9],
    ['name' => 'Верес',    'city' => 'Рівне',     'points' => 12, 'played' => 10],
    ['name' => 'Оболонь',  'city' => 'Київ',      'points' => 20, 'played' => 9],
    ['name' => 'Рух',      'city' => 'Львів',     'points' => 8,  'played' => 8],
];

// Крок 3. Функція форматування одного запису
function formatTeam(array $team): string
{
    return "{$team['name']} ({$team['city']})";
}

// Крок 4. Функція обчислення мітки за умовою варіанта
function getTeamLabel(array $team): string
{
    return $team['points'] >= 20 ? 'Лідер туру' : '';
}

// Крок 6. Агрегатний показник: сумарна кількість зіграних матчів по всіх командах
$totalPlayed = array_sum(array_column($teams, 'played'));
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Спортивна ліга — Турнірна таблиця</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Турнірна таблиця ліги</h1>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
        <tr>
            <th>Команда</th>
            <th>Очки</th>
            <th>Зіграно матчів</th>
            <th>Статус</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($teams as $team): ?>
            <?php $label = getTeamLabel($team); ?>
            <tr<?= $label === 'Лідер туру' ? ' class="leader"' : '' ?>>
                <td><?= htmlspecialchars(formatTeam($team)) ?></td>
                <td><?= $team['points'] ?></td>
                <td><?= $team['played'] ?></td>
                <td><?= $label !== '' ? $label : '—' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Сумарна кількість зіграних матчів по всіх командах:</strong> <?= $totalPlayed ?></p>
    </div>
</body>
</html>

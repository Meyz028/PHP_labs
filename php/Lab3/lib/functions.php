<?php
declare(strict_types=1);

/**
 * "Бібліотека" допоміжних функцій для спортивної ліги.
 */

/**
 * Форматує кількість очок з правильною формою слова:
 * 1 очко, 2-4 очки, 5+ очок (11-14 - завжди "очок").
 */
function formatPoints(int $points): string
{
    $mod100 = $points % 100;
    $mod10 = $points % 10;

    if ($mod100 >= 11 && $mod100 <= 14) {
        $word = 'очок';
    } elseif ($mod10 === 1) {
        $word = 'очко';
    } elseif ($mod10 >= 2 && $mod10 <= 4) {
        $word = 'очки';
    } else {
        $word = 'очок';
    }

    return "{$points} {$word}";
}

/**
 * Текстова мітка місця в таблиці: 1 -> "🥇 1 місце", 2 -> "🥈 2 місце" тощо.
 */
function rankLabel(int $rank): string
{
    if ($rank < 1) {
        return 'н/д';
    }

    return match ($rank) {
        1 => '🥇 1 місце',
        2 => '🥈 2 місце',
        3 => '🥉 3 місце',
        default => "{$rank} місце",
    };
}

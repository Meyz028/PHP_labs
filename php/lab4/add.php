<?php
$pdo = require_once 'db.php';
require_once 'TeamRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name'] ?? '');
    $city   = trim($_POST['city'] ?? '');
    $points = (int) ($_POST['points'] ?? 0);

    if ($name !== '' && $city !== '') {
        (new TeamRepository($pdo))->addTeam($name, $city, $points);
    }
}
header('Location: index.php');
exit;

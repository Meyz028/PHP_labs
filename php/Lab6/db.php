<?php
$dsn = 'mysql:host=localhost;dbname=practicum4;charset=utf8mb4';

return new PDO($dsn, 'root', 'root', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

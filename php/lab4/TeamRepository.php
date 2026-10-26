<?php
// Методи варіанта 20: findByCity, standings, addTeam, updatePoints, deleteTeam
class TeamRepository
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM teams')->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM teams WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByCity(string $city): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM teams WHERE city = :city');
        $stmt->execute([':city' => $city]);
        return $stmt->fetchAll();
    }

    public function standings(): array
    {
        return $this->pdo->query('SELECT * FROM teams ORDER BY points DESC')->fetchAll();
    }

    public function addTeam(string $name, string $city, int $points): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO teams (name, city, points) VALUES (:name, :city, :points)'
        );
        $stmt->execute([':name' => $name, ':city' => $city, ':points' => $points]);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateTeam(int $id, string $name, string $city, int $points): int
    {
        $stmt = $this->pdo->prepare(
            'UPDATE teams SET name = :name, city = :city, points = :points WHERE id = :id'
        );
        $stmt->execute([':name' => $name, ':city' => $city, ':points' => $points, ':id' => $id]);
        return $stmt->rowCount();
    }

    public function updatePoints(int $id, int $points): int
    {
        $stmt = $this->pdo->prepare('UPDATE teams SET points = :points WHERE id = :id');
        $stmt->execute([':points' => $points, ':id' => $id]);
        return $stmt->rowCount();
    }

    public function deleteTeam(int $id): int
    {
        $stmt = $this->pdo->prepare('DELETE FROM teams WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }
}

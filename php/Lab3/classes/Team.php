<?php
declare(strict_types=1);

/**
 * Практична робота №3, варіант 20 (Спортивна ліга)
 * Автор: Shmihel Makar, група IO-44
 *
 * Базовий клас: команда ліги.
 */
class Team
{
    private string $name;
    private string $city;
    protected int $points; // protected: доступний нащадкам

    public function __construct(string $name, string $city, int $points = 0)
    {
        $this->name = $name;
        $this->city = $city;
        $this->points = $points;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function addPoints(int $points): void
    {
        if ($points < 0) {
            throw new InvalidArgumentException('Кількість очок не може бути від\'ємною.');
        }
        $this->points += $points;
    }

    public function getInfo(): string
    {
        return "{$this->name} ({$this->city}), " . formatPoints($this->points);
    }
}

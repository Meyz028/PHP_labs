<?php
declare(strict_types=1);

require_once __DIR__ . '/Team.php';

/**
 * Похідний клас: команда, що підвищилась у класі
 * (прийшла з іншої ліги).
 */
class PromotedTeam extends Team
{
    private string $previousLeague;

    public function __construct(string $name, string $city, int $points, string $previousLeague)
    {
        parent::__construct($name, $city, $points);
        $this->previousLeague = $previousLeague;
    }

    public function getPreviousLeague(): string
    {
        return $this->previousLeague;
    }

    public function getInfo(): string
    {
        return parent::getInfo() . ", підвищилась з: {$this->previousLeague}";
    }
}

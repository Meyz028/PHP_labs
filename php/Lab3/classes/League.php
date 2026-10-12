<?php
declare(strict_types=1);

require_once __DIR__ . '/Team.php';

/**
 * Клас-менеджер: колекція команд ліги.
 */
class League
{
    private string $name;
    /** @var Team[] */
    private array $teams = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addTeam(Team $team): void
    {
        $this->teams[] = $team;
    }

    /**
     * Турнірна таблиця: за очками (спадання), при рівності - за назвою.
     * @return Team[]
     */
    public function standings(): array
    {
        $sorted = $this->teams; // копія масиву, оригінал не змінюється
        usort($sorted, function (Team $a, Team $b): int {
            return [$b->getPoints(), $a->getName()] <=> [$a->getPoints(), $b->getName()];
        });
        return $sorted;
    }

    /**
     * Пошук команд за містом (без урахування регістру).
     * @return Team[]
     */
    public function findByCity(string $city): array
    {
        $result = array_filter(
            $this->teams,
            fn(Team $t): bool => mb_strtolower($t->getCity()) === mb_strtolower($city)
        );
        return array_values($result);
    }
}

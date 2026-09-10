<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class FakeSalleRepository implements SalleRepositoryInterface
{
    /** @var array<int, Salle> */
    private array $salles = [];

    public function ajouter(Salle $salle): void
    {
        $this->salles[$salle->id] = $salle;
    }

    public function findById(int $id): ?Salle
    {
        return $this->salles[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->salles);
    }

    public function findActives(): array
    {
        return array_values(array_filter($this->salles, fn(Salle $s) => $s->active));
    }

    public function create(array $donnees): Salle
    {
        $salle = new Salle($donnees);
        $salle->id = count($this->salles) + 1;
        $this->salles[$salle->id] = $salle;
        return $salle;
    }

    public function update(int $id, array $donnees): ?Salle
    {
        if (!isset($this->salles[$id])) {
            return null;
        }
        $this->salles[$id]->fill($donnees);
        return $this->salles[$id];
    }
}
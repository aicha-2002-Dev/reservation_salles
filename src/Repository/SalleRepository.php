<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Override;

final class SalleRepository implements SalleRepositoryInterface
{
    #[Override]
    public function findById(int $id): ?Salle
    {
        return Salle::find($id);
    }

    #[Override]
    public function findActives(): array
    {
        return Salle::where('active',true)->get()->all();
    }

    #[Override]
    public function create(array $donnees): Salle
    {
        return Salle::create($donnees);
    }
}
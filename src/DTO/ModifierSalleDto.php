<?php

declare(strict_types=1);

namespace App\DTO;

final class ModifierSalleDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly string $batiment,
        public readonly int $capacite,
        public readonly string $type,
        public readonly bool $active,
    ) {}

    public static function fromArray(int $id, array $data): self
    {
        return new self(
            id: $id,
            nom: $data['nom'],
            batiment: $data['batiment'],
            capacite: (int) $data['capacite'],
            type: $data['type'],
            active: (bool) ($data['active'] ?? true),
        );
    }
}
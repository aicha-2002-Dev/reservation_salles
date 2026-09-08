<?php

declare(strict_types=1);

namespace App\DTO;

final class CreerSalleDto
{
    public function __construct(
        public readonly string $nom,
        public readonly string $batiment,
        public readonly int $capacite,
        public readonly string $type,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nom: $data['nom'],
            batiment: $data['batiment'],
            capacite: (int) $data['capacite'],
            type: $data['type'],
        );
    }
}
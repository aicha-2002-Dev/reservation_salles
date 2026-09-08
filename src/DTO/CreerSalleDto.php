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

    public static function build(): CreerSalleDtoBuilder
    {
        return new CreerSalleDtoBuilder();
    }
}
<?php

declare(strict_types=1);

namespace App\DTO;

final class CreerReservationDto
{
    public function __construct(
        public readonly int $salleId,
        public readonly string $responsable,
        public readonly string $email,
        public readonly string $motif,
        public readonly \DateTimeImmutable $dateDebut,
        public readonly \DateTimeImmutable $dateFin,
    ) {}

    public static function build(): CreerReservationDtoBuilder
    {
        return new CreerReservationDtoBuilder();
    }

    
}
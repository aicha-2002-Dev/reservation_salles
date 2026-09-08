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

    public static function fromArray(array $data): self
    {
        return new self(
            salleId: (int) $data['salle_id'],
            responsable: $data['responsable'],
            email: $data['email'],
            motif: $data['motif'],
            dateDebut: new \DateTimeImmutable($data['date_debut']),
            dateFin: new \DateTimeImmutable($data['date_fin']),
        );
    }
}
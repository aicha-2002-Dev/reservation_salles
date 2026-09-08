<?php
declare(strict_types=1);

namespace App\DTO;

class CreerReservationDtoBuilder
{
    private int $salleId;
    private string $responsable;
    private string $email;
    private string $motif;
    private \DateTimeImmutable $dateDebut;
    private \DateTimeImmutable $dateFin;

    public function salleId(int $salleId): self
    {
        $this->salleId = $salleId;

        return $this;
    }

    public function responsable(string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function email(string $email): self
    {
        $this->email= $email;

        return $this;
    }

    public function motif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
    public function dateDebut(\DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function dateFin(\DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }
    public function build(): CreerReservationDto
    {
        return new CreerReservationDto(
            $this->salleId,
            $this->responsable,
            $this->email,
            $this->motif,
            $this->dateDebut,
            $this->dateFin 
        );
    }
}
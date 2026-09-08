<?php

namespace App\DTO;

class CreerSalleDtoBuilder
{
    private string $nom;
    private string $batiment;
    private int $capacite;
    private string $type;

    public function nom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function batiment(string $batiment): self
    {
        $this->batiment = $batiment;

        return $this;
    }

    public function capacite(int $capacite): self
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function build(): CreerSalleDto
    {
        return new CreerSalleDto(
            $this->nom,
            $this->batiment,
            $this->capacite,
            $this->type
        );
    }
}
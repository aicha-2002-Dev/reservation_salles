<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

final class SalleValidator implements ValidatorInterface
{
    private const TYPES_AUTORISES = ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'];

    public function validate(array $data): ValidationResult
    {
        $regles = [
            'nom'      => v::stringType()->length(2, 100)->setName('Le nom'),
            'batiment' => v::stringType()->length(2, 100)->setName('Le bâtiment'),
            'capacite' => v::intVal()->between(1, 1000)->setName('La capacité'),
            'type'     => v::in(self::TYPES_AUTORISES)->setName('Le type'),
        ];

        $messages = [
            'nom'      => "Le nom de la salle doit etre compris entre 2 et 100 caracteres.",
            'batiment' => "Le batiment doit etre compris entre 2 et 100 caracteres.",
            'capacite' => "La capacite doit etre un nombre compris entre 1 et 1000.",
            'type'     => "Le type de la salle n'est pas valide.",
        ];

        $erreurs = [];

        foreach ($regles as $champ => $regle) {
            try {
                $regle->assert($data[$champ] ?? null);
            } catch (ValidationException $e) {
                $erreurs[$champ] = [$messages[$champ]];
            }
        }

        if (!empty($erreurs)) {
            return ValidationResult::failure($erreurs);
        }

        return ValidationResult::success($data);
    }

}

<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

final class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $regles = [
            'salle_id'    => v::intVal()->positive(),
            'responsable' => v::stringType()->length(2, 120),
            'email'       => v::email(),
            'motif'       => v::stringType()->length(5, 255),
            'date_debut'  => v::date(),
            'date_fin'    => v::date(),
        ];

        $erreurs = [];

        foreach ($regles as $champ => $regle) {
            try {
                $regle->assert($data[$champ] ?? null);
            } catch (ValidationException $e) {
                $erreurs[$champ] = $e->getMessage();
            }
        }

        if (!empty($erreurs)) {
            return ValidationResult::failure($erreurs);
        }

        return ValidationResult::success($data);
    }
}
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
            'nom'      => v::stringType()->length(2, 100),
            'batiment' => v::stringType()->length(2, 100),
            'capacite' => v::intVal()->between(1, 1000),
            'type'     => v::in(self::TYPES_AUTORISES),
            'active'   => v::boolVal(),
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
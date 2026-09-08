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

        $erreurs = [];

        foreach ($regles as $champ => $regle) {
            try {
                $regle->assert($data[$champ] ?? null);
            } catch (ValidationException $e) {
                $erreurs[$champ] = $this->extraireMessages($e);
            }
        }

        if (!empty($erreurs)) {
            return ValidationResult::failure($erreurs);
        }

        return ValidationResult::success($data);
    }

    private function extraireMessages(ValidationException $e): array
    {
        $messages = $e->getMessage();

        if (is_string($messages)) {
            return [$messages];
        }

        return array_values($messages);
    }
}

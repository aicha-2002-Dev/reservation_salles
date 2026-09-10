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
            'salle_id'    => v::intVal()->positive()->setName("L'identifiant de la salle"),
            'responsable' => v::stringType()->length(2, 120)->setName('Le responsable'),
            'email'       => v::email()->setName("L'email"),
            'motif'       => v::stringType()->length(5, 255)->setName('Le motif'),
            'date_debut'  => v::dateTime('Y-m-d\TH:i')->setName('La date de début'),
            'date_fin'    => v::dateTime('Y-m-d\TH:i')->setName('La date de fin'),
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
        $messages = $e->getMessages();

        if (is_string($messages)) {
            return [$messages];
        }

        return array_values($messages);
    }
}

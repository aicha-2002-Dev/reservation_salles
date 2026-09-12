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
        $messages = [
            'salle_id'    => "L'identifiant de la salle est obligatoire et doit etre un nombre positif.",
            'responsable' => "Le responsable doit contenir entre 2 et 120 caracteres.",
            'email'       => "L'email n'est pas valide.",
            'motif'       => "Le motif doit contenir entre 5 et 255 caracteres.",
            'date_debut'  => "La date de debut n'est pas valide.",
            'date_fin'    => "La date de fin n'est pas valide."
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

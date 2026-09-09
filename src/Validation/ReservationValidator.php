<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as RespectValidator;

final class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $errors = [];

        $rules = [
            'salle_id'    => RespectValidator::intVal()->min(1),
            'responsable' => RespectValidator::stringType()->notEmpty()->length(2, 150),
            'email'       => RespectValidator::email(),
            'motif'       => RespectValidator::stringType()->length(5, 255),
        ];

        foreach ($rules as $field => $rule) {
            try {
                $rule->assert($data[$field] ?? null);
            } catch (ValidationException $exception) {
                $errors[$field] = [$exception->getMessage()];
            }
        }

        foreach (['date_debut', 'date_fin'] as $field) {
            $value = $data[$field] ?? null;
            if (!is_string($value) || $value === '' || strtotime($value) === false) {
                $errors[$field] = ['La date doit être une date valide.'];
            }
        }

        return new ValidationResult($errors === [], $errors, $data);
    }
}

<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as RespectValidator;

final class SalleValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $rules = [
            'nom'      => RespectValidator::stringType()->length(2, 100),
            'batiment' => RespectValidator::stringType()->length(2, 100),
            'capacite' => RespectValidator::intVal()->between(1, 1000),
            'type'     => RespectValidator::in(['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion']),
            'active'   => RespectValidator::anyOf(
                RespectValidator::boolType(),
                RespectValidator::in(['0', '1', 0, 1], true)
            ),
        ];

        return $this->validateFields($data, $rules);
    }

    private function validateFields(array $data, array $rules): ValidationResult
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            try {
                $rule->assert($data[$field] ?? null);
            } catch (ValidationException $exception) {
                $errors[$field] = [$exception->getMessage()];
            }
        }

        return new ValidationResult($errors === [], $errors, $data);
    }
}

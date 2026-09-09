<?php

declare(strict_types=1);

namespace App\DTO;

use InvalidArgumentException;

final class CreerSalleDTO
{
    public function __construct(
        public readonly string $nom,
        public readonly string $batiment,
        public readonly int $capacite,
        public readonly string $type,
        public readonly bool $active,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nom: self::stringValue($data, 'nom'),
            batiment: self::stringValue($data, 'batiment'),
            capacite: self::intValue($data, 'capacite'),
            type: self::stringValue($data, 'type'),
            active: self::boolValue($data, 'active'),
        );
    }

    private static function stringValue(array $data, string $field): string
    {
        $value = $data[$field] ?? null;

        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Le champ %s doit être une chaîne.', $field));
        }

        return trim($value);
    }

    private static function intValue(array $data, string $field): int
    {
        $value = $data[$field] ?? null;

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && filter_var($value, FILTER_VALIDATE_INT) !== false) {
            return (int) $value;
        }

        throw new InvalidArgumentException(sprintf('Le champ %s doit être un entier.', $field));
    }

    private static function boolValue(array $data, string $field): bool
    {
        $value = $data[$field] ?? null;

        if (is_bool($value)) {
            return $value;
        }

        if ($value === '1' || $value === 1) {
            return true;
        }

        if ($value === '0' || $value === 0) {
            return false;
        }

        throw new InvalidArgumentException(sprintf('Le champ %s doit être booléen.', $field));
    }
}


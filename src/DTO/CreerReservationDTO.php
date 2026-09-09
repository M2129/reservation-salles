<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class CreerReservationDTO
{
    public function __construct(
        public readonly int $salleId,
        public readonly string $responsable,
        public readonly string $email,
        public readonly string $motif,
        public readonly DateTimeImmutable $dateDebut,
        public readonly DateTimeImmutable $dateFin,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            salleId: self::intValue($data, 'salle_id'),
            responsable: self::stringValue($data, 'responsable'),
            email: self::stringValue($data, 'email'),
            motif: self::stringValue($data, 'motif'),
            dateDebut: self::dateValue($data, 'date_debut'),
            dateFin: self::dateValue($data, 'date_fin'),
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

    private static function dateValue(array $data, string $field): DateTimeImmutable
    {
        $value = $data[$field] ?? null;

        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException(sprintf('Le champ %s doit être une date valide.', $field));
        }

        foreach (['!Y-m-d H:i:s', '!Y-m-d H:i'] as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $value);
            $errors = DateTimeImmutable::getLastErrors();

            if ($date !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date;
            }
        }

        throw new InvalidArgumentException(sprintf('Le champ %s doit être une date valide.', $field));
    }
}

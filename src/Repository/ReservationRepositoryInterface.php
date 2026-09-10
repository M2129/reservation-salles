<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;

interface ReservationRepositoryInterface
{
    /** @return list<Reservation> */
    public function all(): array;

    public function findById(int $id): ?Reservation;

    /** @param array<string, mixed> $filters @return list<Reservation> */
    public function search(array $filters = []): array;

    /** @return list<Reservation> */
    public function findConflicts(
        int $salleId,
        DateTimeInterface $dateDebut,
        DateTimeInterface $dateFin,
    ): array;

    public function save(Reservation $reservation): Reservation;

    public function cancel(Reservation $reservation): Reservation;
}

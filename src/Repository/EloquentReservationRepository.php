<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function all(): array
    {
        return Reservation::query()->with('salle')->orderBy('date_debut')->get()->all();
    }

    public function findById(int $id): ?Reservation
    {
        return Reservation::query()->with('salle')->find($id);
    }

    public function search(array $filters = []): array
    {
        $query = Reservation::query()->with('salle');

        if (isset($filters['salle_id'])) {
            $query->where('salle_id', (int) $filters['salle_id']);
        }

        if (isset($filters['statut']) && is_string($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }

        if (isset($filters['date_debut']) && $filters['date_debut'] instanceof DateTimeInterface) {
            $query->where('date_debut', '>=', $filters['date_debut']);
        }

        if (isset($filters['date_fin']) && $filters['date_fin'] instanceof DateTimeInterface) {
            $query->where('date_fin', '<=', $filters['date_fin']);
        }

        return $query->orderBy('date_debut')->get()->all();
    }

    public function findConflicts(
        int $salleId,
        DateTimeInterface $dateDebut,
        DateTimeInterface $dateFin,
    ): array {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', '!=', 'annulee')
            ->where('date_debut', '<', $dateFin)
            ->where('date_fin', '>', $dateDebut)
            ->orderBy('date_debut')
            ->get()
            ->all();
    }

    public function save(Reservation $reservation): Reservation
    {
        $reservation->save();

        return $reservation->refresh()->load('salle');
    }

    public function cancel(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulee';

        return $this->save($reservation);
    }
}

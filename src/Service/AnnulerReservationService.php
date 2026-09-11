<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

final class AnnulerReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
    ) {
    }

    public function annuler(int $reservationId): Reservation
    {
        $reservation = $this->reservationRepository->findById($reservationId);

        if ($reservation === null) {
            throw new ReservationIntrouvableException('La réservation demandée est introuvable.');
        }

        return $this->reservationRepository->cancel($reservation);
    }
}


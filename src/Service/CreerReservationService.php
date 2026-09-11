<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationInvalideException;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use DateTimeImmutable;

final class CreerReservationService
{
    private const STATUT_CONFIRMEE = 'confirmee';
    private const DUREE_MAXIMALE_SECONDES = 4 * 60 * 60;

    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly ReservationRepositoryInterface $reservationRepository,
    ) {
    }

    public function creer(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->salleRepository->findById($dto->salleId);

        if ($salle === null || !$salle->active) {
            throw new SalleIndisponibleException('La salle demandée est inexistante ou inactive.');
        }

        if ($dto->dateDebut >= $dto->dateFin) {
            throw new ReservationInvalideException('La date de début doit précéder la date de fin.');
        }

        $duree = $dto->dateFin->getTimestamp() - $dto->dateDebut->getTimestamp();

        if ($duree > self::DUREE_MAXIMALE_SECONDES) {
            throw new ReservationInvalideException('Une réservation ne peut pas dépasser quatre heures.');
        }

        if ($dto->dateDebut <= new DateTimeImmutable()) {
            throw new ReservationInvalideException('La réservation doit commencer dans le futur.');
        }

        if ($this->reservationRepository->findConflicts($dto->salleId, $dto->dateDebut, $dto->dateFin) !== []) {
            throw new SalleIndisponibleException('La salle est déjà réservée sur ce créneau.');
        }

        $reservation = new Reservation([
            'salle_id' => $dto->salleId,
            'responsable' => $dto->responsable,
            'email' => $dto->email,
            'motif' => $dto->motif,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'statut' => self::STATUT_CONFIRMEE,
        ]);

        return $this->reservationRepository->save($reservation);
    }
}


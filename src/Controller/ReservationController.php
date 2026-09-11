<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationInvalideException;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\View\ViewRenderer;

final class ReservationController
{
    private const VIEW_SHOW = 'reservation/show';

    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly ReservationValidator $validator,
        private readonly CreerReservationService $creerService,
        private readonly AnnulerReservationService $annulerService,
        private readonly ViewRenderer $view,
    ) {
    }

    /** @param array<string, mixed> $filters */
    public function index(array $filters = []): string
    {
        return $this->view->render('reservation/index', [
            'reservations' => $this->reservationRepository->search($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): string
    {
        return $this->view->render('reservation/create', [
            'salles' => $this->salleRepository->search(''),
            'errors' => [],
            'old' => [],
        ]);
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): string
    {
        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->createForm($data, $result->errors());
        }

        try {
            $reservation = $this->creerService->creer(CreerReservationDTO::fromArray($data));
        } catch (ReservationInvalideException|SalleIndisponibleException $exception) {
            return $this->createForm($data, ['global' => [$exception->getMessage()]]);
        }

        return $this->view->render(self::VIEW_SHOW, ['reservation' => $reservation]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservationRepository->findById($id);

        if ($reservation === null) {
            throw new ReservationIntrouvableException('Réservation introuvable.');
        }

        return $this->view->render('reservation/show', ['reservation' => $reservation]);
    }

    public function cancel(int $id): string
    {
        return $this->view->render(self::VIEW_SHOW, [
            'reservation' => $this->annulerService->annuler($id),
        ]);
    }

    /** @param array<string, mixed> $old @param array<string, list<string>> $errors */
    private function createForm(array $old, array $errors): string
    {
        return $this->view->render('reservation/create', [
            'salles' => $this->salleRepository->search(''),
            'errors' => $errors,
            'old' => $old,
        ]);
    }
}

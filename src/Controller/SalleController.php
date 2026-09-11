<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;

final class SalleController
{
    private const VIEW_SHOW = 'salle/show';
    private const SALLE_INTROUVABLE = 'Salle introuvable.';

    public function __construct(
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly SalleValidator $validator,
        private readonly ViewRenderer $view,
    ) {
    }

    public function index(?string $search = null): string
    {
        return $this->view->render('salle/index', [
            'salles' => $this->salleRepository->search($search ?? ''),
            'search' => $search ?? '',
        ]);
    }

    public function create(): string
    {
        return $this->view->render('salle/create', ['errors' => [], 'old' => []]);
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): string
    {
        $data['active'] = $data['active'] ?? false;
        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->view->render('salle/create', [
                'errors' => $result->errors(),
                'old' => $data,
            ]);
        }

        $dto = CreerSalleDTO::fromArray($data);
        $this->salleRepository->save(new Salle([
            'nom' => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type' => $dto->type,
            'active' => $dto->active,
        ]));

        return $this->index();
    }

    public function show(int $id): string
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            throw new SalleIndisponibleException(self::SALLE_INTROUVABLE);
        }

        return $this->view->render(self::VIEW_SHOW, ['salle' => $salle]);
    }

    public function edit(int $id): string
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            throw new SalleIndisponibleException(self::SALLE_INTROUVABLE);
        }

        return $this->view->render('salle/edit', ['salle' => $salle, 'errors' => []]);
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): string
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            throw new SalleIndisponibleException(self::SALLE_INTROUVABLE);
        }

        $data['active'] = $data['active'] ?? false;
        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->view->render('salle/edit', [
                'salle' => $salle,
                'errors' => $result->errors(),
                'old' => $data,
            ]);
        }

        $dto = CreerSalleDTO::fromArray($data);
        $salle->fill([
            'nom' => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type' => $dto->type,
            'active' => $dto->active,
        ]);

        return $this->view->render(self::VIEW_SHOW, [
            'salle' => $this->salleRepository->save($salle),
        ]);
    }

    public function setActive(int $id, bool $active): string
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            throw new SalleIndisponibleException(self::SALLE_INTROUVABLE);
        }

        return $this->view->render(self::VIEW_SHOW, [
            'salle' => $this->salleRepository->setActive($salle, $active),
        ]);
    }
}

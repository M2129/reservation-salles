<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

final class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function all(): array
    {
        return Salle::query()->orderBy('nom')->get()->all();
    }

    public function findById(int $id): ?Salle
    {
        return Salle::query()->find($id);
    }

    public function search(string $term): array
    {
        $term = trim($term);

        if ($term === '') {
            return $this->all();
        }

        return Salle::query()
            ->where('nom', 'like', '%' . $term . '%')
            ->orWhere('batiment', 'like', '%' . $term . '%')
            ->orderBy('nom')
            ->get()
            ->all();
    }

    public function save(Salle $salle): Salle
    {
        $salle->save();

        return $salle->refresh();
    }

    public function setActive(Salle $salle, bool $active): Salle
    {
        $salle->active = $active;

        return $this->save($salle);
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

interface SalleRepositoryInterface
{
    /** @return list<Salle> */
    public function all(): array;

    public function findById(int $id): ?Salle;

    /** @return list<Salle> */
    public function search(string $term): array;

    public function save(Salle $salle): Salle;

    public function setActive(Salle $salle, bool $active): Salle;
}

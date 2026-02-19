<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository Interface
 *
 * Define contrato para repositórios
 */
interface RepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Model;
    public function findBy(string $column, mixed $value): ?Model;
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;
    public function create(array $data): Model;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function deleteMany(array $ids): int;
    public function search(string $query, array $searchColumns): Collection;
    public function filter(array $filters): Collection;
    public function count(): int;
    public function exists(int $id): bool;
}

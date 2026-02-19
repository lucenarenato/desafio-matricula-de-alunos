<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base Repository Pattern
 *
 * Abstrai a lógica de acesso a dados, fornecendo interface
 * consistente para operações CRUD
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get record by id
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Get record by column value
     */
    public function findBy(string $column, mixed $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Get paginated results
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->delete();
    }

    /**
     * Delete multiple records
     */
    public function deleteMany(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    /**
     * Search records
     */
    public function search(string $query, array $searchColumns): Collection
    {
        $builder = $this->model->query();

        foreach ($searchColumns as $column) {
            $builder->orWhere($column, 'LIKE', "%{$query}%");
        }

        return $builder->get();
    }

    /**
     * Filter records
     */
    public function filter(array $filters): Collection
    {
        $query = $this->model->query();

        foreach ($filters as $column => $value) {
            if ($value !== null) {
                $query->where($column, $value);
            }
        }

        return $query->get();
    }

    /**
     * Count records
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if record exists
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }
}

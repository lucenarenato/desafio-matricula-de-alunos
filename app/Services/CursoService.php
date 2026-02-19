<?php

namespace App\Services;

use App\Repositories\CursoRepository;
use App\Adapters\NotificationManager;
use App\Managers\CacheManager;

/**
 * Curso Service
 *
 * Centraliza lógica de negócio para Cursos
 * Utiliza Repository abstrato e outros managers
 */
class CursoService
{
    protected CursoRepository $repository;
    protected NotificationManager $notificationManager;
    protected CacheManager $cacheManager;

    public function __construct(
        CursoRepository $repository,
        NotificationManager $notificationManager,
        CacheManager $cacheManager
    ) {
        $this->repository = $repository;
        $this->notificationManager = $notificationManager;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Get all cursos
     */
    public function getAllCursos()
    {
        return $this->cacheManager->remember('all_cursos', function () {
            return $this->repository->all();
        });
    }

    /**
     * Get curso by id
     */
    public function getCursoById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get available cursos
     */
    public function getAvailableCursos()
    {
        return $this->repository->getAvailable();
    }

    /**
     * Create new curso
     */
    public function createCurso(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Update curso
     */
    public function updateCurso(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete curso
     */
    public function deleteCurso(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Delete multiple cursos
     */
    public function deleteCursos(array $ids)
    {
        return $this->repository->deleteMany($ids);
    }

    /**
     * Search cursos
     */
    public function searchCursos(string $query)
    {
        return $this->repository->search($query, ['name', 'description']);
    }
}

<?php

namespace App\Observers;

use App\Models\Curso;
use App\Managers\CacheManager;

/**
 * Curso Observer
 *
 * Listen para eventos do modelo Curso
 * Implementa padrão Observer
 */
class CursoObserver
{
    protected CacheManager $cacheManager;

    public function __construct()
    {
        $this->cacheManager = CacheManager::getInstance();
    }

    /**
     * Handle the Curso "created" event
     */
    public function created(Curso $curso): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateCursos();

        // Log event
        \Log::info("Curso created: {$curso->name}");
    }

    /**
     * Handle the Curso "updated" event
     */
    public function updated(Curso $curso): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateCursos();

        // Log event
        \Log::info("Curso updated: {$curso->name}");
    }

    /**
     * Handle the Curso "deleted" event
     */
    public function deleted(Curso $curso): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateCursos();

        // Log event
        \Log::warning("Curso deleted: {$curso->name}");
    }

    /**
     * Handle the Curso "restored" event
     */
    public function restored(Curso $curso): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateCursos();

        // Log event
        \Log::info("Curso restored: {$curso->name}");
    }
}

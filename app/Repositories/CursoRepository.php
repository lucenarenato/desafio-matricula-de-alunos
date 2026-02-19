<?php

namespace App\Repositories;

use App\Models\Curso;

/**
 * Curso Repository
 *
 * Specializes BaseRepository for Curso model
 */
class CursoRepository extends BaseRepository
{
    public function __construct(Curso $model)
    {
        parent::__construct($model);
    }

    /**
     * Get cursos with student count
     */
    public function withStudentCount()
    {
        return $this->model->withCount('students');
    }

    /**
     * Get available cursos (with spots available)
     */
    public function getAvailable()
    {
        return $this->model->where('available_spots', '>', 0)->get();
    }

    /**
     * Get cursos by type
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Get active cursos
     */
    public function getActive()
    {
        return $this->model->where('status', 'active')->get();
    }
}

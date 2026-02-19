<?php

namespace App\Repositories;

use App\Models\Registration;

/**
 * Registration Repository
 *
 * Specializes BaseRepository for Registration model
 */
class RegistrationRepository extends BaseRepository
{
    public function __construct(Registration $model)
    {
        parent::__construct($model);
    }

    /**
     * Get registration with related data
     */
    public function withRelations()
    {
        return $this->model->with(['student', 'curso']);
    }

    /**
     * Get registrations by student
     */
    public function getByStudent(int $studentId)
    {
        return $this->model->where('student_id', $studentId)->get();
    }

    /**
     * Get registrations by curso
     */
    public function getByCurso(int $cursoId)
    {
        return $this->model->where('curso_id', $cursoId)->get();
    }

    /**
     * Check if student is enrolled in curso
     */
    public function isEnrolled(int $studentId, int $cursoId): bool
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('curso_id', $cursoId)
            ->exists();
    }

    /**
     * Get active registrations
     */
    public function getActive()
    {
        return $this->model->where('status', 'active')->get();
    }
}

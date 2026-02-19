<?php

namespace App\Repositories;

use App\Models\Student;

/**
 * Student Repository
 *
 * Specializes BaseRepository for Student model
 */
class StudentRepository extends BaseRepository
{
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    /**
     * Get students with registrations count
     */
    public function withRegistrationsCount()
    {
        return $this->model->withCount('registrations');
    }

    /**
     * Get students with their courses
     */
    public function withCourses()
    {
        return $this->model->with('courses');
    }

    /**
     * Get active students
     */
    public function getActive()
    {
        return $this->model->where('status', 'active')->get();
    }

    /**
     * Get students by email
     */
    public function getByEmail(string $email): ?Student
    {
        return $this->model->where('email', $email)->first();
    }
}

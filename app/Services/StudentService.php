<?php

namespace App\Services;

use App\Repositories\StudentRepository;
use App\Adapters\NotificationManager;
use App\Managers\CacheManager;

/**
 * Student Service
 *
 * Centraliza lógica de negócio para Students
 * Utiliza Repository abstrato e outros managers
 */
class StudentService
{
    protected StudentRepository $repository;
    protected NotificationManager $notificationManager;
    protected CacheManager $cacheManager;

    public function __construct(
        StudentRepository $repository,
        NotificationManager $notificationManager,
        CacheManager $cacheManager
    ) {
        $this->repository = $repository;
        $this->notificationManager = $notificationManager;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Get all students
     */
    public function getAllStudents()
    {
        return $this->cacheManager->remember('all_students', function () {
            return $this->repository->all();
        });
    }

    /**
     * Get student by id
     */
    public function getStudentById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get student with courses
     */
    public function getStudentWithCourses(int $id)
    {
        return $this->repository->withCourses()->find($id);
    }

    /**
     * Get active students
     */
    public function getActiveStudents()
    {
        return $this->repository->getActive();
    }

    /**
     * Create new student
     */
    public function createStudent(array $data)
    {
        $student = $this->repository->create($data);

        // Notify about new student
        $this->notificationManager->send(
            $data['email'] ?? 'admin@example.com',
            'Bem-vindo!',
            "Aluno {$data['name']} foi cadastrado com sucesso."
        );

        return $student;
    }

    /**
     * Update student
     */
    public function updateStudent(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete student
     */
    public function deleteStudent(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Delete multiple students
     */
    public function deleteStudents(array $ids)
    {
        return $this->repository->deleteMany($ids);
    }

    /**
     * Search students
     */
    public function searchStudents(string $query)
    {
        return $this->repository->search($query, ['name', 'email']);
    }
}

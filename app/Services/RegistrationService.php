<?php

namespace App\Services;

use App\Repositories\RegistrationRepository;
use App\Repositories\CursoRepository;
use App\Adapters\NotificationManager;
use App\Managers\CacheManager;
use Illuminate\Validation\ValidationException;

/**
 * Registration Service
 *
 * Centraliza lógica de negócio para Registrations
 * Gerencia regras de validação de negócio
 */
class RegistrationService
{
    protected RegistrationRepository $repository;
    protected CursoRepository $cursoRepository;
    protected NotificationManager $notificationManager;
    protected CacheManager $cacheManager;

    public function __construct(
        RegistrationRepository $repository,
        CursoRepository $cursoRepository,
        NotificationManager $notificationManager,
        CacheManager $cacheManager
    ) {
        $this->repository = $repository;
        $this->cursoRepository = $cursoRepository;
        $this->notificationManager = $notificationManager;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Get all registrations
     */
    public function getAllRegistrations()
    {
        return $this->repository->all();
    }

    /**
     * Get registration by id
     */
    public function getRegistrationById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get registrations by student
     */
    public function getRegistrationsByStudent(int $studentId)
    {
        return $this->repository->getByStudent($studentId);
    }

    /**
     * Enroll student in curso - with business rules validation
     */
    public function enrollStudent(int $studentId, int $cursoId): bool
    {
        // Check if already enrolled
        if ($this->repository->isEnrolled($studentId, $cursoId)) {
            throw ValidationException::withMessages([
                'enrollment' => 'Aluno já está matriculado neste curso.'
            ]);
        }

        // Check curso availability
        $curso = $this->cursoRepository->find($cursoId);
        if (!$curso || $curso->available_spots <= 0) {
            throw ValidationException::withMessages([
                'curso' => 'Curso não possui vagas disponíveis.'
            ]);
        }

        // Check registration deadline
        if ($curso->registration_deadline && $curso->registration_deadline < now()) {
            throw ValidationException::withMessages([
                'deadline' => 'Prazo de inscrição expirado.'
            ]);
        }

        // Create registration
        $registration = $this->repository->create([
            'student_id' => $studentId,
            'curso_id' => $cursoId,
            'enrolled_at' => now(),
        ]);

        // Update available spots
        $curso->decrement('available_spots');

        return (bool) $registration;
    }

    /**
     * Cancel enrollment
     */
    public function cancelEnrollment(int $registrationId): bool
    {
        $registration = $this->repository->find($registrationId);
        if (!$registration) {
            return false;
        }

        // Return available spot
        $curso = $registration->curso;
        $curso->increment('available_spots');

        // Delete registration
        return $registration->delete();
    }

    /**
     * Delete multiple registrations
     */
    public function deleteRegistrations(array $ids)
    {
        foreach ($ids as $id) {
            $this->cancelEnrollment($id);
        }
        return true;
    }
}

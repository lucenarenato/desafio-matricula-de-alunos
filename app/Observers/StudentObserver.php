<?php

namespace App\Observers;

use App\Models\Student;
use App\Managers\MailManager;
use App\Managers\CacheManager;

/**
 * Student Observer
 *
 * Listen para eventos do modelo Student
 * Implementa padrão Observer
 */
class StudentObserver
{
    protected MailManager $mailManager;
    protected CacheManager $cacheManager;

    public function __construct()
    {
        $this->mailManager = MailManager::getInstance();
        $this->cacheManager = CacheManager::getInstance();
    }

    /**
     * Handle the Student "created" event
     */
    public function created(Student $student): void
    {
        // Invalidate students cache
        $this->cacheManager->invalidateUsers();

        // Send welcome email
        $this->mailManager->sendWelcome(
            $student->email,
            $student->name
        );

        // Log event
        \Log::info("Student created: {$student->name}");
    }

    /**
     * Handle the Student "updated" event
     */
    public function updated(Student $student): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateUser($student->id);
        $this->cacheManager->invalidateUsers();

        // Log event
        \Log::info("Student updated: {$student->name}");
    }

    /**
     * Handle the Student "deleted" event
     */
    public function deleted(Student $student): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateUser($student->id);
        $this->cacheManager->invalidateUsers();

        // Log event
        \Log::warning("Student deleted: {$student->name}");
    }

    /**
     * Handle the Student "restored" event
     */
    public function restored(Student $student): void
    {
        // Log event
        \Log::info("Student restored: {$student->name}");
    }
}

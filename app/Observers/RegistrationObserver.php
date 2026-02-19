<?php

namespace App\Observers;

use App\Models\Registration;
use App\Managers\MailManager;
use App\Managers\CacheManager;
use App\Adapters\NotificationManager;

/**
 * Registration Observer
 *
 * Listen para eventos do modelo Registration
 * Implementa padrão Observer
 */
class RegistrationObserver
{
    protected MailManager $mailManager;
    protected CacheManager $cacheManager;
    protected NotificationManager $notificationManager;

    public function __construct()
    {
        $this->mailManager = MailManager::getInstance();
        $this->cacheManager = CacheManager::getInstance();
        $this->notificationManager = new NotificationManager();
    }

    /**
     * Handle the Registration "created" event
     */
    public function created(Registration $registration): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateUsers();

        // Get relationships
        $student = $registration->student;
        $curso = $registration->curso;

        // Send confirmation email
        $this->mailManager->sendRegistrationConfirmation(
            $student->email,
            $student->name,
            $curso->name
        );

        // Send notification via multiple channels
        $this->notificationManager->sendMultiple(
            ['email', 'database'],
            $student->email,
            'Matrícula Confirmada',
            "Você foi matriculado no curso: {$curso->name}"
        );

        // Log event
        \Log::info("Registration created: Student {$student->name} enrolled in {$curso->name}");
    }

    /**
     * Handle the Registration "deleted" event
     */
    public function deleted(Registration $registration): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateUsers();

        // Get relationships
        $student = $registration->student;
        $curso = $registration->curso;

        // Notify student
        $this->notificationManager->send(
            $student->email,
            'Matrícula Cancelada',
            "Sua matrícula no curso {$curso->name} foi cancelada."
        );

        // Log event
        \Log::info("Registration deleted: Student {$student->name} unenrolled from {$curso->name}");
    }
}

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
        $curso = $registration->curso;

        // Determine if it's a Student or User registration
        if ($registration->user_id) {
            // Registration from a common user
            $user = $registration->user;
            if (!$user) {
                return;
            }

            $email = $user->email;
            $name = $user->name;
            $context = "User {$name}";
        } else {
            // Registration from a Student
            $student = $registration->student;
            if (!$student) {
                return;
            }

            $email = $student->email;
            $name = $student->name;
            $context = "Student {$name}";
        }

        // Send confirmation email
        $this->mailManager->sendRegistrationConfirmation(
            $email,
            $name,
            $curso->name
        );

        // Send notification via multiple channels
        $this->notificationManager->sendMultiple(
            ['email', 'database'],
            $email,
            'Matrícula Confirmada',
            "Você foi matriculado no curso: {$curso->name}"
        );

        // Log event
        \Log::info("Registration created: {$context} enrolled in {$curso->name}");
    }

    /**
     * Handle the Registration "deleted" event
     */
    public function deleted(Registration $registration): void
    {
        // Invalidate cache
        $this->cacheManager->invalidateUsers();

        // Get relationships
        $curso = $registration->curso;

        // Determine if it's a Student or User registration
        if ($registration->user_id) {
            // Registration from a common user
            $user = $registration->user;
            if (!$user) {
                return;
            }

            $email = $user->email;
            $name = $user->name;
            $context = "User {$name}";
        } else {
            // Registration from a Student
            $student = $registration->student;
            if (!$student) {
                return;
            }

            $email = $student->email;
            $name = $student->name;
            $context = "Student {$name}";
        }

        // Notify about cancellation
        $this->notificationManager->send(
            $email,
            'Matrícula Cancelada',
            "Sua matrícula no curso {$curso->name} foi cancelada."
        );

        // Log event
        \Log::info("Registration deleted: {$context} unenrolled from {$curso->name}");
    }
}

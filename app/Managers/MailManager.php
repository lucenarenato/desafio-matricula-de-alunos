<?php

namespace App\Managers;

use Illuminate\Support\Facades\Mail as MailFacade;
use Illuminate\Mail\Message;

/**
 * Mail Manager - Singleton Pattern
 *
 * Centraliza gerenciamento de envio de emails
 * Garante instância única em toda aplicação
 */
class MailManager
{
    private static ?self $instance = null;

    private function __construct() {}

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialize
     */
    public function __wakeup(): void
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * Send simple email
     */
    public function send(string $to, string $subject, string $view, array $data = []): bool
    {
        try {
            MailFacade::send($view, $data, function (Message $message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send email to multiple recipients
     */
    public function sendMultiple(array $recipients, string $subject, string $view, array $data = []): bool
    {
        try {
            foreach ($recipients as $email) {
                $this->send($email, $subject, $view, $data);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send welcome email
     */
    public function sendWelcome(string $email, string $name): bool
    {
        return $this->send(
            $email,
            'Bem-vindo à nossa plataforma!',
            'emails.welcome',
            ['name' => $name]
        );
    }

    /**
     * Send registration confirmation
     */
    public function sendRegistrationConfirmation(string $email, string $studentName, string $cursoName): bool
    {
        return $this->send(
            $email,
            'Matrícula confirmada',
            'emails.registration-confirmation',
            ['student_name' => $studentName, 'curso_name' => $cursoName]
        );
    }

    /**
     * Send notification email
     */
    public function sendNotification(string $to, string $subject, string $message): bool
    {
        return $this->send($to, $subject, 'emails.notification', [
            'message' => $message
        ]);
    }

    /**
     * Check if mail is configured
     */
    public function isConfigured(): bool
    {
        return config('mail.from.address') !== null;
    }
}

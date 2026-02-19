<?php

namespace App\Adapters;

use Illuminate\Support\Facades\Mail;

/**
 * Email Notification Adapter
 *
 * Adapta notificações para formato de email
 */
class EmailNotificationAdapter implements NotificationAdapterInterface
{
    /**
     * Send email notification
     */
    public function send(string $recipient, string $subject, string $message): bool
    {
        try {
            Mail::raw($message, function ($msg) use ($recipient, $subject) {
                $msg->to($recipient)->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get adapter type
     */
    public function getType(): string
    {
        return 'email';
    }

    /**
     * Check if email is configured
     */
    public function isAvailable(): bool
    {
        return config('mail.from.address') !== null;
    }
}

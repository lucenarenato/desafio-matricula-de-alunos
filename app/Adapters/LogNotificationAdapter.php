<?php

namespace App\Adapters;

use Illuminate\Support\Facades\Log;

/**
 * Log Notification Adapter
 *
 * Adapta notificações para formato de log
 */
class LogNotificationAdapter implements NotificationAdapterInterface
{
    /**
     * Send log notification
     */
    public function send(string $recipient, string $subject, string $message): bool
    {
        try {
            Log::info("Notification to {$recipient}", [
                'subject' => $subject,
                'message' => $message,
                'timestamp' => now(),
            ]);
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
        return 'log';
    }

    /**
     * Always available
     */
    public function isAvailable(): bool
    {
        return true;
    }
}

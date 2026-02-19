<?php

namespace App\Adapters;

use Illuminate\Support\Facades\DB;

/**
 * Database Notification Adapter
 *
 * Adapta notificações para armazenamento em banco de dados
 */
class DatabaseNotificationAdapter implements NotificationAdapterInterface
{
    /**
     * Send database notification
     */
    public function send(string $recipient, string $subject, string $message): bool
    {
        try {
            DB::table('notifications')->insert([
                'recipient' => $recipient,
                'subject' => $subject,
                'message' => $message,
                'read' => false,
                'created_at' => now(),
                'updated_at' => now(),
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
        return 'database';
    }

    /**
     * Check database connectivity
     */
    public function isAvailable(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

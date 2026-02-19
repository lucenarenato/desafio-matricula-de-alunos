<?php

namespace App\Adapters;

/**
 * Notification Interface
 *
 * Define contrato para diferentes tipos de notificações
 */
interface NotificationAdapterInterface
{
    /**
     * Send notification
     */
    public function send(string $recipient, string $subject, string $message): bool;

    /**
     * Get notification type
     */
    public function getType(): string;

    /**
     * Check if adapter is available
     */
    public function isAvailable(): bool;
}

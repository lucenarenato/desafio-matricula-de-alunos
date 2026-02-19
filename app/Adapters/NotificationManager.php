<?php

namespace App\Adapters;

/**
 * Notification Manager
 *
 * Factory para criar e gerenciar diferentes tipos de adaptadores
 */
class NotificationManager
{
    private array $adapters = [];
    private ?NotificationAdapterInterface $defaultAdapter = null;

    public function __construct()
    {
        $this->registerAdapters();
    }

    /**
     * Register available adapters
     */
    protected function registerAdapters(): void
    {
        $this->register('email', new EmailNotificationAdapter());
        $this->register('log', new LogNotificationAdapter());
        $this->register('database', new DatabaseNotificationAdapter());
    }

    /**
     * Register adapter
     */
    public function register(string $name, NotificationAdapterInterface $adapter): self
    {
        $this->adapters[$name] = $adapter;
        return $this;
    }

    /**
     * Get adapter by name
     */
    public function adapter(string $name): ?NotificationAdapterInterface
    {
        return $this->adapters[$name] ?? null;
    }

    /**
     * Set default adapter
     */
    public function setDefault(string $name): self
    {
        if (isset($this->adapters[$name])) {
            $this->defaultAdapter = $this->adapters[$name];
        }
        return $this;
    }

    /**
     * Get default adapter
     */
    public function getDefault(): NotificationAdapterInterface
    {
        return $this->defaultAdapter ??= $this->adapters['email'] ?? $this->adapters['log'];
    }

    /**
     * Send notification using specific adapter
     */
    public function via(string $adapterName): NotificationAdapterInterface
    {
        return $this->adapter($adapterName) ?? $this->getDefault();
    }

    /**
     * Send notification using default adapter
     */
    public function send(string $recipient, string $subject, string $message): bool
    {
        return $this->getDefault()->send($recipient, $subject, $message);
    }

    /**
     * Send notification via multiple adapters
     */
    public function sendMultiple(array $adapters, string $recipient, string $subject, string $message): array
    {
        $results = [];
        foreach ($adapters as $adapterName) {
            $adapter = $this->adapter($adapterName);
            if ($adapter && $adapter->isAvailable()) {
                $results[$adapterName] = $adapter->send($recipient, $subject, $message);
            }
        }
        return $results;
    }

    /**
     * Get all available adapters
     */
    public function getAvailableAdapters(): array
    {
        $available = [];
        foreach ($this->adapters as $name => $adapter) {
            if ($adapter->isAvailable()) {
                $available[$name] = $adapter;
            }
        }
        return $available;
    }
}

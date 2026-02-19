<?php

namespace App\Managers;

use Illuminate\Support\Facades\Cache as CacheFacade;

/**
 * Cache Manager - Singleton Pattern
 *
 * Gerencia toda estratégia de cache da aplicação
 * Garante instância única em toda aplicação
 */
class CacheManager
{
    private static ?self $instance = null;
    private const DEFAULT_TTL = 3600; // 1 hour

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
     * Get cached value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return CacheFacade::get($key, $default);
    }

    /**
     * Set cache value
     */
    public function put(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): bool
    {
        return CacheFacade::put($key, $value, $ttl);
    }

    /**
     * Get or store value
     */
    public function remember(string $key, \Closure $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return CacheFacade::remember($key, $ttl, $callback);
    }

    /**
     * Forever cache
     */
    public function forever(string $key, mixed $value): bool
    {
        return CacheFacade::forever($key, $value);
    }

    /**
     * Remove cache entry
     */
    public function forget(string $key): bool
    {
        return CacheFacade::forget($key);
    }

    /**
     * Clear all cache
     */
    public function flush(): bool
    {
        return CacheFacade::flush();
    }

    /**
     * Check if key exists in cache
     */
    public function has(string $key): bool
    {
        return CacheFacade::has($key);
    }

    /**
     * Cache users
     */
    public function cacheUsers(array $users, int $ttl = self::DEFAULT_TTL): bool
    {
        return $this->put('users', $users, $ttl);
    }

    /**
     * Get cached users
     */
    public function getCachedUsers(): ?array
    {
        return $this->get('users');
    }

    /**
     * Cache cursos
     */
    public function cacheCursos(array $cursos, int $ttl = self::DEFAULT_TTL): bool
    {
        return $this->put('cursos', $cursos, $ttl);
    }

    /**
     * Get cached cursos
     */
    public function getCachedCursos(): ?array
    {
        return $this->get('cursos');
    }

    /**
     * Cache specific user
     */
    public function cacheUser(int $userId, object $user, int $ttl = self::DEFAULT_TTL): bool
    {
        return $this->put("user.{$userId}", $user, $ttl);
    }

    /**
     * Get cached user
     */
    public function getCachedUser(int $userId): ?object
    {
        return $this->get("user.{$userId}");
    }

    /**
     * Invalidate users cache
     */
    public function invalidateUsers(): bool
    {
        return $this->forget('users');
    }

    /**
     * Invalidate cursos cache
     */
    public function invalidateCursos(): bool
    {
        return $this->forget('cursos');
    }

    /**
     * Invalidate user cache
     */
    public function invalidateUser(int $userId): bool
    {
        return $this->forget("user.{$userId}");
    }
}

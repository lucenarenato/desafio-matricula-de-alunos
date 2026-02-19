<?php

namespace App\Managers;

use Illuminate\Support\Facades\Auth as AuthFacade;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Authentication Manager - Singleton Pattern
 *
 * Gerencia toda a lógica de autenticação centralizada
 * Garante instância única em toda aplicação
 */
class AuthManager
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
     * Login user with credentials
     */
    public function login(array $credentials): ?string
    {
        $token = JWTAuth::attempt($credentials);
        return $token ?: null;
    }

    /**
     * Login using api guard
     */
    public function apiLogin(array $credentials): ?string
    {
        $token = auth('api')->attempt($credentials);
        return $token ?: null;
    }

    /**
     * Logout current user
     */
    public function logout(): bool
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get authenticated user
     */
    public function user()
    {
        return auth()->user() ?? auth('api')->user();
    }

    /**
     * Get authenticated user from API
     */
    public function apiUser()
    {
        return auth('api')->user();
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return auth()->check() || auth('api')->check();
    }

    /**
     * Check if user has role
     */
    public function hasRole(string $role): bool
    {
        $user = $this->user();
        return $user && $user->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Refresh JWT token
     */
    public function refreshToken(): ?string
    {
        try {
            return auth('api')->refresh();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get JWT token
     */
    public function getToken(): ?string
    {
        try {
            return JWTAuth::getToken() ?? auth('api')->getToken();
        } catch (\Exception $e) {
            return null;
        }
    }
}

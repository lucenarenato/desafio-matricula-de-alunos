<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends \App\Http\Controllers\Controller
{
    /**
     * Login endpoint
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        $token = auth()->attempt($credentials);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Falha na autenticação',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Logout endpoint
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        $token = auth()->refresh();

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}

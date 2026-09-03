<?php

namespace App\Services\Client;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientAuthService
{
    /**
     * Register a new client user.
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);
    }

    /**
     * Attempt to login a client user.
     */
    public function login(array $credentials): bool
    {
        $remember = $credentials['remember'] ?? false;

        $attempt = Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => 'user',
        ], $remember);

        if ($attempt) {
            $user = Auth::user();
            if ($user->status === 'locked') {
                Auth::logout();

                return false;
            }
            request()->session()->regenerate();
        }

        return $attempt;
    }

    /**
     * Logout the current user.
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}

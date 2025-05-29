<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    // Token expiration times (in minutes)
    protected const ACCESS_TOKEN_EXPIRY = 7;      // 1 hour
    protected const REFRESH_TOKEN_EXPIRY = 30;  // 30 days

    // Token names
    protected const ACCESS_TOKEN_NAME = 'access-token';
    protected const REFRESH_TOKEN_NAME = 'refresh-token';

    // Rate limiting
    protected const MAX_LOGIN_ATTEMPTS = 5;
    protected const RATE_LIMIT_DECAY = 5;


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Uncomment for email verification
        // event(new Registered($user));

        $token = $user->createToken(
            self::ACCESS_TOKEN_NAME,
            ['*'],
            now()->addDay(self::ACCESS_TOKEN_EXPIRY)
        );

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ], 201);
    }


    public function login(Request $request)
    {
        $throttleKey = 'login.' . $request->ip();

        if ($this->isRateLimited($throttleKey)) {
            return response()->json([
                'message' => "Too many attempts. Try again in {$this->formatRateLimitTime($throttleKey)}."
            ], 429);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey);
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        RateLimiter::clear($throttleKey);
        $user->tokens()->delete(); // Revoke old tokens

        $token = $user->createToken(
            self::ACCESS_TOKEN_NAME,
            ['*'],
            now()->addDay(self::ACCESS_TOKEN_EXPIRY)
        );

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ], 200);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    // --- REFRESH TOKEN --- //
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        $currentToken = $user->currentAccessToken();

        // If token is expired, force re-login
        if (!$currentToken->expires_at || $currentToken->expires_at->isPast()) {
            return response()->json([
                'message' => 'Session expired. Please log in again.'
            ], 401);
        }

        $currentToken->delete(); // Revoke the old token

        $newToken = $user->createToken(
            self::REFRESH_TOKEN_NAME,
            ['refresh'],
            now()->addDay(self::REFRESH_TOKEN_EXPIRY)
        );

        return response()->json([
            'user' => $user,
            'token' => $newToken->plainTextToken,
            'expires_at' => $newToken->accessToken->expires_at,
        ], 200);
    }

    // Check if rate-limited
    protected function isRateLimited(string $key): bool
    {
        return RateLimiter::tooManyAttempts(
            $key,
            self::MAX_LOGIN_ATTEMPTS,
            round(self::RATE_LIMIT_DECAY * 60)
        );
    }

    // Format rate-limit time (e.g., "2 minutes")
    protected function formatRateLimitTime(string $key): string
    {
        $seconds = RateLimiter::availableIn($key);

        if ($seconds <= 60) {
            return $seconds . ' seconds';
        }

        $minutes = round($seconds / 60);
        return $minutes == 1 ? '1 minute' : "$minutes minutes";
    }
}

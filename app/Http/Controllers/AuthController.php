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
    public function register(Request $request)
    {
        $validatedAttributes = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $validatedAttributes['password'] = Hash::make($validatedAttributes['password']);
        $user = User::create($validatedAttributes);

        // Uncomment for email verification
        // event(new Registered($user));

        $token = $user->createToken('auth_token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ], 201);
    }

    public function login(Request $request)
    {
        // Check rate limit
        $key = 'login.' . $request->ip();

        if ($this->limitAttempts(5, $key, 60, 5)) {

            return response()->json([
                'message' => "Too many login attempts. Please try again in {$this->formatRateLimitTime($key)}."
            ], 429);
        }


        $validatedAttributes = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validatedAttributes['email'])->first();
        $password = Hash::check($validatedAttributes['password'], $user->password);


        if (!$user || !$password) {
            RateLimiter::hit($key);
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        $token = $user->createToken('auth_token');

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 200);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    protected function limitAttempts(int $attempts, string $key, int  $decayRate, int $multiplier)
    {

        $availableAttempts = RateLimiter::attempt($key, $attempts, function () {}, $decayRate * $multiplier);

        if (!$availableAttempts) {
            return true;
        }
    }

    protected function formatRateLimitTime(string $key): string
    {
        $seconds = RateLimiter::availableIn($key);

        if ($seconds <= 0) {
            return 'a moment';
        }

        $minutes = round($seconds / 60);

        if ($minutes < 1) {
            return 'less than a minute';
        }

        return $minutes == 1 ? '1 minute' : "{$minutes} minutes";
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        $validatedAttributes = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $validatedAttributes['password'] = bcrypt($validatedAttributes['password']);
        $user =  User::create($validatedAttributes);

        // FOR EMAIL VERIFICATION
        // event(new Registered($user));

        $token = $user->createToken($user->name);

        // Auth::login($user);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request)
    {
        $validatedAttributes = $request->validate([
            'email' => 'required|email|exist:users',
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::where('email', $validatedAttributes['email'])->first();

        if (!$user || Hash::check($validatedAttributes['email'] === $user->password)) {
            return response()->json(['message' => 'The provided credentials are not exist']);
        }

       return $user->createToken($user->name)->plainTextToken;
    }

    public function logout() {}
}

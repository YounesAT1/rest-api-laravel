<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'message' => 'You have successfully registered'
        ];

        return response($response, 201);
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'message' => 'logged in successfully'
        ];

        return response($response, 201);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ApiResponse;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the request data
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        // Check if the user exists
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        // Attempt to authenticate the user
        if (!Hash::check($credentials['password'], $user->password)) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        // Generate an access token for the authenticated user
        $token = $user->createToken($user->name, ['read'])->plainTextToken;

        // Return the access token in the response
        return ApiResponse::success([
            'token' => $token,
        ]);
    }
}

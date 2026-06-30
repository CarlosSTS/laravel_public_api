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

        // Generate an access token for the authenticated user with their abilities and set the expiration time to 5 minutes
        // $token = $user->createToken($user->name, json_decode($user->abilities), now()->plus(minutes: 5))->plainTextToken;

        // Generate an access token for the authenticated user with their abilities
        $token = $user->createToken($user->name, json_decode($user->abilities))->plainTextToken;

        // Return the access token in the response
        return ApiResponse::success([
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        // Revoke all access tokens for the user
        // $request->user()->tokens()->delete();

        // Remove the current access token from the user's tokens
        // $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        // Return a success response
        return ApiResponse::success(
            [],
            'Logged out successfully',
        );
    }
}

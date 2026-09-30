<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'user_type' => 'customer',
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'token' => $token,
        ]);
    }

    public function me(): JsonResponse
    {
        $user = request()->user();

        return response()->json(['success' => true, 'data' => [
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
        ]]);
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged out.']);
    }
}

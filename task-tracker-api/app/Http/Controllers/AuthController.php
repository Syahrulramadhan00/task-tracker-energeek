<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/login",
     * tags={"Authentication"},
     * summary="Login menggunakan email dan password",
     * description="Endpoint ini menggunakan Laravel Sanctum untuk menghasilkan Personal Access Token (PAT).",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@energeek.id"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login berhasil",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Login berhasil"),
     * @OA\Property(property="access_token", type="string", example="1|laravel_sanctum_token_here..."),
     * @OA\Property(property="token_type", type="string", example="Bearer")
     * )
     * ),
     * @OA\Response(response=401, description="Kredensial tidak valid"),
     * @OA\Response(response=422, description="Validasi error")
     * )
     */
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email is required.',
            'email.email'    => 'The email format is invalid.',
            'password.required' => 'Password is required.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Invalid credentials. Please check your email and password.',
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => 'success',
            'message'      => 'Login successful.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully. Token has been revoked.',
        ]);
    }
}

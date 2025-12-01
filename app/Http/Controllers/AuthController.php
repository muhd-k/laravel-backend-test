<?php

namespace App\Http\Controllers;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
/**
 * @OA\Info(
 *     title="Laravel API",
 *     version="1.0.0",
 *     description="API documentation for Laravel application"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */


use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User registered")
     * )
     */
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($fields);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful", @OA\JsonContent(
     *         @OA\Property(property="user", type="object"),
     *         @OA\Property(property="token", type="string")
     *     )),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return [
                'message' => 'The provided credentials are incorrect.'
            ];
        }

        return [
            'user' => JWTAuth::user(),
            'token' => $token
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Logged out successfully", @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="You are logged out.")
     *     )),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return [
            'message' => 'You are logged out.'
        ];
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Auth",
 *   description="Autenticación con JWT"
 * )
 *
 * @OA\Schema(
 *   schema="AuthTokenResponse",
 *   @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJh..."),
 *   @OA\Property(property="token_type", type="string", example="bearer"),
 *   @OA\Property(property="expires_in", type="integer", example=3600)
 * )
 *
 * @OA\Schema(
 *   schema="UserPublic",
 *   @OA\Property(property="id", type="integer", example=23),
 *   @OA\Property(property="name", type="string", example="Fernando"),
 *   @OA\Property(property="email", type="string", example="ferchex@correo.com"),
 *   @OA\Property(property="is_admin", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class AuthController extends Controller
{
    /**
     * Registrar usuario
     *
     * @OA\Post(
     *   path="/api/auth/register",
     *   tags={"Auth"},
     *   summary="Registrar un nuevo usuario",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password","password_confirmation"},
     *       @OA\Property(property="name", type="string", maxLength=255, example="Fernando"),
     *       @OA\Property(property="email", type="string", format="email", example="ferchex@correo.com"),
     *       @OA\Property(property="password", type="string", minLength=6, example="secret123"),
     *       @OA\Property(property="password_confirmation", type="string", example="secret123")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Usuario registrado",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User registered successfully"),
     *       @OA\Property(property="user", ref="#/components/schemas/UserPublic")
     *     )
     *   ),
     *   @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    /**
     * Login y obtener token JWT
     *
     * @OA\Post(
     *   path="/api/auth/login",
     *   tags={"Auth"},
     *   summary="Iniciar sesión y obtener token JWT",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="ferchex@correo.com"),
     *       @OA\Property(property="password", type="string", example="secret123")
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")),
     *   @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Perfil de usuario autenticado
     *
     * @OA\Post(
     *   path="/api/auth/profile",
     *   tags={"Auth"},
     *   summary="Obtener el perfil del usuario autenticado",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UserPublic")),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Logout (invalidar token)
     *
     * @OA\Post(
     *   path="/api/auth/logout",
     *   tags={"Auth"},
     *   summary="Cerrar sesión (invalidar token)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(@OA\Property(property="message", type="string", example="Successfully logged out"))
     *   ),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refrescar token JWT
     *
     * @OA\Post(
     *   path="/api/auth/refresh",
     *   tags={"Auth"},
     *   summary="Refrescar el token JWT",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    // ================== Helper de respuesta de token ==================

    /**
     * Estructura de respuesta del token
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Schema(
     *   schema="TokenTTLSeconds",
     *   type="integer",
     *   example=3600
     * )
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
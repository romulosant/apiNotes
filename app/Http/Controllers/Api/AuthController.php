<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Registro de usuário
     *
     * Cria uma nova conta no sistema.
     *
     * @group Autenticação
     *
     * @bodyParam name string required Nome do usuário. Example: Rômulo
     * @bodyParam email string required Email do usuário. Example: user@email.com
     * @bodyParam password string required Senha do usuário (mínimo 8 caracteres). Example: 12345678
     *
     * @response 200 {
     *   "message": "User registered successfully",
     *   "user": {
     *     "id": 1,
     *     "name": "Rômulo",
     *     "email": "user@email.com"
     *   }
     * }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
    }

    /**
     * Login do usuário
     *
     * Autentica o usuário e retorna um token JWT.
     *
     * @group Autenticação
     *
     * @bodyParam email string required Email do usuário. Example: user@email.com
     * @bodyParam password string required Senha do usuário. Example: 12345678
     *
     * @response 200 {
     *   "access_token": "jwt_token_here",
     *   "token_type": "bearer"
     * }
     *
     * @response 401 {
     *   "error": "Unauthorized"
     * }
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    /**
     * Usuário autenticado
     *
     * Retorna os dados do usuário logado.
     *
     * @group Autenticação
     * @authenticated
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "Rômulo",
     *     "email": "user@email.com"
     *   }
     * }
     */
    public function me()
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    /**
     * Logout do usuário
     *
     * Invalida o token atual do usuário.
     *
     * @group Autenticação
     * @authenticated
     *
     * @response 200 {
     *   "message": "Logged out successfully"
     * }
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

}

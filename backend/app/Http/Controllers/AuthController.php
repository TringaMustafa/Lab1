<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // (Obligative) këto janë protected, përveç login/register
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

   public function register(Request $request)
{
    $request->validate([
        'name'     => 'required|string',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    // ✅ gjithmonë user
    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'user',
    ]);

    $token = auth('api')->login($user);

    return response()->json([
        'message' => 'User registered successfully!',
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
    ], 201);
}


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // ✅ JWT attempt (nëse kredencialet janë ok, kthen token)
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'user' => auth('api')->user(),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    // ✅ obligative për route /me
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    // ✅ obligative për logout
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out']);
    }
}

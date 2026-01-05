<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // VALIDIMI
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:user,admin'
        ]);

        // Vetëm këta email mund të jenë admin
        $allowedAdmins = ['admin1@example.com', 'admin2@example.com'];

        if ($request->role === 'admin' && !in_array($request->email, $allowedAdmins)) {
            return response()->json([
                'message' => 'Ky email nuk është i autorizuar të regjistrohet si admin.'
            ], 403);
        }

        // KRIJO USERIN
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user'    => $user
        ], 201);
    }



    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // kontrollo fjalëkalimin
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // krijo token
        $token = $user->createToken('apikey')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'email' => ['required','email:rfc,dns','max:255','unique:usuario,email'],
            'password' => ['required','confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = Usuario::create([
            'nombre' => $data['first_name'],
            'apellido' => $data['last_name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Registered',
            'token' => $token,
            'user' => [
                'id' => $user->usuario_id,
                'first_name' => $user->nombre,
                'last_name' => $user->apellido,
                'email' => $user->email,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email:rfc,dns'],
            'password' => ['required','string'],
        ]);

        $user = Usuario::where('email', strtolower($data['email']))->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            // 422 keeps it in the validation-ish family; 401 is also fine
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Logged in',
            'token' => $token,
            'user' => [
                'id' => $user->usuario_id,
                'first_name' => $user->nombre,
                'last_name' => $user->apellido,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke only the current token:
        $request->user()->currentAccessToken()?->delete();

        // Or revoke all tokens:
        // $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}


<?php
namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

final class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $d = $request->validated();
        $user = Usuario::create([
            'nombre' => $d['first_name'],
            'apellido' => $d['last_name'],
            'email' => strtolower($d['email']),
            'password' => Hash::make($d['password']),
        ]);
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['message'=>'Registered','token'=>$token,'user'=>[
            'id'=>$user->usuario_id,'first_name'=>$user->nombre,'last_name'=>$user->apellido,'email'=>$user->email,
        ]], 201);
    }

    public function login(LoginRequest $request)
    {
        $d = $request->validated();
        $user = Usuario::where('email', strtolower($d['email']))->first();
        if (!$user || !Hash::check($d['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }
        $token = $user->createToken('api')->plainTextToken;
        return response()->json(['message'=>'Logged in','token'=>$token,'user'=>[
            'id'=>$user->usuario_id,'first_name'=>$user->nombre,'last_name'=>$user->apellido,'email'=>$user->email,
        ]]);
    }

    public function logout()
    {
        request()->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

<?php
namespace App\Http\Controllers;

use App\Application\Auth\LoginUser;
use App\Application\Auth\RegisterUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\JsonResponse;

final class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $useCase): JsonResponse
    {
        $out = $useCase->handle($request->validated());
        return response()->json($out, 201);
    }

    public function login(LoginRequest $request, LoginUser $useCase): JsonResponse
    {
        $out = $useCase->handle($request->validated());
        return response()->json($out, 200);
    }

    public function logout(): JsonResponse
    {
        request()->user()?->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out']);
    }
}


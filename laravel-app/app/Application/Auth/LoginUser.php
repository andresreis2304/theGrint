<?php
namespace App\Application\Auth;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class LoginUser
{
    /**
     * @param array{email:string,password:string} $data
     */
    public function handle(array $data): array
    {
        $user = Usuario::where('email', strtolower($data['email']))->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            // returns a 422 with the standard Laravel validation shape
            throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return [
            'message' => 'Logged in',
            'token'   => $token,
            'user'    => [
                'id'         => $user->usuario_id,
                'first_name' => $user->nombre,
                'last_name'  => $user->apellido,
                'email'      => $user->email,
            ],
        ];
    }
}


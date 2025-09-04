<?php
namespace App\Application\Auth;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

final class RegisterUser
{
    /**
     * @param array{first_name:string,last_name:string,email:string,password:string,password_confirmation?:string} $data
     */
    public function handle(array $data): array
    {
        $user = Usuario::create([
            'nombre'   => $data['first_name'],
            'apellido' => $data['last_name'],
            'email'    => strtolower($data['email']),
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'message' => 'Registered',
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


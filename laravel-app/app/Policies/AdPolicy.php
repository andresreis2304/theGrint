<?php
namespace App\Policies;

use App\Models\Usuario;
use App\Models\Anuncio;

class AdPolicy
{
    public function cancel(Usuario $user, Anuncio $ad): bool
    {
        return $ad->usuario_id === $user->usuario_id;
    }
}

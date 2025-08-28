<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;

class DevAuth
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->header('X-Dev-User-ID');

        if (!$id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Usuario::find($id);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->setUserResolver(fn () => $user);
        return $next($request);
    }
}

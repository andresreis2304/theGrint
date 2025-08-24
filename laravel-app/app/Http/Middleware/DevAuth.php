<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;

class DevAuth
{
    /**
     * Very simple dev-only auth:
     * Reads "X-Dev-User-ID" header, loads that Usuario and attaches it to $request->user()
     */
    public function handle(Request $request, Closure $next)
    {
        $id = $request->header('X-Dev-User-ID');

        if (!$id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Don't use findOrFail here (that would throw and become a 500)
        $user = Usuario::find($id);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Attach the model so $request->user() returns your Usuario
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}

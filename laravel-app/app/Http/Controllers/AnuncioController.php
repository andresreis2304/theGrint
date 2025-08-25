<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnuncioRequest;
use App\Http\Resources\AnuncioResource;
use App\Models\Anuncio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AnuncioController extends Controller
{
    // 1) CREATE AD (POST /api/ads)
    public function store(StoreAnuncioRequest $request)
    {
        $user = $request->user(); // set by DevAuth middleware
        if (!$user instanceof Usuario) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validated();
        // Map API "condition" (English) to DB "estado" (Spanish ENUM)
        $conditionToEstado = [
            'new' => 'nuevo',
            'used' => 'usado',
            'refurbished' => 'reacondicionado',
            'like new' => 'como nuevo',
        ];

        $estado = $conditionToEstado[$data['condition']] ?? $data['condition'];

        $ad = Anuncio::create([
            'usuario_id'  => $user->usuario_id,
            'categoria_id'=> $data['category_id'],
            'titulo' => $data['title'],
            'precio' => $data['price'],
            'estado' => $estado,   // "condition" per spec, stored in 'estado'
            'descripcion' => $data['description'] ?? null,
            'fecha_fin'   => \Illuminate\Support\Carbon::parse($data['end_date']),
            'is_canceled' => 0,
        ]);

        return (new AnuncioResource($ad))
            ->response()
            ->setStatusCode(201);
    }

    // 2) CANCEL AD (DELETE /api/ads/{id})
    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        if (!$user instanceof Usuario) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ad = Anuncio::find($id);
        if (!$ad) {
            return response()->json(['message' => 'Ad not found'], 404);
        }

        if ($ad->usuario_id !== $user->usuario_id) {
            return response()->json(['message' => 'Forbidden: not your ad'], 403);
        }

        // Soft cancel by flagging (preferred so history remains)
        $ad->is_canceled = true;
        $ad->save();

        return response()->json([
            'message' => 'Ad canceled successfully',
            'ad_id'   => $ad->anuncio_id,
        ], 200);
    }

    // 3) LIST ADS (GET /api/ads) - public
    public function index(Request $request)
    {
        // show only active, not canceled, and not expired
        $ads = Anuncio::with(['usuario', 'categoria'])
            ->where('is_canceled', false)
            ->where(function ($q) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>', now());
            })
            ->orderByDesc('created_at')
            ->get();

        return AnuncioResource::collection($ads);
    }
}

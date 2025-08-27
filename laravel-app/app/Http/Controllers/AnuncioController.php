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
    //create AD (POST /api/ads)
    public function store(StoreAnuncioRequest $request)
    {
        $user = $request->user(); // set by DevAuth middleware
        if (!$user instanceof Usuario) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validated();
        // Map API "condition" (English) to DB "estado"
        $conditionToEstado = [
            'new' => 'nuevo',
            'used' => 'usado',
            'refurbished' => 'restaurado',
            'like_new' => 'como_nuevo',
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

    //delete AD (DELETE /api/ads/{id})
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

    // 3) list ADS GET /api/ads needs to be public
    public function index(Request $request)
    {
        $data = $request->validate([
        'price_min'     => ['nullable','numeric','min:0'],
        'price_max'     => ['nullable','numeric','gte:price_min'],
        'category_id'   => ['nullable','string'],
        'estado'        => ['nullable','string'],
        'q'             => ['nullable','string'],
        'mostrar_todos' => ['nullable','boolean'],
        'per_page'      => ['nullable','integer','min:1','max:100'],
        ]);

        $estadoMap = ['new' => 'nuevo', 'nuevo' => 'nuevo', 
        'used' => 'usado', 'usado' => 'usado',
        'refurbished' => 'restaurado', 'reacondicionado' => 'restaurado', 'restaurado' => 'restaurado',
        'like_new' => 'como_nuevo', 'como_nuevo' => 'como_nuevo', 'como nuevo' => 'como_nuevo',];

        $mostrarTodos = $request->boolean('mostrar_todos');

        // show only active, not canceled, and not expired
        $ads = Anuncio::with(['usuario', 'categoria']);

        if(!$mostrarTodos){
            $ads->where('is_canceled', false)->where(function($sub){
                $sub->whereNull('fecha_fin')->orWhere('fecha_fin', '>', now());
            });
        }
        if (!empty($data['price_min'])) $ads->where('precio', '>=', (float)$data['price_min']);
        if (!empty($data['price_max'])) $ads->where('precio', '<=', (float)$data['price_max']);

        if (!empty($data['category_id'])) {
            $ids = collect(explode(',', $data['category_id']))->map(fn($v)=>(int)trim($v))->filter()->values();
            if ($ids->isNotEmpty()) $ads->whereIn('categoria_id', $ids);
        }

        if (!empty($data['estado'])) {
            $key = mb_strtolower(trim($data['estado']));
            $estado = $estadoMap[$key] ?? $key;
            $ads->where('estado', $estado);
        }
        
        if (!empty($data['q'])) {
            $term = trim($data['q']);
            $ads->where(function ($sub) use ($term) {
                $sub->where('titulo', 'like', "%{$term}%")
                    ->orWhere('descripcion', 'like', "%{$term}%");
            });
        }


        if ($mostrarTodos) {
            $ads->orderBy('precio', 'desc');          // mostrar todos -> precio desc
        } else {
            $ads->orderBy('created_at', 'asc');       // activos -> más antiguo a más nuevo
        }

        // Paginación
        $perPage = (int)($data['per_page'] ?? 10);
        $perPage = max(1, min($perPage, 100));

        $ads = $ads->paginate($perPage)->appends($data);
        return AnuncioResource::collection($ads);
    }
}

<?php
namespace App\Infrastructure\Persistence;

use App\Domain\Ads\AdRepository;
use App\Models\Anuncio;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentAdRepository implements AdRepository
{
    public function create(array $data): Anuncio
    {
        return Anuncio::create($data);
    }

    public function findById(int $id): ?Anuncio
    {
        return Anuncio::with(['usuario','categoria'])->find($id);
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $q = Anuncio::with(['usuario','categoria']);

        if (!($filters['mostrar_todos'] ?? false)) {
            $q->where('is_canceled', false)
              ->where(fn($sub) => $sub->whereNull('fecha_fin')->orWhere('fecha_fin', '>', now()));
        }
        if (!empty($filters['price_min'])) $q->where('precio', '>=', (float)$filters['price_min']);
        if (!empty($filters['price_max'])) $q->where('precio', '<=', (float)$filters['price_max']);

        if (!empty($filters['category_id'])) {
            $ids = collect(explode(',', $filters['category_id']))->map(fn($v)=>(int)trim($v))->filter()->values();
            if ($ids->isNotEmpty()) $q->whereIn('categoria_id', $ids);
        }

        if (!empty($filters['estado'])) {
            $q->where('estado', $filters['estado']); // already normalized in Request
        }

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $q->where(fn($sub) => $sub->where('titulo','like',"%{$term}%")
                                      ->orWhere('descripcion','like',"%{$term}%"));
        }

        if ($filters['mostrar_todos'] ?? false) $q->orderBy('precio','desc');
        else $q->orderBy('created_at','asc');

        $perPage = max(1, min((int)($filters['per_page'] ?? 10), 100));
        return $q->paginate($perPage)->appends($filters);
    }

    public function cancel(Anuncio $ad): void
    {
        $ad->is_canceled = true;
        $ad->save();
    }
}

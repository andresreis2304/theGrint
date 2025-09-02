<?php
namespace App\Domain\Ads;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Anuncio;

interface AdRepository
{
    public function create(array $data): Anuncio;
    public function findById(int $id): ?Anuncio;
    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator;
    public function cancel(Anuncio $ad): void;
}

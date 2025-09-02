<?php
namespace App\Http\Controllers;
use App\Application\Ads\CreateAd;
use App\Application\Ads\CancelAd;
use App\Application\Ads\ListAds;
use App\Application\Ads\DTO\CreateAdInput;
use App\Application\Ads\DTO\ListAdsFilter;
use App\Domain\Ads\Condition;
use App\Http\Requests\StoreAnuncioRequest;
use App\Http\Requests\Ads\IndexAdsRequest;
use App\Http\Resources\AnuncioResource;
use Illuminate\Http\JsonResponse;

final class AnuncioController extends Controller
{
    public function store(StoreAnuncioRequest $request, CreateAd $useCase): AnuncioResource
    {
        $u = $request->user();
        $data = $request->validated();
        $ad = $useCase->handle(new CreateAdInput(
            userId: $u->usuario_id,
            categoryId: (int)$data['category_id'],
            title: $data['title'],
            price: (float)$data['price'],
            estado: Condition::normalize($data['condition']),
            description: $data['description'] ?? null,
            endDate: \Illuminate\Support\Carbon::parse($data['end_date']),
        ));
        return new AnuncioResource($ad);
    }

    public function destroy(int $id, CancelAd $useCase): JsonResponse
    {
        $useCase->handle($id, auth()->user()->usuario_id);
        return response()->json(['message' => 'Ad canceled successfully', 'ad_id' => $id]);
    }

    public function index(IndexAdsRequest $request, ListAds $useCase)
    {
        $v = $request->validated();
        $result = $useCase->handle(new ListAdsFilter(
            priceMin: $v['price_min'] ?? null,
            priceMax: $v['price_max'] ?? null,
            categoryIdCsv: $v['category_id'] ?? null,
            estado: $v['estado'] ?? null,
            q: $v['q'] ?? null,
            mostrarTodos: (bool)($v['mostrar_todos'] ?? false),
            perPage: (int)($v['per_page'] ?? 10),
        ));
        return AnuncioResource::collection($result);
    }
}

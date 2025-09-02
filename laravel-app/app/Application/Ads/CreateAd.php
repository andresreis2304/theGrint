<?php
namespace App\Application\Ads;

use App\Application\Ads\DTO\CreateAdInput;
use App\Domain\Ads\AdRepository;
use App\Jobs\EnrichAdWithAiJob;
use Illuminate\Support\Facades\DB;

final class CreateAd
{
    public function __construct(private AdRepository $ads) {}

    public function handle(CreateAdInput $in)
    {
        $ad = DB::transaction(function () use ($in) {
            return $this->ads->create([
                'usuario_id'  => $in->userId,
                'categoria_id'=> $in->categoryId,
                'titulo'      => $in->title,
                'precio'      => $in->price,
                'estado'      => $in->estado,
                'descripcion' => $in->description,
                'fecha_fin'   => $in->endDate,
                'is_canceled' => false,
            ]);
        });

        // Queue AI enrichment (checks env internally)
        EnrichAdWithAiJob::dispatch($ad->anuncio_id);
        return $ad->fresh(['usuario','categoria']);
    }
}

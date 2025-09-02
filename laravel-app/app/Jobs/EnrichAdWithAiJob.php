<?php

namespace App\Jobs;

use App\Models\Anuncio;
use App\Services\AdAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;  
use Illuminate\Queue\InteractsWithQueue;     
use Illuminate\Queue\SerializesModels;       

class EnrichAdWithAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $adId) {}

    public function handle(AdAiService $ai): void
    {
        if (!config('services.openai.key')) return;

        $ad = Anuncio::with('categoria')->find($this->adId);
        if ($ad) {
            $ai->enrichAndSave($ad);
        }
    }
}

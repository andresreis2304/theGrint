<?php
namespace App\Services;

use App\Models\Anuncio;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdAiService
{
    public function enrich(Anuncio $ad): array
    {
        $apiKey = config('services.openai.key');
        $model  = config('services.openai.model', 'gpt-4o-mini');

        if (!$apiKey) {
            return ['valuation' => null, 'market_price' => null];
        }

        $estadoMap = [
            'nuevo' => 'new',
            'usado' => 'used',
            'restaurado' => 'refurbished',
            'reacondicionado' => 'refurbished',
            'como_nuevo' => 'like new',
        ];
        $estadoLegible = $estadoMap[strtolower((string)$ad->estado)] ?? $ad->estado;
        $categoria     = $ad->categoria?->nombre ?? 'N/A';

        $prompt = <<<PROMPT
        Eres un tasador de equipos de golf.
        Objetivo:
        1) Da una valoración breve (<=200 caracteres) del modelo/equipo.
        2) Estima precio de mercado en USD considerando el estado declarado.
        Responde SOLO en JSON válido con esta forma exacta:
        {
        "valuation": "string",
        "market_price": number
        }
        Datos:
        - Título: {$ad->titulo}
        - Categoría: {$categoria}
        - Estado: {$estadoLegible}
        - Precio publicado por el usuario: {$ad->precio}
        - Descripción: {$ad->descripcion}
        PROMPT;

        try {
            $resp = Http::timeout(12)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.2,
                    'messages' => [
                        ['role' => 'system', 'content' => 'Responde únicamente con JSON válido.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if (!$resp->successful()) {
                return ['valuation' => null, 'market_price' => null];
            }

            $content = data_get($resp->json(), 'choices.0.message.content', '{}');
            $json    = json_decode($content, true) ?: [];

            $valuation = isset($json['valuation']) ? Str::limit(trim((string)$json['valuation']), 200) : null;
            $price     = isset($json['market_price']) ? (float)$json['market_price'] : null;

            if ($price !== null && $price < 0) $price = 0.0;

            return ['valuation' => $valuation, 'market_price' => $price];
        } catch (\Throwable $e) {
            return ['valuation' => null, 'market_price' => null];
        }
    }

    public function enrichAndSave(Anuncio $ad): void
    {
        $data = $this->enrich($ad);
        $ad->forceFill([
            'ai_valuation'       => $data['valuation'],
            'ai_estimated_price' => $data['market_price'],
        ])->save();
    }
}

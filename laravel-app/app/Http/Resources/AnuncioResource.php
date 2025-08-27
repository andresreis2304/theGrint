<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnuncioResource extends JsonResource
{
    public function toArray($request): array
    {
        $conditionToEstado = [
            'new' => 'nuevo',
            'used' => 'usado',
            'refurbished' => 'restaurado',
            'like_new' => 'como_nuevo',
        ];

        $estadoToCondition = [
            'nuevo' => 'new',
            'usado'=> 'used',
            'reacondicionado' => 'refurbished',
            'como_nuevo' => 'like_new',
        ];
        
        return [
            'user' => [
                'first_name' => $this->usuario?->nombre,
                'last_name'  => $this->usuario?->apellido,
            ],
            'category' => [
                'name' => $this->categoria?->nombre,
            ],
            'ad_id' => $this->anuncio_id,
            'title' => $this->titulo,
            'price' => $this->precio,
            'condition' => $estadoToCondition[$this->estado] ?? $this->estado,
            'description' => $this->descripcion ?? '',
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'ai_valuation' => $this->ai_valuation,
            'ai_estimated_price' => $this->ai_estimated_price,
        ];
    }
}

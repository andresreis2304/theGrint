<?php
namespace App\Application\Ads\DTO;

final class ListAdsFilter
{
    public function __construct(
        public ?float $priceMin,
        public ?float $priceMax,
        public ?string $categoryIdCsv,
        public ?string $estado,
        public ?string $q,
        public bool $mostrarTodos,
        public int $perPage
    ) {}
    public function toArray(): array {
        return [
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
            'category_id' => $this->categoryIdCsv,
            'estado' => $this->estado,
            'q' => $this->q,
            'mostrar_todos' => $this->mostrarTodos,
            'per_page' => $this->perPage,
        ];
    }
}
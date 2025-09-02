<?php
namespace App\Application\Ads\DTO;

final class CreateAdInput
{
    public function __construct(
        public int $userId,
        public int $categoryId,
        public string $title,
        public float $price,
        public string $estado,      // normalized
        public ?string $description,
        public \DateTimeInterface $endDate,
    ) {}
}
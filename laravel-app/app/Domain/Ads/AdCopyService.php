<?php
namespace App\Domain\Ads;
interface AdCopyService {
    public function generate(string $title, ?string $description, string $estado, string $categoria, float $precio): array;
}

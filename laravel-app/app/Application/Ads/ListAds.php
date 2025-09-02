<?php
namespace App\Application\Ads;

use App\Application\Ads\DTO\ListAdsFilter;
use App\Domain\Ads\AdRepository;

final class ListAds
{
    public function __construct(private AdRepository $ads) {}
    public function handle(ListAdsFilter $f)
    {
        return $this->ads->paginate($f->toArray(), $f->perPage);
    }
}


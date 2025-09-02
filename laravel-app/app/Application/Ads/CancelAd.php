<?php
namespace App\Application\Ads;

use App\Domain\Ads\AdRepository;
use Illuminate\Auth\Access\AuthorizationException;

final class CancelAd
{
    public function __construct(private AdRepository $ads) {}
    public function handle(int $adId, int $currentUserId): void
    {
        $ad = $this->ads->findById($adId);
        if (!$ad) abort(404, 'Ad not found');
        if ($ad->usuario_id !== $currentUserId) {
            throw new AuthorizationException('Forbidden');
        }
        $this->ads->cancel($ad);
    }
}

<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Ads\AdRepository;
use App\Infrastructure\Persistence\EloquentAdRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdRepository::class, EloquentAdRepository::class);
    }
}

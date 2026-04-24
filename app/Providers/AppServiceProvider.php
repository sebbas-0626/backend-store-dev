<?php

namespace App\Providers;

use App\Domains\Category\Contracts\Repositories\CategoryRepositoryInterface;
use App\Domains\Category\Repositories\EloquentCategoryRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

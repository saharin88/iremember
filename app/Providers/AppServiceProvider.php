<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        \App\Contracts\QueryBuilderPreparationInterface::class => \App\Services\QueryBuilderPreparation::class,
        \App\Contracts\FamilyRelationsServiceInterface::class => \App\Services\FamilyRelationsService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

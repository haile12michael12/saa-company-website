<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register tenant-scoped services here.
    }

    public function boot(): void
    {
        // Bootstrap tenant configuration here.
    }
}
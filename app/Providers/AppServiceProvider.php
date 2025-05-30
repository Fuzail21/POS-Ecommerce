<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Purchase;

class AppServiceProvider extends ServiceProvider
{
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
         Relation::morphMap([
            'customer' => Customer::class,
            'supplier' => Supplier::class,
            'purchase' => Purchase::class,
        ]);
    }
}
